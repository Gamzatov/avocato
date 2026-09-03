<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'weight' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'badge' => ['nullable', Rule::in(array_keys(Product::badgeOptions()))],
            'is_active' => ['nullable', 'boolean'],
            'cities' => ['required', 'array'],
            'cities.*.price' => ['nullable', 'numeric', 'min:0'],
            'cities.*.is_active' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'options.*.name' => ['nullable', 'string', 'max:255'],
            'options.*.price' => ['nullable', 'numeric', 'min:0'],
            'options.*.weight' => ['nullable', 'string', 'max:100'],
            'options.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'options.*.is_active' => ['nullable', 'boolean'],
        ];
    }
}
