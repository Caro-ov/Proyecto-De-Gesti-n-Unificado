<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrador del sistema'],
            ['name' => 'user', 'description' => 'Usuario regular'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}