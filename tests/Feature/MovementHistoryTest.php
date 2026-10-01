<?php

namespace Tests\Feature;

use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-10: registrar un historial de movimientos
 * (entrada/salida, producto, cantidad, fecha y usuario).
 */
class MovementHistoryTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_history_shows_entries_exits_product_quantity_date_and_user(): void
    {
        $admin = $this->userWithRole('admin');
        $admin->update(['name' => 'Responsable Almacén']);

        $entrada = $this->makeProduct(['name' => 'Producto Entrada']);
        $salida = $this->makeProduct(['name' => 'Producto Salida']);

        $this->makeMovement($entrada, $admin, Movement::ENTRADA, 20);
        $this->makeMovement($salida, $admin, Movement::SALIDA, 7);

        $response = $this->actingAs($admin)->get(route('movimientos.index'));

        $response->assertOk()
            ->assertSee('Producto Entrada')
            ->assertSee('Producto Salida')
            ->assertSee('Responsable Almacén')
            ->assertSee('20')
            ->assertSee('7')
            ->assertSee(now()->format('d/m/Y'));
    }

    public function test_history_can_be_filtered_by_type(): void
    {
        $admin = $this->userWithRole('admin');

        $this->makeMovement($this->makeProduct(['name' => 'Solo Entrada']), $admin, Movement::ENTRADA, 10);
        $this->makeMovement($this->makeProduct(['name' => 'Solo Salida']), $admin, Movement::SALIDA, 5);

        $this->actingAs($admin)
            ->get(route('movimientos.index', ['type' => 'entrada']))
            ->assertOk()
            ->assertSee('Solo Entrada')
            ->assertDontSee('Solo Salida');

        $this->actingAs($admin)
            ->get(route('movimientos.index', ['type' => 'salida']))
            ->assertOk()
            ->assertSee('Solo Salida')
            ->assertDontSee('Solo Entrada');
    }

    public function test_history_can_be_filtered_by_product(): void
    {
        $admin = $this->userWithRole('admin');

        $this->makeMovement($this->makeProduct(['name' => 'Teclado Mecánico']), $admin, Movement::ENTRADA, 10);
        $this->makeMovement($this->makeProduct(['name' => 'Silla Oficina']), $admin, Movement::ENTRADA, 2);

        $this->actingAs($admin)
            ->get(route('movimientos.index', ['search' => 'Teclado']))
            ->assertOk()
            ->assertSee('Teclado Mecánico')
            ->assertDontSee('Silla Oficina');
    }

    public function test_entries_and_exits_modules_show_their_own_history(): void
    {
        $admin = $this->userWithRole('admin');

        $this->makeMovement($this->makeProduct(['name' => 'Producto Entradas']), $admin, Movement::ENTRADA, 10);
        $this->makeMovement($this->makeProduct(['name' => 'Producto Salidas']), $admin, Movement::SALIDA, 5);

        $this->actingAs($admin)->get(route('entradas.index'))->assertOk()->assertSee('Producto Entradas');
        $this->actingAs($admin)->get(route('salidas.index'))->assertOk()->assertSee('Producto Salidas');
    }

    public function test_consultor_can_view_history_but_not_register(): void
    {
        $consultor = $this->userWithRole('consultor');

        $this->actingAs($consultor)->get(route('movimientos.index'))->assertOk();
        $this->actingAs($consultor)->get(route('entradas.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($consultor)->get(route('salidas.index'))->assertRedirect(route('dashboard'));
    }
}
