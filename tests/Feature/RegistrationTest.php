<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * Registro público de nuevos usuarios desde el login: la cuenta nace con
 * el rol "consultor" (solo lectura), la contraseña se guarda con hash
 * (RNF-03) y se envía el correo de verificación.
 */
class RegistrationTest extends TestCase
{
    use BuildsInventory, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_register_page_is_displayed(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Registrate');
    }

    public function test_login_page_links_to_the_register_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('register'));
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        Notification::fake();

        $this->post(route('register.store'), [
            'name' => 'Nuevo Registrado',
            'username' => 'registrado',
            'email' => 'registrado@inventario.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $user = User::where('email', 'registrado@inventario.test')->firstOrFail();

        $this->assertSame('consultor', $user->role->slug);   // solo lectura
        $this->assertSame('registrado', $user->username);
        $this->assertNotSame('secreto123', $user->password); // nunca en texto plano
        $this->assertTrue(Hash::check('secreto123', $user->password));
        $this->assertAuthenticatedAs($user);

        // Recibe el enlace de verificación y queda sin verificar.
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->assertNull($user->email_verified_at);
    }

    public function test_registration_requires_valid_data(): void
    {
        $this->post(route('register.store'), [
            'name' => '',
            'username' => 'no válido con espacios',
            'email' => 'no-es-correo',
            'password' => '123',
            'password_confirmation' => '456',
        ])
            ->assertSessionHasErrors(['name', 'username', 'email', 'password']);

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_username_and_email_must_be_unique(): void
    {
        $existing = $this->userWithRole('consultor');

        $this->post(route('register.store'), [
            'name' => 'Otro Usuario',
            'username' => $existing->username,
            'email' => 'otro@inventario.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertSessionHasErrors('username');

        $this->post(route('register.store'), [
            'name' => 'Otro Usuario',
            'username' => 'otrouser',
            'email' => $existing->email,
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 1); // solo el creado en el primer paso
    }

    public function test_authenticated_users_cannot_register(): void
    {
        $user = $this->userWithRole('consultor');

        $this->actingAs($user)
            ->get(route('register'))
            ->assertRedirect(route('dashboard'));
    }
}
