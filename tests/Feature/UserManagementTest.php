<?php

namespace Tests\Feature;

use App\Models\Movement;
use App\Models\Role;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-02: gestionar usuarios y asignar roles.
 * RNF-03: contraseñas con hash.
 */
class UserManagementTest extends TestCase
{
    use BuildsInventory, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_users_list_is_displayed(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->get(route('usuarios.index'))
            ->assertOk()
            ->assertSee($admin->email)
            ->assertSee('Administrador');
    }

    public function test_user_profile_is_displayed(): void
    {
        $admin = $this->userWithRole('admin');
        $user = $this->userWithRole('consultor');

        $this->actingAs($admin)
            ->get(route('usuarios.show', $user))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee($user->username)
            ->assertSee($user->email)
            ->assertSee('Consultor')
            ->assertDontSee('Eliminar mi cuenta');   // la autoeliminación solo aparece en el perfil propio
    }

    public function test_user_can_view_and_edit_his_own_profile_without_permission(): void
    {
        $user = $this->userWithRole('consultor');

        $this->actingAs($user)
            ->get(route('usuarios.show', $user))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('Eliminar mi cuenta');   // botón de autoeliminación visible

        $this->actingAs($user)
            ->get(route('usuarios.edit', $user))
            ->assertOk()
            ->assertSee($user->username);
    }

    public function test_user_profile_of_others_requires_users_permission(): void
    {
        $user = $this->userWithRole('consultor');
        $other = $this->userWithRole('encargado');

        $this->actingAs($user)
            ->get(route('usuarios.show', $other))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->get(route('usuarios.edit', $other))
            ->assertRedirect(route('dashboard'));
    }

    public function test_user_can_update_his_own_profile_but_not_his_role(): void
    {
        $user = $this->userWithRole('consultor');
        $adminRole = Role::where('slug', 'admin')->first();

        $this->actingAs($user)->put(route('usuarios.update', $user), [
            'name' => 'Nombre Propio Editado',
            'username' => $user->username,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
            'role_id' => $adminRole->id,   // intento de autoascenso ignorado
        ])->assertRedirect(route('usuarios.show', $user));

        $user->refresh();
        $this->assertSame('Nombre Propio Editado', $user->name);
        $this->assertSame('consultor', $user->role->slug);
    }

    public function test_user_password_can_be_changed_from_profile(): void
    {
        $admin = $this->userWithRole('admin');
        $user = $this->userWithRole('consultor');

        $this->actingAs($admin)
            ->get(route('usuarios.password', $user))
            ->assertOk()
            ->assertSee($user->name);

        $this->actingAs($admin)
            ->put(route('usuarios.password.update', $user), [
                'password' => 'nuevaclave2',
                'password_confirmation' => 'nuevaclave2',
            ])
            ->assertRedirect(route('usuarios.show', $user));

        $this->assertTrue(Hash::check('nuevaclave2', $user->fresh()->password));
    }

    public function test_password_change_requires_matching_and_valid_confirmation(): void
    {
        $admin = $this->userWithRole('admin');
        $user = $this->userWithRole('consultor');

        $this->actingAs($admin)
            ->put(route('usuarios.password.update', $user), [
                'password' => '123',
                'password_confirmation' => '456',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password)); // sin cambios
    }

    public function test_user_can_change_his_own_password_without_permission(): void
    {
        $user = $this->userWithRole('consultor');

        $this->actingAs($user)
            ->get(route('usuarios.password', $user))
            ->assertOk()
            ->assertSee($user->name);

        $this->actingAs($user)
            ->put(route('usuarios.password.update', $user), [
                'password' => 'nuevaclave3',
                'password_confirmation' => 'nuevaclave3',
            ])
            ->assertRedirect(route('usuarios.show', $user));

        $this->assertTrue(Hash::check('nuevaclave3', $user->fresh()->password));
    }

