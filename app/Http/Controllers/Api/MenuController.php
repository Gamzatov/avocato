<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function cities(): JsonResponse
    {
        return response()->json(
            City::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'phone', 'secondary_phone'])
                ->map(fn (City $city): array => $this->cityPayload($city))
        );
    }

    public function categories(): JsonResponse
    {
        return response()->json(
            Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'icon', 'image'])
        );
    }

    public function menu(City $city): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['products' => function ($query) use ($city) {
                $query
                    ->where('products.is_active', true)
                    ->whereHas('cities', function ($cityQuery) use ($city) {
                        $cityQuery
                            ->where('cities.id', $city->id)
                            ->where('city_product.is_active', true);
                    })
                    ->with(['cities' => function ($cityQuery) use ($city) {
                        $cityQuery->where('cities.id', $city->id);
                    }])
                    ->with(['options' => function ($optionQuery) {
                        $optionQuery->where('is_active', true);
                    }])
                    ->orderBy('sort_order');
            }])
            ->get();

        $payload = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'image' => $category->image
                    ? asset('storage/'.$category->image)
                    : null,
                'products' => $category->products->map(function ($product) {
                    $city = $product->cities->first();

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'description' => $product->description,
                        'weight' => $product->weight,
                        'image' => $product->image
                            ? asset('storage/'.$product->image)
                            : null,
                        'price' => $city?->pivot?->price,
                        'options' => $product->options->map(fn ($option): array => [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price' => $option->price,
                            'weight' => $option->weight,
                        ])->values(),
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'city' => $this->cityPayload($city),
            'categories' => $payload,
        ]);
    }

    /**
     * @return array{id: int, name: string, slug: string, phone: ?string, phones: list<string>}
     */
    private function cityPayload(City $city): array
    {
        return [
            'id' => $city->id,
            'name' => $city->name,
            'slug' => $city->slug,
            'phone' => $city->phone,
            'phones' => $city->phones(),
        ];
    }
}
