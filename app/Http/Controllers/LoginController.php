<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión (RF-01).
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Autentica al usuario con correo o nombre de usuario.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'login' => 'usuario o correo',
        ]);

        $field = str_contains($credentials['login'], '@') ? 'email' : 'username';

        $remember = $request->boolean('remember');

        if (! Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $remember)) {
            throw ValidationException::withMessages([
                'login' => 'Las credenciales proporcionadas no son correctas.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Bienvenido(a), '.Auth::user()->name.'.');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
