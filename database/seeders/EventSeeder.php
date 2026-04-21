<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Seed the application's events.
     */
    public function run(): void
    {
        $events = [
            [
                'name' => 'La ultima gran party',
                'description' => 'Hay que darle  mucha moral para que sea la ultima gran party',
                'date' => Carbon::now()->addDays(7)->toDateString(),
                'time' => Carbon::now()->addDays(7)->setTime(9, 0, 0),
                'location' => 'Pescaito Beach',
                'status' => 'Programado',
                'capacity' => 120,
                'has_parking' => true,
                'parking_slots' => 40,
                'user_email' => 'admin@example.com',
            ],
            [
                'name' => 'Capacitacion de Coordinadores',
                'description' => 'Sesion de capacitacion para responsables de eventos.',
                'date' => Carbon::now()->addDays(12)->toDateString(),
                'time' => Carbon::now()->addDays(12)->setTime(14, 30, 0),
                'location' => 'Sala de Juntas B',
                'status' => 'Confirmado',
                'capacity' => 35,
                'has_parking' => false,
                'parking_slots' => null,
                'user_email' => 'coordinacion@example.com',
            ],
            [
                'name' => 'Feria de Bienvenida',
                'description' => 'Actividad para nuevos usuarios y equipos de apoyo.',
                'date' => Carbon::now()->addDays(20)->toDateString(),
                'time' => Carbon::now()->addDays(20)->setTime(10, 0, 0),
                'location' => 'Plaza Central',
                'status' => 'Abierto',
                'capacity' => 250,
                'has_parking' => true,
                'parking_slots' => 90,
                'user_email' => 'usuario@example.com',
            ],
        ];

        foreach ($events as $event) {
            $ownerId = User::query()
                ->where('email', $event['user_email'])
                ->sole()
                ->id;

            Event::updateOrCreate(
                [
                    'name' => $event['name'],
                    'date' => $event['date'],
                ],
                [
                    'description' => $event['description'],
                    'time' => $event['time'],
                    'location' => $event['location'],
                    'status' => $event['status'],
                    'capacity' => $event['capacity'],
                    'has_parking' => $event['has_parking'],
                    'parking_slots' => $event['parking_slots'],
                    'user_id' => $ownerId,
                ],
            );
        }
    }
}
