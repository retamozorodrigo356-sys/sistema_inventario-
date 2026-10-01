<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'price',
        'stock',
        'stock_min',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'stock_min' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /** Indica si el stock actual es igual o menor al stock mínimo. */
    public function hasLowStock(): bool
    {
        return $this->stock <= $this->stock_min;
    }
}
