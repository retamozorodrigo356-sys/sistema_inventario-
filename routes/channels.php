<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Canales de difusión
|--------------------------------------------------------------------------
|
| El canal "inventory" es privado: solo los usuarios con acceso al dashboard
| reciben los cambios del inventario en tiempo real.
|
*/

Broadcast::channel('inventory', function (User $user) {
    return $user->hasPermission('dashboard.view');
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
