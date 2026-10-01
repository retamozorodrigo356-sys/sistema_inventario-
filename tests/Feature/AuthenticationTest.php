<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-01: el sistema debe permitir iniciar sesión.
 * RNF-03: las contraseñas se almacenan con hash.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get(route('products.index'))->assertRedirect('/login');
    }

    public function test_login_page_is_displayed(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Iniciar sesión');
    }

    public function test_user_can_login_with_username(): void
    {
        $user = $this->userWithRole('admin');
        $user->update(['username' => 'pruebaadmin']);

        $response = $this->post(route('login.store'), [
            'login' => 'pruebaadmin',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_email(): void
    {
        $user = $this->userWithRole('consultor');

        $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $this->userWithRole('consultor');

        $response = $this->from(route('login'))->post(route('login.store'), [
            'login' => 'noexiste',
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_password_is_stored_hashed(): void
    {
        $user = $this->userWithRole('admin');

        $this->assertNotSame('password', $user->password);
        $this->assertStringStartsWith('$2y$', $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $user->password));
    }

    public function test_user_can_logout(): void
    {
        $user = $this->userWithRole('encargado');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
