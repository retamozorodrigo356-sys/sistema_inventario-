<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('categories', 'categorias');
        Schema::rename('products', 'productos');
        Schema::rename('movements', 'movimientos');
    }

    public function down(): void
    {
        Schema::rename('movimientos', 'movements');
        Schema::rename('productos', 'products');
        Schema::rename('categorias', 'categories');
    }
};
