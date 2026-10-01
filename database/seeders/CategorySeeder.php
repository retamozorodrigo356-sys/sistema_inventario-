<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Cinco categorías de ejemplo (RF-04).
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electrónica', 'description' => 'Equipos y accesorios electrónicos'],
            ['name' => 'Hogar y cocina', 'description' => 'Artículos para el hogar'],
            ['name' => 'Oficina', 'description' => 'Materiales y mobiliario de oficina'],
            ['name' => 'Limpieza', 'description' => 'Productos de aseo y limpieza'],
            ['name' => 'Ferretería', 'description' => 'Herramientas y suministros'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
