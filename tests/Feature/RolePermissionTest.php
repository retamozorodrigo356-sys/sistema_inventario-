<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RNF-04: el sistema debe controlar el acceso mediante autenticación y roles.
 *
 * Administrador  → acceso completo.
 * Encargado      → gestiona productos/categorías/movimientos, no usuarios ni elimina productos.
 * Consultor      → solo consulta.
 */
class RolePermissionTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    /** Secciones visibles para cualquier rol autenticado. */
    public function test_every_role_can_view_dashboard_and_reports(): void
    {
        foreach (['admin', 'encargado', 'consultor'] as $slug) {
            $this->actingAs($this->userWithRole($slug))
                ->get(route('dashboard'))
                ->assertOk();

            $this->actingAs($this->userWithRole($slug))
                ->get(route('reportes.index'))
                ->assertOk();
        }
    }

    public function test_admin_can_access_every_section(): void
    {
        $admin = $this->userWithRole('admin');

        foreach ([
            'products.index', 'products.create', 'categories.index', 'categories.create',
            'entradas.index', 'entradas.create', 'salidas.index', 'salidas.create',
            'movimientos.index', 'usuarios.index', 'usuarios.create', 'reportes.index',
        ] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }
    }

    public function test_consultor_can_only_view_information(): void
    {
        $consultor = $this->userWithRole('consultor');

        foreach (['products.index', 'categories.index', 'movimientos.index', 'reportes.index'] as $route) {
            $this->actingAs($consultor)->get(route($route))->assertOk();
        }

        foreach (['products.create', 'categories.create', 'entradas.create', 'salidas.create', 'usuarios.index'] as $route) {
            $this->actingAs($consultor)->get(route($route))->assertRedirect(route('dashboard'));
        }
    }

    public function test_encargado_cannot_manage_users(): void
    {
        $encargado = $this->userWithRole('encargado');

        $this->actingAs($encargado)->get(route('usuarios.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($encargado)->get(route('usuarios.create'))->assertRedirect(route('dashboard'));
    }

    public function test_encargado_can_manage_products_categories_and_movements(): void
    {
        $encargado = $this->userWithRole('encargado');

        foreach (['products.create', 'categories.create', 'entradas.create', 'salidas.create', 'movimientos.index'] as $route) {
            $this->actingAs($encargado)->get(route($route))->assertOk();
        }
    }

    public function test_consultor_does_not_see_action_buttons(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->userWithRole('consultor'))
            ->get(route('products.index'))
            ->assertOk()
            ->assertDontSee('Registrar producto')
            ->assertDontSee('data-delete-url="'.route('products.destroy', $product).'"', false);
    }

    public function test_encargado_does_not_see_product_delete_button(): void
    {
        $product = $this->makeProduct();

        $response = $this->actingAs($this->userWithRole('encargado'))
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee(route('products.edit', $product))
            ->assertDontSee('data-delete-url="'.route('products.destroy', $product).'"', false);
    }

    public function test_admin_sees_all_buttons(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->userWithRole('admin'))
            ->get(route('products.index'))
            ->assertSee('Registrar producto')
            ->assertSee('data-delete-url="'.route('products.destroy', $product).'"', false);
    }
}
