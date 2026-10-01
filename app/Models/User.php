<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    /**
     * Verifica si el usuario posee una habilidad (RF / RNF-04).
     */
    public function hasPermission(?string $ability): bool
    {
        $slug = $this->role?->slug;

        if (! $slug) {
            return false;
        }

        $abilities = config("permissions.roles.{$slug}.abilities", []);

        return in_array('*', $abilities, true) || in_array($ability, $abilities, true);
    }
}
