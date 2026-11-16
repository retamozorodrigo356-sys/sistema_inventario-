<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * Verificación de correo electrónico: banner de aviso (no bloquea),
 * reenvío del correo y confirmación mediante enlace firmado.
 */
class EmailVerificationTest extends TestCase
{
    use BuildsInventory, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    /** Crea un usuario cuyo correo aún no está verificado. */
    private function unverifiedUser(string $slug): User
    {
        $user = $this->userWithRole($slug);
        $user->forceFill(['email_verified_at' => null])->save();

        return $user;
    }

    public function test_unverified_user_is_blocked_from_every_view(): void
    {
        $user = $this->unverifiedUser('consultor');

        // Sin el correo verificado no se responde ninguna vista del sistema.
        foreach (['dashboard', 'products.index', 'categories.index', 'entradas.index',
            'salidas.index', 'movimientos.index', 'reportes.index'] as $route) {
            $this->actingAs($user)
                ->get(route($route))
                ->assertRedirect(route('verification.notice'));
        }

        // El aviso de bloqueo sí está disponible y ofrece reenviar el correo.
        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Verificá tu correo')
            ->assertSee('Reenviar correo de verificación');
    }

    public function test_verified_user_does_not_see_the_banner(): void
    {
        $user = $this->userWithRole('consultor');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('aún no está verificado');
    }

    public function test_user_can_resend_the_verification_email(): void
    {
        Notification::fake();
        $user = $this->unverifiedUser('consultor');

        $this->actingAs($user)
            ->post(route('verification.resend'))
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('success');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_email_is_not_sent_when_already_verified(): void
    {
        Notification::fake();
        $user = $this->userWithRole('consultor');

        $this->actingAs($user)
            ->post(route('verification.resend'))
            ->assertRedirect(route('dashboard'));

        Notification::assertNotSentTo($user, VerifyEmail::class);
    }

    public function test_email_is_verified_through_the_signed_link(): void
    {
        $user = $this->unverifiedUser('consultor');

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // Verificado: ya puede entrar a las vistas del sistema.
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_wrong_hash_is_rejected(): void
    {
        $user = $this->unverifiedUser('consultor');

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => sha1('otro@correo.test'),
        ]);

        $this->actingAs($user)->get($url)->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_unsigned_link_is_rejected(): void
    {
        $user = $this->unverifiedUser('consultor');

        $this->actingAs($user)
            ->get(route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]))
            ->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_profile_shows_the_email_verification_status(): void
    {
        $verified = $this->userWithRole('consultor');

        $this->actingAs($verified)
            ->get(route('usuarios.show', $verified))
            ->assertOk()
            ->assertSee('Correo verificado')
            ->assertSee('Verificado')
            ->assertDontSee('No verificado');

        $pending = $this->unverifiedUser('encargado');
        $admin = $this->userWithRole('admin');

        // El propio usuario sin verificar no puede abrir el perfil (queda bloqueado),
        // pero un administrador sí ve el estado de su correo.
        $this->actingAs($pending)
            ->get(route('usuarios.show', $pending))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($admin)
            ->get(route('usuarios.show', $pending))
            ->assertOk()
            ->assertSee('Correo verificado')
            ->assertSee('No verificado');
    }

    public function test_public_form_to_request_the_verification_link_is_displayed(): void
    {
        $this->get(route('verification.request'))
            ->assertOk()
            ->assertSee('Verificar mi correo');
    }

    public function test_login_page_links_to_the_verification_request_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('verification.request'));
    }

    public function test_unverified_user_can_request_the_verification_link(): void
    {
        Notification::fake();
        $user = $this->unverifiedUser('consultor');

        $this->post(route('verification.request.store'), ['email' => $user->email])
            ->assertRedirect(route('verification.request'))
            ->assertSessionHas('success');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unknown_or_already_verified_email_gets_the_same_answer(): void
    {
        Notification::fake();
        $this->userWithRole('consultor'); // correo ya verificado (factory)

        $this->post(route('verification.request.store'), ['email' => 'noexiste@inventario.test'])
            ->assertRedirect(route('verification.request'))
            ->assertSessionHas('success');

        $this->post(route('verification.request.store'), ['email' => 'no-es-correo'])
            ->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }
}
