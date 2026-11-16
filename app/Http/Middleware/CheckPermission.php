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
            return $request->expectsJson()
                ? response()->json(['message' => __('app.messages.unauthenticated')], 401)
                : redirect()->route('login');
        }

        if (! $user->hasPermission($ability)) {
            return $request->expectsJson()
                ? response()->json(['message' => __('app.messages.no_permission')], 403)
                : redirect()
                    ->route('dashboard')
                    ->with('error', __('app.messages.no_permission'));
        }

        return $next($request);
    }
}
