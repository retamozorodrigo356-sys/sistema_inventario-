<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;

/**
 * Helpers para construir datos de prueba del inventario.
 */
trait BuildsInventory
{
    /** Carga los tres roles del sistema. */
    protected function seedRoles(): void
    {
        $this->seed(RoleSeeder::class);
    }

    /** Crea un usuario con el rol indicado (admin, encargado, consultor). */
    protected function userWithRole(string $slug): User
    {
        $role = Role::where('slug', $slug)->firstOrFail();

        return User::factory()->create(['role_id' => $role->id]);
    }

    /** Crea una categoría. */
    protected function makeCategory(array $attributes = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Categoría '.uniqid(),
            'description' => 'Descripción de prueba',
        ], $attributes));
    }

    /** Crea un producto ligado a una categoría. */
    protected function makeProduct(array $attributes = []): Product
    {
        $product = Product::create(array_merge([
            'code' => 'TST-'.uniqid(),
            'name' => 'Producto '.uniqid(),
            'description' => 'Producto de prueba',
            'category_id' => ($attributes['category_id'] ?? null) ?? $this->makeCategory()->id,
            'price' => 100,
            'stock' => 0,
            'stock_min' => 5,
            'status' => 'activo',
        ], $attributes));

        return $product->fresh();
    }

    /** Crea un movimiento directamente en la base. */
    protected function makeMovement(Product $product, User $user, string $type, int $quantity): Movement
    {
        return Movement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => $type,
            'quantity' => $quantity,
            'date' => now()->toDateString(),
            'observation' => 'Movimiento de prueba',
        ]);
    }
}
