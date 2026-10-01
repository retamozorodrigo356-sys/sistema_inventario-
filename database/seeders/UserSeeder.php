<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Un usuario de prueba por cada rol (contraseñas con hash).
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ana Torres',
                'username' => 'admin',
                'email' => 'admin@inventario.test',
                'password' => 'admin123',
                'role' => 'admin',
            ],
            [
                'name' => 'Luis Mendoza',
                'username' => 'almacen',
                'email' => 'almacen@inventario.test',
                'password' => 'almacen123',
                'role' => 'encargado',
            ],
            [
                'name' => 'María López',
                'username' => 'consultor',
                'email' => 'consultor@inventario.test',
                'password' => 'consultor123',
                'role' => 'consultor',
            ],
        ];

        foreach ($users as $data) {
            $role = Role::where('slug', $data['role'])->first();

            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => $data['password'], // se guarda con hash (RNF-03)
                    'role_id' => $role?->id,
                ]
            );
        }
    }
}
