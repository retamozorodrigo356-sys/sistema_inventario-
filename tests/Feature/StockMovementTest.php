<?php

namespace Tests\Feature;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-05: registrar entradas.
 * RF-06: registrar salidas.
 * RF-07: actualizar automáticamente el stock.
 * RF-08: impedir salidas superiores al stock disponible.
 */
class StockMovementTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_entry_increases_stock_and_records_movement(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $user = $this->userWithRole('encargado');

        $response = $this->actingAs($user)->post(route('entradas.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => now()->toDateString(),
            'observation' => 'Reposición',
        ]);

        $response->assertRedirect(route('entradas.index'));
        $response->assertSessionHas('success');

        // RF-07: el stock aumenta automáticamente.
        $this->assertSame(15, $product->fresh()->stock);

        // RF-10: queda registrada la entrada con su usuario.
        $this->assertDatabaseHas('movimientos', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => Movement::ENTRADA,
            'quantity' => 5,
        ]);
    }

    public function test_exit_decreases_stock_and_records_movement(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $user = $this->userWithRole('admin');

        $this->actingAs($user)->post(route('salidas.store'), [
            'product_id' => $product->id,
            'quantity' => 4,
            'date' => now()->toDateString(),
            'observation' => 'Entrega',
        ])->assertRedirect(route('salidas.index'));

        $this->assertSame(6, $product->fresh()->stock);
        $this->assertDatabaseHas('movimientos', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => Movement::SALIDA,
            'quantity' => 4,
        ]);
    }

    public function test_exit_greater_than_stock_is_blocked(): void
    {
        $product = $this->makeProduct(['stock' => 5]);

        $response = $this->actingAs($this->userWithRole('encargado'))
            ->from(route('salidas.create'))
            ->post(route('salidas.store'), [
                'product_id' => $product->id,
                'quantity' => 50,
                'date' => now()->toDateString(),
            ]);

        // RF-08: no se permite la salida y el stock no cambia.
        $response->assertSessionHasErrors('quantity');
        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(0, Movement::count());
    }

    public function test_exit_equal_to_stock_is_allowed(): void
    {
        $product = $this->makeProduct(['stock' => 5]);

        $this->actingAs($this->userWithRole('encargado'))->post(route('salidas.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => now()->toDateString(),
        ])->assertRedirect(route('salidas.index'));

        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_movement_quantity_must_be_positive(): void
    {
        $product = $this->makeProduct(['stock' => 5]);

        $this->actingAs($this->userWithRole('admin'))
            ->post(route('salidas.store'), [
                'product_id' => $product->id,
                'quantity' => 0,
                'date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_entry_and_exit_require_existing_product_and_date(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->post(route('entradas.store'), [
                'product_id' => 999999,
                'quantity' => -3,
                'date' => 'no-es-fecha',
            ])
            ->assertSessionHasErrors(['product_id', 'quantity', 'date']);
    }

    public function test_consultor_cannot_register_movements(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $consultor = $this->userWithRole('consultor');

        $this->actingAs($consultor)->get(route('entradas.create'))->assertRedirect(route('dashboard'));
        $this->actingAs($consultor)->get(route('salidas.create'))->assertRedirect(route('dashboard'));

        $this->actingAs($consultor)->post(route('entradas.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => now()->toDateString(),
        ])->assertRedirect(route('dashboard'));

        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(0, Movement::count());
    }

    public function test_entry_and_exit_forms_show_available_products(): void
    {
        $product = $this->makeProduct(['name' => 'Producto Visible', 'stock' => 8]);

        $this->actingAs($this->userWithRole('encargado'))
            ->get(route('entradas.create'))
            ->assertOk()
            ->assertSee('Producto Visible')
            ->assertSee('stock: 8');

        $this->actingAs($this->userWithRole('encargado'))
            ->get(route('salidas.create'))
            ->assertOk()
            ->assertSee('disponible: 8');
    }
}
