<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'quantity' => (int) $this->quantity,
            'date' => $this->date ? (is_string($this->date) ? $this->date : $this->date->format('Y-m-d')) : null,
            'observation' => $this->observation,
            'product' => $this->product ? [
                'id' => $this->product->id,
                'code' => $this->product->code,
                'name' => $this->product->name,
                'current_stock' => $this->product->stock,
            ] : null,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

