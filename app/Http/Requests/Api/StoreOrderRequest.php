<?php

namespace App\Http\Requests\Api;

use App\Models\City;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'city_slug' => [
                'required',
                'string',
                Rule::exists('cities', 'slug')->where('is_active', true),
            ],
            'customer' => ['required', 'array:name,phone,address,comment'],
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.phone' => ['required', 'string', 'max:50'],
            'customer.address' => ['nullable', 'string', 'max:255'],
            'customer.comment' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'array:product_id,qty'],
            'items.*.product_id' => ['required', 'integer', 'distinct', Rule::exists('products', 'id')->where('is_active', true)],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['city_slug', 'items', 'items.*.product_id'])) {
                    return;
                }

                $city = City::query()->where('slug', (string) $this->input('city_slug'))->first();

                if (! $city) {
                    return;
                }

                $productIds = collect($this->input('items', []))
                    ->pluck('product_id')
                    ->map(fn (mixed $productId): int => (int) $productId)
                    ->all();

                $availableProductIds = Product::query()
                    ->whereIn('id', $productIds)
                    ->where('is_active', true)
                    ->whereHas('cities', function ($query) use ($city): void {
                        $query
                            ->where('cities.id', $city->id)
                            ->where('city_product.is_active', true);
                    })
                    ->pluck('id')
                    ->all();

                if (count($availableProductIds) !== count($productIds)) {
                    $validator->errors()->add('items', 'У кошику є товари, недоступні для обраного міста.');
                }
            },
        ];
    }
}
