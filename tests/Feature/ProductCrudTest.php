<?php

namespace Tests\Feature;

use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-03: gestionar productos (CRUD).
 * RF-11: consultar el inventario (búsqueda y listado).
 */
class ProductCrudTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_products_list_is_displayed(): void
    {
        $product = $this->makeProduct(['name' => 'Monitor Especial']);

        $this->actingAs($this->userWithRole('consultor'))
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee($product->code)
            ->assertSee('Monitor Especial');
    }

    public function test_product_can_be_created(): void
    {
        $category = $this->makeCategory();

        $response = $this->actingAs($this->userWithRole('encargado'))->post(route('products.store'), [
            'code' => 'NEW-001',
            'name' => 'Producto nuevo',
            'description' => 'Descripción',
            'category_id' => $category->id,
            'price' => 99.50,
            'stock' => 12,
            'stock_min' => 3,
            'status' => 'activo',
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('productos', [
            'code' => 'NEW-001',
            'name' => 'Producto nuevo',
            'stock' => 12,
        ]);
    }

    public function test_product_requires_valid_data(): void
    {
        $response = $this->actingAs($this->userWithRole('admin'))
            ->from(route('products.create'))
            ->post(route('products.store'), [
                'code' => '',
                'name' => '',
                'category_id' => 999999,
                'price' => -5,
                'stock' => -1,
                'stock_min' => -1,
                'status' => 'otro',
            ]);

        $response->assertSessionHasErrors(['code', 'name', 'category_id', 'price', 'stock', 'stock_min', 'status']);
        $this->assertDatabaseCount('productos', 0);
    }

    public function test_product_code_must_be_unique(): void
    {
        $product = $this->makeProduct(['code' => 'DUP-001']);
        $category = $product->category_id;

        $this->actingAs($this->userWithRole('admin'))
            ->post(route('products.store'), [
                'code' => 'DUP-001',
                'name' => 'Otro producto',
                'category_id' => $category,
                'price' => 10,
                'stock' => 1,
                'stock_min' => 1,
                'status' => 'activo',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_product_can_be_updated(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->userWithRole('encargado'))
            ->put(route('products.update', $product), [
                'code' => $product->code,
                'name' => 'Nombre actualizado',
                'description' => 'Descripción actualizada',
                'category_id' => $product->category_id,
                'price' => 250,
                'stock' => 40,
                'stock_min' => 10,
                'status' => 'inactivo',
            ])
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('productos', [
            'id' => $product->id,
            'name' => 'Nombre actualizado',
            'status' => 'inactivo',
        ]);
    }

    public function test_product_can_be_deleted(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->userWithRole('admin'))
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('productos', ['id' => $product->id]);
    }

    public function test_product_with_movements_cannot_be_deleted(): void
    {
        $admin = $this->userWithRole('admin');
        $product = $this->makeProduct(['stock' => 10]);
        $this->makeMovement($product, $admin, Movement::ENTRADA, 10);

        $response = $this->actingAs($admin)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('productos', ['id' => $product->id]);
    }

    public function test_products_can_be_searched(): void
    {
        $this->makeProduct(['code' => 'BUSC-111', 'name' => 'Producto Encontrado']);
        $this->makeProduct(['code' => 'OTRO-222', 'name' => 'Producto Invisible']);

        $response = $this->actingAs($this->userWithRole('consultor'))
            ->get(route('products.index', ['search' => 'BUSC-111']));

        $response->assertOk()->assertSee('Producto Encontrado')->assertDontSee('Producto Invisible');
    }

    public function test_consultor_cannot_create_edit_or_delete_products(): void
    {
        $product = $this->makeProduct();
        $consultor = $this->userWithRole('consultor');

        $this->actingAs($consultor)->get(route('products.create'))->assertRedirect(route('dashboard'));
        $this->actingAs($consultor)->get(route('products.edit', $product))->assertRedirect(route('dashboard'));

        $this->actingAs($consultor)->post(route('products.store'), [
            'code' => 'X-1', 'name' => 'x', 'category_id' => $product->category_id,
            'price' => 1, 'stock' => 1, 'stock_min' => 1, 'status' => 'activo',
        ])->assertRedirect(route('dashboard'));

        $this->actingAs($consultor)->delete(route('products.destroy', $product))->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('productos', ['id' => $product->id]);
    }

    public function test_encargado_cannot_delete_products(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->userWithRole('encargado'))
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('productos', ['id' => $product->id]);
    }
}
