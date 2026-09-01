<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['category', 'cities'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
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

        DB::transaction(function () use ($request, $data) {
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['slug'] = $this->generateUniqueSlug($data['name']);
            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $product = Product::create($data);

            $this->syncCities($product, $request->input('cities', []));
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Продукт додано.');
    }

    public function edit(Product $product): View
    {
        $product->load('cities');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $product) {
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $product->update($data);

            $this->syncCities($product, $request->input('cities', []));
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
