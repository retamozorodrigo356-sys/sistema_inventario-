<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\User::query()->update(['email_verified_at' => now()]);

foreach (App\Models\User::all() as $u) {
    echo "ID: {$u->id} | {$u->name} ({$u->email}) | Rol: {$u->role?->name} | Verificado: " . ($u->email_verified_at ? 'SI' : 'NO') . "\n";
}
