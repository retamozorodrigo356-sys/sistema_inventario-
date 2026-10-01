<?php

namespace Database\Seeders;

use App\Models\Movement;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MovementSeeder extends Seeder
{
    /**
     * Entradas y salidas de ejemplo; el stock final de cada
     * producto se recalcula a partir de sus movimientos (RF-07, RF-10).
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@inventario.test')->first();
        $almacen = User::where('email', 'almacen@inventario.test')->first();

        // [código, stock final, salida previa]
        $data = [
            ['PRD-001', 25, 6],
            ['PRD-002', 4, 3],
            ['PRD-003', 12, 2],
            ['PRD-004', 3, 7],
            ['PRD-005', 8, 2],
            ['PRD-006', 6, 4],
            ['PRD-007', 2, 3],
            ['PRD-008', 60, 20],
            ['PRD-009', 9, 1],
            ['PRD-010', 18, 10],
        ];

        foreach ($data as $index => [$code, $stock, $salida]) {
            $product = Product::where('code', $code)->first();

            if (! $product) {
                continue;
            }

            $days = ($index + 1) * 2;

            // Entrada inicial: stock final + lo que luego salió.
            Movement::create([
                'product_id' => $product->id,
                'user_id' => ($almacen ?? $admin)?->id,
                'type' => Movement::ENTRADA,
                'quantity' => $stock + $salida,
                'date' => Carbon::now()->subDays($days + 7)->toDateString(),
                'observation' => 'Ingreso inicial de mercadería',
            ]);

            // Salida parcial.
            Movement::create([
                'product_id' => $product->id,
                'user_id' => ($admin ?? $almacen)?->id,
                'type' => Movement::SALIDA,
                'quantity' => $salida,
                'date' => Carbon::now()->subDays($days)->toDateString(),
                'observation' => 'Despacho a área solicitante',
            ]);

            // El stock actual siempre coincide con sus movimientos.
            $product->update(['stock' => $stock]);
        }
    }
}
