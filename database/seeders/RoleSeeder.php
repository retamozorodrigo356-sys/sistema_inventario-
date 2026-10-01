<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Los tres roles del sistema (Administrador, Encargado, Consultor).
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'slug' => 'admin'],
            ['name' => 'Encargado de almacén', 'slug' => 'encargado'],
            ['name' => 'Consultor', 'slug' => 'consultor'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
