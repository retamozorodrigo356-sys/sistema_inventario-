<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

/**
 * Restablecimiento de contraseña mediante el enlace enviado por correo
 * ("¿Olvidaste tu contraseña?" → correo → formulario de nueva contraseña).
 *
 * El token se valida y consume con el password broker, que usa la tabla
 * password_reset_tokens (un solo uso, expira en 60 minutos).
 */
class ResetPasswordController extends Controller
{
    /**
     * Muestra el formulario para elegir la nueva contraseña.
     */
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * Valida el enlace y guarda la nueva contraseña hasheada (RNF-03).
     */
    public function store(Request $request, string $token): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ]);

        $status = Password::broker()->reset(
            [
                'token' => $token,
                'email' => $data['email'],
                'password' => $data['password'],
                // La regla "confirmed" ya validó que exista y coincida.
                'password_confirmation' => (string) $request->input('password_confirmation'),
            ],
            function (User $user, string $password): void {
                // El cast "hashed" del modelo guarda el hash, nunca el texto plano.
                $user->update(['password' => $password]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return redirect()
                ->route('password.forgot')
                ->withErrors(['email' => __('app.messages.password_reset_invalid')]);
        }

        return redirect()
            ->route('login')
            ->with('success', __('app.messages.password_reset_success'));
    }
}
