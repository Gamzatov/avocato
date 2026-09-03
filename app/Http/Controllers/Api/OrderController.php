<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data): Order {
            $city = City::query()
                ->where('slug', $data['city_slug'])
                ->where('is_active', true)
                ->firstOrFail();

            $items = collect($data['items']);
            $productIds = $items->pluck('product_id')->unique()->values();
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->where('is_active', true)
                ->whereHas('cities', function ($query) use ($city): void {
                    $query
                        ->where('cities.id', $city->id)
                        ->where('city_product.is_active', true);
                })
                ->with(['cities' => function ($query) use ($city): void {
                    $query
                        ->where('cities.id', $city->id)
                        ->where('city_product.is_active', true);
                }])
                ->get()
                ->keyBy('id');

            $options = ProductOption::query()
                ->whereIn('id', $items->pluck('product_option_id')->filter())
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            if ($products->count() !== $productIds->count()) {
                throw ValidationException::withMessages([
                    'items' => 'У кошику є товари, недоступні для обраного міста.',
                ]);
            }

            $order = Order::create([
                'city_id' => $city->id,
                'customer_name' => $data['customer']['name'],
                'customer_phone' => $data['customer']['phone'],
                'customer_address' => $data['customer']['address'] ?? null,
                'customer_comment' => $data['customer']['comment'] ?? null,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                $option = isset($item['product_option_id'])
                    ? $options->get($item['product_option_id'])
                    : null;
                $quantity = (int) $item['qty'];
                $unitPrice = (float) ($option?->price ?? $product->cities->first()->pivot->price);
                $lineTotal = $unitPrice * $quantity;
                $total += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_option_id' => $option?->id,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'product_option_name' => $option?->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ]);
            }

            $order->update(['total' => $total]);

            return $order->load(['city', 'items']);
        });

        return response()->json([
            'message' => 'Замовлення створено.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'city' => $order->city->name,
                'total' => $order->total,
                'items_count' => $order->items->sum('quantity'),
            ],
        ], 201);
    }
}
