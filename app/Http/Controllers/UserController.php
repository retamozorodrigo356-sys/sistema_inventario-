<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * CRUD de usuarios y asignación de roles (RF-02, RNF-04).
 */
class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')->orderBy('name')->paginate(10);

        return view('usuarios.index', compact('users'));
    }

    public function create(): View
    {
        return view('usuarios.create', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'role_id' => ['required', 'exists:roles,id'],
        ], [], [
            'name' => 'nombre',
            'username' => 'usuario',
            'email' => 'correo',
            'password' => 'contraseña',
            'role_id' => 'rol',
        ]);

        // La contraseña se almacena con hash (RNF-03).
        User::create($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(User $user): View
    {
        return view('usuarios.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,'.$user->id],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'role_id' => ['required', 'exists:roles,id'],
        ], [], [
            'name' => 'nombre',
            'username' => 'usuario',
            'email' => 'correo',
            'password' => 'contraseña',
            'role_id' => 'rol',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puede eliminar su propio usuario.');
        }

        try {
            $user->delete();

            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (QueryException) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'No se puede eliminar el usuario porque tiene movimientos registrados.');
        }
    }
}
