<?php

namespace Tests\Feature;

use App\Notifications\ResetPasswordLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * Recuperación de contraseña desde el login: el correo incluye un enlace
 * (token de un solo uso) para elegir una nueva contraseña en la aplicación
 * (RNF-03: hash, nunca texto plano).
 */
class PasswordRecoveryTest extends TestCase
{
    use BuildsInventory, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_forgot_password_page_is_displayed(): void
    {
        $this->get(route('password.forgot'))
            ->assertOk()
            ->assertSee('¿Olvidaste tu contraseña?');
    }

    public function test_login_page_links_to_forgot_password(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('password.forgot'));
    }

    public function test_registered_email_receives_a_reset_link(): void
    {
        Notification::fake();
        $user = $this->userWithRole('consultor');

        $this->post(route('password.forgot.store'), ['email' => $user->email])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $token = null;
        Notification::assertSentTo($user, ResetPasswordLink::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->assertNotNull($token);

        // La contraseña sigue intacta hasta que se usa el enlace.
        $this->assertTrue(Hash::check('password', $user->fresh()->password));

        // El enlace abre el formulario de nueva contraseña.
        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('Nueva contraseña');

        // Restablecer desde el formulario del correo.
        $this->post(route('password.reset.store', $token), [
            'email' => $user->email,
            'password' => 'nuevapass1',
            'password_confirmation' => 'nuevapass1',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('nuevapass1', $user->fresh()->password));
        $this->assertFalse(Hash::check('password', $user->fresh()->password));

        // Se puede iniciar sesión con la contraseña nueva.
        $this->post(route('login.store'), ['login' => $user->email, 'password' => 'nuevapass1'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_reset_link_token_cannot_be_reused(): void
    {
        Notification::fake();
        $user = $this->userWithRole('consultor');

        $this->post(route('password.forgot.store'), ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo($user, ResetPasswordLink::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $payload = [
            'email' => $user->email,
            'password' => 'nuevapass2',
            'password_confirmation' => 'nuevapass2',
        ];

        $this->post(route('password.reset.store', $token), $payload)
            ->assertRedirect(route('login'));

        // Segundo uso del mismo enlace: token ya consumido.
        $this->post(route('password.reset.store', $token), [
            'email' => $user->email,
            'password' => 'otrapass9',
            'password_confirmation' => 'otrapass9',
        ])
            ->assertRedirect(route('password.forgot'))
            ->assertSessionHasErrors('email');

        // La contraseña quedó en la del primer uso y la segunda no corrió.
        $this->assertTrue(Hash::check('nuevapass2', $user->fresh()->password));
        $this->assertFalse(Hash::check('otrapass9', $user->fresh()->password));
    }

    public function test_invalid_token_shows_an_error_and_keeps_the_password(): void
    {
        $user = $this->userWithRole('consultor');

        $this->post(route('password.reset.store', 'token-inexistente'), [
            'email' => $user->email,
            'password' => 'nuevapass3',
            'password_confirmation' => 'nuevapass3',
        ])
            ->assertRedirect(route('password.forgot'))
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_unknown_email_gets_the_same_answer_without_sending(): void
    {
        Notification::fake();
        $this->userWithRole('consultor');

        $this->post(route('password.forgot.store'), ['email' => 'noexiste@inventario.test'])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        Notification::assertNothingSent();
    }

    public function test_email_is_required_and_valid(): void
    {
        $this->post(route('password.forgot.store'), ['email' => 'no-es-correo'])
            ->assertSessionHasErrors('email');
    }

    public function test_reset_form_requires_a_valid_password_and_confirmation(): void
    {
        $this->post(route('password.reset.store', 'token-inexistente'), [
            'email' => 'usuario@inventario.test',
            'password' => '123',
            'password_confirmation' => '456',
        ])
            ->assertSessionHasErrors('password');
    }
}
