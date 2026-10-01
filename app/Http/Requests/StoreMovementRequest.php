<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:productos,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date'],
            'observation' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'producto',
            'quantity' => 'cantidad',
            'date' => 'fecha',
            'observation' => 'observación',
        ];
    }
}
