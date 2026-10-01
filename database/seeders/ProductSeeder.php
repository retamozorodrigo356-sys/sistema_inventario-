<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Diez productos de ejemplo, varios con stock bajo (RF-03, RF-09).
     * El stock definitivo se calcula en MovementSeeder.
     */
    public function run(): void
    {
        $products = [
            ['code' => 'PRD-001', 'name' => 'Teclado mecánico', 'category' => 'Electrónica', 'price' => 150.00, 'stock_min' => 5],
            ['code' => 'PRD-002', 'name' => 'Mouse inalámbrico', 'category' => 'Electrónica', 'price' => 75.00, 'stock_min' => 5],
            ['code' => 'PRD-003', 'name' => 'Monitor 24 pulgadas', 'category' => 'Electrónica', 'price' => 850.00, 'stock_min' => 3],
            ['code' => 'PRD-004', 'name' => 'Resma de papel A4', 'category' => 'Oficina', 'price' => 45.00, 'stock_min' => 10],
            ['code' => 'PRD-005', 'name' => 'Silla ergonómica', 'category' => 'Oficina', 'price' => 690.00, 'stock_min' => 2],
            ['code' => 'PRD-006', 'name' => 'Licuadora 1.5L', 'category' => 'Hogar y cocina', 'price' => 320.00, 'stock_min' => 2],
            ['code' => 'PRD-007', 'name' => 'Juego de ollas 5 piezas', 'category' => 'Hogar y cocina', 'price' => 410.00, 'stock_min' => 4],
            ['code' => 'PRD-008', 'name' => 'Detergente 1 kg', 'category' => 'Limpieza', 'price' => 28.00, 'stock_min' => 15],
            ['code' => 'PRD-009', 'name' => 'Taladro eléctrico', 'category' => 'Ferretería', 'price' => 560.00, 'stock_min' => 3],
            ['code' => 'PRD-010', 'name' => 'Cinta aisladora', 'category' => 'Ferretería', 'price' => 12.00, 'stock_min' => 20],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();

            Product::firstOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'description' => 'Producto de ejemplo para la demostración del sistema.',
                    'category_id' => $category?->id,
                    'price' => $data['price'],
                    'stock' => 0,
                    'stock_min' => $data['stock_min'],
                    'status' => 'activo',
                ]
            );
        }
    }
}
