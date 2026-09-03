<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->with(['category', 'cities', 'options'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('weight', 'like', "%{$search}%")
                        ->orWhere('badge', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('options', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product,
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $productData = Arr::except($data, ['cities', 'options']);

        DB::transaction(function () use ($request, $data, $productData) {
            if ($request->hasFile('image')) {
                $productData['image'] = $request->file('image')->store('products', 'public');
            }

            $productData['slug'] = $this->generateUniqueSlug($productData['name']);
            $productData['is_active'] = $request->boolean('is_active');
            $productData['badge'] = ($productData['badge'] ?? null) ?: null;
            $productData['sort_order'] = $productData['sort_order'] ?? 0;

            $product = Product::create($productData);

            $this->syncCities($product, $data['cities'] ?? []);
            $this->syncOptions($product, $data['options'] ?? []);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Продукт додано.');
    }

    public function edit(Product $product): View
    {
        $product->load(['cities', 'options']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $productData = Arr::except($data, ['cities', 'options']);

        DB::transaction(function () use ($request, $data, $product, $productData) {
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $productData['image'] = $request->file('image')->store('products', 'public');
            }

            $productData['is_active'] = $request->boolean('is_active');
            $productData['badge'] = ($productData['badge'] ?? null) ?: null;
            $productData['sort_order'] = $productData['sort_order'] ?? 0;

            $product->update($productData);

            $this->syncCities($product, $data['cities'] ?? []);
            $this->syncOptions($product, $data['options'] ?? []);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Продукт оновлено.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        DB::transaction(function () use ($product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Продукт видалено.');
    }

    private function syncCities(Product $product, array $cities): void
    {
        $sync = [];

        foreach ($cities as $cityId => $cityData) {
            if (! isset($cityData['price']) || $cityData['price'] === '') {
                continue;
            }

            $sync[$cityId] = [
                'price' => $cityData['price'],
                'is_active' => (bool) ($cityData['is_active'] ?? false),
            ];
        }

        $product->cities()->sync($sync);
    }

    private function syncOptions(Product $product, array $options): void
    {
        $keptOptionIds = [];

        foreach ($options as $index => $optionData) {
            $optionId = isset($optionData['id']) ? (int) $optionData['id'] : null;
            $name = trim((string) ($optionData['name'] ?? ''));
            $price = $optionData['price'] ?? null;

            if ($optionId && (bool) ($optionData['delete'] ?? false)) {
                $product->options()->whereKey($optionId)->delete();

                continue;
            }

            if ($name === '' || $price === null || $price === '') {
                continue;
            }

            $option = $optionId
                ? $product->options()->whereKey($optionId)->first()
                : $product->options()->make();

            if (! $option) {
                continue;
            }

            $option->fill([
                'name' => $name,
                'price' => $price,
                'weight' => $optionData['weight'] ?? null,
                'sort_order' => $optionData['sort_order'] ?? $index,
                'is_active' => (bool) ($optionData['is_active'] ?? false),
            ]);

            $option->save();
            $keptOptionIds[] = $option->id;
        }

        $product->options()
            ->when($keptOptionIds !== [], fn ($query) => $query->whereNotIn('id', $keptOptionIds))
            ->delete();
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'product';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
