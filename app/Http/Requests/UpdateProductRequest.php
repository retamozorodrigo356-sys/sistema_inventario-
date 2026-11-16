<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('productos', 'code')->ignore($productId)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categorias,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_min' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => __('validation.attributes.code'),
            'category_id' => __('validation.attributes.category_id'),
            'price' => __('validation.attributes.price'),
            'stock' => __('validation.attributes.stock'),
            'stock_min' => __('validation.attributes.stock_min'),
        ];
    }
}
