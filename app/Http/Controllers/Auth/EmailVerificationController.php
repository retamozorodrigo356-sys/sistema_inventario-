<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Verificación de correo electrónico: es obligatoria para acceder a
 * cualquier vista del sistema.
 *
 * El usuario llega aquí desde el enlace firmado del correo, desde el
 * botón "Reenviar" o, si su correo sigue sin verificar, desde el aviso
 * de verification.notice (bloquea todo el resto del sistema).
 */
class EmailVerificationController extends Controller
{
    /**
     * Aviso de bloqueo: pide verificar el correo antes de usar el sistema.
     */
    public function notice(): View
    {
        return view('auth.verify-email-notice');
    }

    /**
     * Formulario público para pedir el enlace de verificación por correo.
     */
    public function create(): View
    {
        return view('auth.verify-email-request');
    }

    /**
     * Envía el enlace de verificación al correo indicado, si existe y
     * todavía no está verificado.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        // Respuesta idéntica exista o no el correo (evita filtrar emails).
        return redirect()
            ->route('verification.request')
            ->with('success', __('app.messages.verify_link_sent'));
    }

    /**
     * Confirma el correo cuando el usuario abre el enlace firmado.
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! hash_equals((string) $request->route('id'), (string) $user->getKey())
            || ! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('dashboard')
                ->with('success', __('app.messages.email_already_verified'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()
            ->route('dashboard')
            ->with('success', __('app.messages.email_verified'));
    }

    /**
     * Reenvía el correo de verificación al usuario autenticado.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('dashboard')
                ->with('success', __('app.messages.email_is_verified'));
        }

        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice')
            ->with('success', __('app.messages.verify_resent'));
    }
}
