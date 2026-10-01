<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-02: gestionar usuarios y asignar roles.
 * RNF-03: contraseñas con hash.
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

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

    public function test_user_can_be_created_with_role_and_hashed_password(): void
    {
        $role = \App\Models\Role::where('slug', 'consultor')->first();

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
        $adminRole = \App\Models\Role::where('slug', 'admin')->first();

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

    public function test_admin_cannot_delete_his_own_account(): void
    {
        $admin = $this->userWithRole('admin');

        $response = $this->actingAs($admin)->delete(route('usuarios.destroy', $admin));

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
