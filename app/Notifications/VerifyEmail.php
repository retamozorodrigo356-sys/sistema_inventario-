<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Correo de verificación con los textos traducidos al idioma activo
 * (español, inglés o francés), reutilizando el enlace firmado de Laravel.
 */
class VerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(__('app.mail.verify_subject'))
            ->greeting(__('app.mail.verify_greeting'))
            ->line(__('app.mail.verify_line_1', ['email' => $notifiable->getEmailForVerification()]))
            ->action(__('app.mail.verify_action'), $url)
            ->line(__('app.mail.verify_line_2'));
    }
}
