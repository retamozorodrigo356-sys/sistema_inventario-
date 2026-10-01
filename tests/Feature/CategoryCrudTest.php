<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsInventory;
use Tests\TestCase;

/**
 * RF-04: gestionar categorías (CRUD).
 */
class CategoryCrudTest extends TestCase
{
    use RefreshDatabase, BuildsInventory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_categories_list_is_displayed(): void
    {
        $category = $this->makeCategory(['name' => 'Categoría Especial']);

        $this->actingAs($this->userWithRole('consultor'))
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Categoría Especial');
    }

    public function test_category_can_be_created(): void
    {
        $response = $this->actingAs($this->userWithRole('encargado'))->post(route('categories.store'), [
            'name' => 'Deportes',
            'description' => 'Artículos deportivos',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categorias', ['name' => 'Deportes']);
    }

    public function test_category_requires_a_name(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->post(route('categories.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_category_name_must_be_unique(): void
    {
        $this->makeCategory(['name' => 'Repetida']);

        $this->actingAs($this->userWithRole('admin'))
            ->post(route('categories.store'), ['name' => 'Repetida'])
            ->assertSessionHasErrors('name');
    }

    public function test_category_can_be_updated(): void
    {
        $category = $this->makeCategory();

        $this->actingAs($this->userWithRole('encargado'))
            ->put(route('categories.update', $category), [
                'name' => 'Nombre nuevo',
                'description' => 'Descripción nueva',
            ])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categorias', ['id' => $category->id, 'name' => 'Nombre nuevo']);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $category = $this->makeCategory();

        $this->actingAs($this->userWithRole('admin'))
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categorias', ['id' => $category->id]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $product = $this->makeProduct();
        $category = $product->category;

        $response = $this->actingAs($this->userWithRole('admin'))
            ->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categorias', ['id' => $category->id]);
    }

    public function test_consultor_cannot_manage_categories(): void
    {
        $category = $this->makeCategory();
        $consultor = $this->userWithRole('consultor');

        $this->actingAs($consultor)->get(route('categories.create'))->assertRedirect(route('dashboard'));
        $this->actingAs($consultor)->post(route('categories.store'), ['name' => 'N'])->assertRedirect(route('dashboard'));
        $this->actingAs($consultor)->delete(route('categories.destroy', $category))->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('categorias', ['id' => $category->id]);
    }
}
