<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    public const ENTRADA = 'entrada';
    public const SALIDA = 'salida';

    protected $table = 'movimientos';

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'date',
        'observation',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function isEntrada(): bool
    {
        return $this->type === self::ENTRADA;
    }
}
