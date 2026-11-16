<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * Recuperación de contraseña desde el login ("¿Olvidaste tu contraseña?").
 *
 * Se envía al correo del usuario un enlace firmado para que elija una
 * nueva contraseña desde la propia aplicación.
 */
class ForgotPasswordController extends Controller
{
    /**
     * Muestra el formulario para solicitar la recuperación.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Genera el token de restablecimiento y envía el enlace al correo registrado.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            // Token de un solo uso que vence en 60 minutos (config/auth.php).
            $token = Password::broker()->createToken($user);

            $user->notify(new ResetPasswordLink($token));
        }

        // Respuesta idéntica exista o no el correo (evita filtrar emails).
        return redirect()
            ->route('login')
            ->with('success', __('app.messages.password_link_sent'));
    }
}
