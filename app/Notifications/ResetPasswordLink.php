<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Envía por correo el enlace para restablecer la contraseña desde la
 * propia aplicación. El token es de un solo uso y vence según
 * config('auth.passwords.users.expire') (60 minutos).
 */
class ResetPasswordLink extends Notification
{
    use Queueable;

    public function __construct(public readonly string $token) {}

    /**
     * Canales usados: solo correo (Gmail SMTP).
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject(__('app.mail.reset_subject'))
            ->greeting(__('app.mail.reset_greeting', ['name' => $notifiable->name]))
            ->line(__('app.mail.reset_line_1'))
            ->action(__('app.mail.reset_action'), $url)
            ->line(__('app.mail.reset_line_2'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
