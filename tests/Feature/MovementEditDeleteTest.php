<?php

namespace Tests\Feature;

use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-05 / RF-06 / RF-07 / RF-08 ampliados:
 * editar y eliminar entradas y salidas manteniendo el stock consistente.
 */
class MovementEditDeleteTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    /** Edita una entrada y recalcula el stock (RF-07). */
    public function test_editing_entry_quantity_updates_stock(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $movement = $this->makeMovement($product, $this->userWithRole('encargado'), Movement::ENTRADA, 5);

        $this->actingAs($this->userWithRole('admin'))
            ->put(route('entradas.update', $movement), [
                'product_id' => $product->id,
                'quantity' => 8,
                'date' => now()->toDateString(),
                'observation' => 'Corregida',
            ])
            ->assertRedirect(route('entradas.index'));

        $this->assertSame(13, $product->fresh()->stock);
        $this->assertSame(8, $movement->fresh()->quantity);
    }

    /** Al cambiar de producto se revierte el anterior y se aplica el nuevo. */
    public function test_editing_entry_product_moves_stock_between_products(): void
    {
        $origen = $this->makeProduct(['stock' => 10]);
        $destino = $this->makeProduct(['stock' => 2]);
        $movement = $this->makeMovement($origen, $this->userWithRole('admin'), Movement::ENTRADA, 4);

        $this->actingAs($this->userWithRole('admin'))
            ->put(route('entradas.update', $movement), [
                'product_id' => $destino->id,
                'quantity' => 4,
                'date' => now()->toDateString(),
            ])
            ->assertRedirect(route('entradas.index'));

        $this->assertSame(6, $origen->fresh()->stock);
        $this->assertSame(6, $destino->fresh()->stock);
        $this->assertSame($destino->id, $movement->fresh()->product_id);
    }

    /** Eliminar una entrada revierte el aumento de stock (RF-07). */
    public function test_deleting_entry_reverts_stock(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $movement = $this->makeMovement($product, $this->userWithRole('admin'), Movement::ENTRADA, 5);

        $this->actingAs($this->userWithRole('admin'))
            ->delete(route('entradas.destroy', $movement))
            ->assertRedirect(route('entradas.index'))
            ->assertSessionHas('success');

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertDatabaseMissing('movimientos', ['id' => $movement->id]);
    }

    /** No se borra una entrada si el stock ya no alcanza para reversarla. */
    public function test_entry_that_would_negative_stock_cannot_be_deleted(): void
    {
        $product = $this->makeProduct(['stock' => 2]);
        $movement = $this->makeMovement($product, $this->userWithRole('admin'), Movement::ENTRADA, 10);

        $this->actingAs($this->userWithRole('admin'))
            ->delete(route('entradas.destroy', $movement))
            ->assertRedirect(route('entradas.index'))
            ->assertSessionHas('error');

        $this->assertSame(2, $product->fresh()->stock);
        $this->assertDatabaseHas('movimientos', ['id' => $movement->id]);
    }

    /** Editar una salida descuenta solo la diferencia (RF-07). */
    public function test_editing_exit_quantity_updates_stock(): void
    {
        // El stock ya refleja la salida: 10 - 5 = 5.
        $product = $this->makeProduct(['stock' => 5]);
        $movement = $this->makeMovement($product, $this->userWithRole('encargado'), Movement::SALIDA, 5);

        $this->actingAs($this->userWithRole('admin'))
            ->put(route('salidas.update', $movement), [
                'product_id' => $product->id,
                'quantity' => 2,
                'date' => now()->toDateString(),
            ])
            ->assertRedirect(route('salidas.index'));

        $this->assertSame(8, $product->fresh()->stock);
        $this->assertSame(2, $movement->fresh()->quantity);
    }

    /** RF-08: la salida editada no puede superar el stock disponible. */
    public function test_editing_exit_cannot_exceed_stock(): void
    {
        $product = $this->makeProduct(['stock' => 4]);
        $movement = $this->makeMovement($product, $this->userWithRole('admin'), Movement::SALIDA, 5);

        $this->actingAs($this->userWithRole('admin'))
            ->put(route('salidas.update', $movement), [
                'product_id' => $product->id,
                'quantity' => 50,
                'date' => now()->toDateString(),
            ])
            ->assertRedirect(route('salidas.edit', $movement))
            ->assertSessionHas('error');

        $this->assertSame(4, $product->fresh()->stock);
        $this->assertSame(5, $movement->fresh()->quantity);
    }

    /** Eliminar una salida devuelve la cantidad al stock (RF-07). */
    public function test_deleting_exit_restores_stock(): void
    {
        // El stock ya refleja la salida: 10 - 5 = 5.
        $product = $this->makeProduct(['stock' => 5]);
        $movement = $this->makeMovement($product, $this->userWithRole('admin'), Movement::SALIDA, 5);

        $this->actingAs($this->userWithRole('admin'))
            ->delete(route('salidas.destroy', $movement))
            ->assertRedirect(route('salidas.index'))
            ->assertSessionHas('success');

        $this->assertSame(10, $product->fresh()->stock);
        $this->assertDatabaseMissing('movimientos', ['id' => $movement->id]);
    }

    /** Un tipo de movimiento no puede gestionarse desde el módulo contrario. */
    public function test_exit_cannot_be_edited_from_entries_module(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $salida = $this->makeMovement($product, $this->userWithRole('admin'), Movement::SALIDA, 5);
        $entrada = $this->makeMovement($product, $this->userWithRole('admin'), Movement::ENTRADA, 5);

        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->get(route('entradas.edit', $salida))->assertNotFound();
        $this->actingAs($admin)->get(route('salidas.edit', $entrada))->assertNotFound();
    }

    /** El consultor solo consulta: no ve botones ni puede editar/eliminar. */
    public function test_consultor_cannot_edit_or_delete_movements(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $entrada = $this->makeMovement($product, $this->userWithRole('admin'), Movement::ENTRADA, 5);
        $salida = $this->makeMovement($product, $this->userWithRole('admin'), Movement::SALIDA, 5);
        $consultor = $this->userWithRole('consultor');

        $this->actingAs($consultor)->get(route('entradas.edit', $entrada))->assertRedirect(route('dashboard'));
        $this->actingAs($consultor)->get(route('salidas.edit', $salida))->assertRedirect(route('dashboard'));
        $this->actingAs($consultor)->delete(route('entradas.destroy', $entrada))->assertRedirect(route('dashboard'));
        $this->actingAs($consultor)->delete(route('salidas.destroy', $salida))->assertRedirect(route('dashboard'));

        // Entrada (+5) y salida (-5) se anulan: el stock no cambia.
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertDatabaseHas('movimientos', ['id' => $entrada->id]);
        $this->assertDatabaseHas('movimientos', ['id' => $salida->id]);
    }

    /** El encargado gestiona entradas y salidas; el consultor no ve los botones. */
    public function test_action_buttons_follow_permissions(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $entrada = $this->makeMovement($product, $this->userWithRole('admin'), Movement::ENTRADA, 5);
        $salida = $this->makeMovement($product, $this->userWithRole('admin'), Movement::SALIDA, 5);

        $this->actingAs($this->userWithRole('encargado'))
            ->get(route('entradas.index'))
            ->assertOk()
            ->assertSee(route('entradas.edit', $entrada), false)
            ->assertSee('data-delete-url="'.route('entradas.destroy', $entrada).'"', false);

        $this->actingAs($this->userWithRole('encargado'))
            ->get(route('salidas.index'))
            ->assertOk()
            ->assertSee(route('salidas.edit', $salida), false)
            ->assertSee('data-delete-url="'.route('salidas.destroy', $salida).'"', false);
    }
}
