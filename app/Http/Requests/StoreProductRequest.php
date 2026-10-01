<?php

namespace App\Http\Requests;

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
            'code' => ['required', 'string', 'max:50', 'unique:productos,code'],
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
            'code' => 'código',
            'category_id' => 'categoría',
            'price' => 'precio',
            'stock' => 'stock actual',
            'stock_min' => 'stock mínimo',
        ];
    }
}
