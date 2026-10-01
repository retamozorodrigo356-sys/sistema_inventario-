<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege rutas según el rol/permiso del usuario.
 * Uso: ->middleware('permission:products.create')
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasPermission($ability)) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No tiene permisos para realizar esta acción.');
        }

        return $next($request);
    }
}
