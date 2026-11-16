<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Registro público de nuevos usuarios ("¿No tenés cuenta? Registrate").
 *
 * El rol se asigna automáticamente "consultor" (solo lectura, RNF-04): es
 * el de menores privilegios. Un administrador puede ascenderlo después
 * desde Usuarios → Editar.
 */
class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Crea la cuenta, envía el correo de verificación e inicia sesión.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        // La contraseña se almacena con hash (RNF-03); el rol por defecto
        // es "consultor" (solo lectura).
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => Role::where('slug', 'consultor')->firstOrFail()->id,
        ]);

        // Enlace de verificación de correo (la app no bloquea el acceso).
        $user->sendEmailVerificationNotification();

        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('success', __('app.messages.account_created'));
    }
}
