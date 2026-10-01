<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida la salida de productos e impide que la cantidad
 * supere el stock disponible (RF-08).
 */
class StoreExitRequest extends StoreMovementRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['quantity'][] = function (string $attribute, mixed $value, \Closure $fail) {
            $product = \App\Models\Product::find($this->input('product_id'));

            if ($product && (int) $value > $product->stock) {
                $fail("La cantidad solicitada ({$value}) supera el stock disponible ({$product->stock}) del producto {$product->name}.");
            }
        };

        return $rules;
    }
}
