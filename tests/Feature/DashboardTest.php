<?php

namespace Tests\Feature;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-09: mostrar productos con stock bajo.
 * RF-12: mostrar información resumida mediante un dashboard.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_guest_cannot_see_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect('/login');
    }

    public function test_dashboard_shows_summary_cards(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total de productos')
            ->assertSee('Total de categorías')
            ->assertSee('Productos con stock bajo')
            ->assertSee('Total de entradas')
            ->assertSee('Total de salidas');
    }

    public function test_dashboard_counts_are_correct(): void
    {
        $admin = $this->userWithRole('admin');

        $this->makeProduct(['stock' => 10, 'stock_min' => 2]); // sin stock bajo
        $low = $this->makeProduct(['stock' => 1, 'stock_min' => 5]); // stock bajo

        $entry = Product::first();
        $this->makeMovement($entry, $admin, Movement::ENTRADA, 10);
        $this->makeMovement($entry, $admin, Movement::SALIDA, 3);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('totals', fn (array $totals) => $totals['products'] === 2
                && $totals['categories'] === 2
                && $totals['low_stock'] === 1
                && $totals['entries'] === 1
                && $totals['exits'] === 1);
    }

    public function test_dashboard_lists_low_stock_products(): void
    {
        $low = $this->makeProduct(['name' => 'Producto Agotado', 'stock' => 0, 'stock_min' => 5]);
        $this->makeProduct(['name' => 'Producto Sano', 'stock' => 100, 'stock_min' => 5]);

        $this->actingAs($this->userWithRole('consultor'))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Producto Agotado')
            ->assertDontSee('Producto Sano');
    }
}
