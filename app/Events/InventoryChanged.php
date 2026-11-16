<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Notifica en tiempo real que el inventario cambió (RF-07, RF-12).
 *
 * Se envía de inmediato (ShouldBroadcastNow) por la conexión configurada en
 * BROADCAST_CONNECTION; si el servidor de WebSockets no está disponible, el
 * fallo solo se registra en el log y no interrumpe la operación del usuario.
 */
class InventoryChanged implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $action,
        public string $description,
        public ?string $actor = null,
    ) {}

    /**
     * Notifica sin interrumpir la petición si los WebSockets no están activos.
     */
    public static function notify(string $action, string $description): void
    {
        try {
            static::dispatch($action, $description, auth()->user()?->name);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Canal privado al que solo acceden los usuarios con acceso al dashboard.
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('inventory')];
    }

    /**
     * Nombre del evento escuchado en el cliente: .inventory.changed
     */
    public function broadcastAs(): string
    {
        return 'inventory.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'description' => $this->description,
            'actor' => $this->actor,
            'at' => now()->toIso8601String(),
        ];
    }
}