    public function test_password_change_of_others_requires_users_update_permission(): void
    {
        $user = $this->userWithRole('consultor');
        $other = $this->userWithRole('encargado');

        $this->actingAs($user)
            ->get(route('usuarios.password', $other))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->put(route('usuarios.password.update', $other), [
                'password' => 'nuevaclave3',
                'password_confirmation' => 'nuevaclave3',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(Hash::check('password', $other->fresh()->password));
    }

    public function test_user_can_be_created_with_role_and_hashed_password(): void
    {
        $role = Role::where('slug', 'consultor')->first();

        $response = $this->actingAs($this->userWithRole('admin'))->post(route('usuarios.store'), [
            'name' => 'Nuevo Usuario',
            'username' => 'nuevousuario',
            'email' => 'nuevo@inventario.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'role_id' => $role->id,
        ]);

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success');

        $user = User::where('email', 'nuevo@inventario.test')->firstOrFail();

        $this->assertSame('nuevousuario', $user->username);
        $this->assertSame('consultor', $user->role->slug);
        $this->assertNotSame('secreto123', $user->password);   // nunca en texto plano
        $this->assertTrue(Hash::check('secreto123', $user->password));
    }

    public function test_creating_a_user_sends_the_verification_email(): void
    {
        Notification::fake();
        $role = Role::where('slug', 'consultor')->first();

        $this->actingAs($this->userWithRole('admin'))->post(route('usuarios.store'), [
            'name' => 'Nuevo Usuario',
            'username' => 'nuevousuario2',
            'email' => 'nuevo2@inventario.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'role_id' => $role->id,
        ])->assertRedirect(route('usuarios.index'));

        $user = User::where('email', 'nuevo2@inventario.test')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
        $this->assertNull($user->email_verified_at); // queda sin verificar hasta abrir el enlace
    }

    public function test_user_requires_valid_data(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->post(route('usuarios.store'), [
                'name' => '',
                'username' => '',
                'email' => 'correo-invalido',
                'password' => '123',
                'password_confirmation' => '456',
                'role_id' => 999999,
            ])
            ->assertSessionHasErrors(['name', 'username', 'email', 'password', 'role_id']);

        $this->assertDatabaseCount('users', 1); // solo el admin creado en el setup
    }

    public function test_user_email_must_be_unique(): void
    {
        $existing = $this->userWithRole('consultor');

        $this->actingAs($this->userWithRole('admin'))
            ->post(route('usuarios.store'), [
                'name' => 'Otro',
                'username' => 'otrouser',
                'email' => $existing->email,
                'password' => 'secreto123',
                'password_confirmation' => 'secreto123',
                'role_id' => 1,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_user_can_be_updated_and_role_changed(): void
    {
        $user = $this->userWithRole('consultor');
        $adminRole = Role::where('slug', 'admin')->first();

        $this->actingAs($this->userWithRole('admin'))->put(route('usuarios.update', $user), [
            'name' => 'Nombre Editado',
            'username' => $user->username,
            'email' => $user->email,
            'password' => '',                    // no cambiar contraseña
            'password_confirmation' => '',
            'role_id' => $adminRole->id,
        ])->assertRedirect(route('usuarios.index'));

        $user->refresh();
        $this->assertSame('Nombre Editado', $user->name);
        $this->assertSame('admin', $user->role->slug);
        $this->assertTrue(Hash::check('password', $user->password)); // contraseña original intacta
    }

    public function test_user_password_can_be_changed(): void
    {
        $user = $this->userWithRole('consultor');
        $before = $user->password;

        $this->actingAs($this->userWithRole('admin'))->put(route('usuarios.update', $user), [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'nuevaclave1',
            'password_confirmation' => 'nuevaclave1',
            'role_id' => $user->role_id,
        ])->assertRedirect(route('usuarios.index'));

        $this->assertNotSame($before, $user->fresh()->password);
        $this->assertTrue(Hash::check('nuevaclave1', $user->fresh()->password));
    }

    public function test_user_can_be_deleted(): void
    {
        $user = $this->userWithRole('consultor');

        $this->actingAs($this->userWithRole('admin'))
            ->delete(route('usuarios.destroy', $user))
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_can_delete_his_own_account(): void
    {
        $user = $this->userWithRole('consultor');

        $this->actingAs($user)
            ->delete(route('usuarios.destroy', $user))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_deleting_another_user_requires_users_delete_permission(): void
    {
        $user = $this->userWithRole('consultor');
        $other = $this->userWithRole('encargado');

        $this->actingAs($user)
            ->delete(route('usuarios.destroy', $other))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $other->id]);
    }

    public function test_user_with_movements_cannot_delete_his_own_account(): void
    {
        $user = $this->userWithRole('encargado');
        $this->makeMovement($this->makeProduct(), $user, Movement::ENTRADA, 1);

        $this->actingAs($user)
            ->delete(route('usuarios.destroy', $user))
            ->assertRedirect(route('usuarios.show', $user))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
