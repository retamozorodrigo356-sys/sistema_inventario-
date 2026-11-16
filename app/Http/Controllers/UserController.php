<?php

namespace App\Http\Controllers;

use App\Models\Movement;
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

    /**
     * Perfil del usuario: datos, rol y actividad reciente.
     *
     * Todo usuario puede ver su propio perfil; ver el de terceros exige
     * el permiso indicado (RNF-04).
     */
    public function show(User $user): View|RedirectResponse
    {
        if ($denied = $this->denyUnlessAllowed($user, 'users.view')) {
            return $denied;
        }

        $user->load('role');

        return view('usuarios.show', [
            'user' => $user,
            'totalMovements' => $user->movements()->count(),
            'entradas' => $user->movements()->ofType(Movement::ENTRADA)->count(),
            'salidas' => $user->movements()->ofType(Movement::SALIDA)->count(),
            'movements' => $user->movements()->with('product')->latest('id')->take(5)->get(),
        ]);
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
        ]);

        // La contraseña se almacena con hash (RNF-03).
        $user = User::create($data);

        // El nuevo usuario recibe de inmediato el enlace de verificación de correo.
        $user->sendEmailVerificationNotification();

        return redirect()->route('usuarios.index')->with('success', __('app.messages.user_created'));
    }

    public function edit(User $user): View|RedirectResponse
    {
        if ($denied = $this->denyUnlessAllowed($user, 'users.update')) {
            return $denied;
        }

        return view('usuarios.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    /**
     * Formulario para cambiar la contraseña de un usuario.
     */
    public function editPassword(User $user): View|RedirectResponse
    {
        if ($denied = $this->denyUnlessAllowed($user, 'users.update')) {
            return $denied;
        }

        return view('usuarios.password', ['user' => $user]);
    }

    /**
     * Guarda la nueva contraseña del usuario (hash, RNF-03).
     */
    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        if ($denied = $this->denyUnlessAllowed($user, 'users.update')) {
            return $denied;
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user->update(['password' => $data['password']]);

        return redirect()
            ->route('usuarios.show', $user)
            ->with('success', __('app.messages.user_password_updated'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($denied = $this->denyUnlessAllowed($user, 'users.update')) {
            return $denied;
        }

        // Quien solo edita su propio perfil no puede alterar su rol (RF-02).
        $selfService = $user->id === auth()->id() && ! auth()->user()->hasPermission('users.update');

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,'.$user->id],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ];

        if (! $selfService) {
            $rules['role_id'] = ['required', 'exists:roles,id'];
        }

        $data = $request->validate($rules);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($selfService) {
            $data['role_id'] = $user->role_id;
        }

        $user->update($data);

        if ($user->id === auth()->id()) {
            return redirect()->route('usuarios.show', $user)->with('success', __('app.messages.profile_updated'));
        }

        return redirect()->route('usuarios.index')->with('success', __('app.messages.user_updated'));
    }

    /**
     * El perfil propio es siempre accesible; el de otros usuarios requiere
     * el permiso indicado y, sin él, se redirige al dashboard (RNF-04).
     */
    private function denyUnlessAllowed(User $user, string $ability): ?RedirectResponse
    {
        $actor = auth()->user();

        if ($user->id !== $actor->id && ! $actor->hasPermission($ability)) {
            return redirect()
                ->route('dashboard')
                ->with('error', __('app.messages.no_permission'));
        }

        return null;
    }

    public function destroy(User $user): RedirectResponse
    {
        $isSelf = $user->id === auth()->id();

        // Cualquier usuario puede eliminar su propia cuenta; la de otros
        // requiere el permiso users.delete (RNF-04).
        if (! $isSelf && ! auth()->user()->hasPermission('users.delete')) {
            return redirect()
                ->route('dashboard')
                ->with('error', __('app.messages.no_permission'));
        }

        try {
            $user->delete();
        } catch (QueryException) {
            $error = __('app.messages.user_has_movements');

            return $isSelf
                ? redirect()->route('usuarios.show', $user)->with('error', $error)
                : redirect()->route('usuarios.index')->with('error', $error);
        }

        if ($isSelf) {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('success', __('app.messages.self_deleted'));
        }

        return redirect()->route('usuarios.index')->with('success', __('app.messages.user_deleted'));
    }
}
