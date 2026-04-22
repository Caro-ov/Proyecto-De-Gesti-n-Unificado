<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Principal',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'status' => true,
            ],
            [
                'name' => 'Coordinadora Eventos',
                'email' => 'coordinacion@example.com',
                'role' => 'coordinator',
                'status' => true,
            ],
            [
                'name' => 'Usuario Activo',
                'email' => 'usuario@example.com',
                'role' => 'user',
                'status' => true,
            ],
            [
                'name' => 'Usuario Secundario',
                'email' => 'usuario2@example.com',
                'role' => 'user',
                'status' => true,
            ],
            [
                'name' => 'Usuario Lista Espera',
                'email' => 'listaespera@example.com',
                'role' => 'user',
                'status' => true,
            ],
            [
                'name' => 'Usuario Cancelado',
                'email' => 'cancelado@example.com',
                'role' => 'user',
                'status' => true,
            ],
            [
                'name' => 'Usuario Historico',
                'email' => 'historico@example.com',
                'role' => 'user',
                'status' => true,
            ],
            [
                'name' => 'Usuario Inactivo',
                'email' => 'inactivo@example.com',
                'role' => 'user',
                'status' => false,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'status' => $user['status'],
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
