<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "User 1: " . (App\Models\User::first()?->id ?? 'NONE') . "\n";
echo "Category 1: " . (App\Models\Category::first()?->id ?? 'NONE') . "\n";
echo "Product 1: " . (App\Models\Product::first()?->id ?? 'NONE') . "\n";
