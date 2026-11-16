<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paginación con estilos de Bootstrap 5.
        Paginator::useBootstrapFive();

        // Soporte de layout como componentes Blade: <x-layouts.app>, <x-layout> y <x-app-layout>
        Blade::component('layouts.app', 'layouts.app');
        Blade::component('layouts.app', 'layout');
        Blade::component('layouts.app', 'app-layout');
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');

        // Permite usar @can('products.create') en Blade según el rol.
        Gate::before(function (User $user, string $ability) {
            return $user->hasPermission($ability) ? true : null;
        });

        // Correo de verificación en español, con el enlace firmado de la app.
        VerifyEmail::toMailUsing(function ($notifiable, string $verificationUrl) {
            return (new MailMessage)
                ->subject('Verificación de correo electrónico · Sistema de Inventario')
                ->greeting('Verificación de correo')
                ->line('Haga clic en el botón para verificar su dirección de correo ('.$notifiable->email.').')
                ->action('Verificar mi correo', $verificationUrl)
                ->line('Si no creó esta cuenta, ignore este mensaje.');
        });
    }
}
