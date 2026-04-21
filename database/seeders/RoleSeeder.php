<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's roles.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador del sistema',
                'status' => true,
            ],
            [
                'name' => 'coordinator',
                'description' => 'Coordinador de eventos',
                'status' => true,
            ],
            [
                'name' => 'user',
                'description' => 'Usuario regular',
                'status' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role,
            );
        }
    }
}
