<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
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
                'name' => 'Jornada de Integracion',
                'description' => 'Evento abierto para demostrar el flujo principal de inscripcion.',
                'date' => Carbon::now()->addDays(7)->toDateString(),
                'time' => Carbon::now()->addDays(7)->setTime(9, 0, 0),
                'location' => 'Auditorio Central',
                'status' => EventStatus::OPEN->value,
                'capacity' => 120,
                'has_parking' => true,
                'parking_slots' => 40,
                'user_email' => 'admin@example.com',
            ],
            [
                'name' => 'Capacitacion Operativa',
                'description' => 'Evento activo con gestion desde backoffice.',
                'date' => Carbon::now()->addDays(12)->toDateString(),
                'time' => Carbon::now()->addDays(12)->setTime(14, 30, 0),
                'location' => 'Sala de Juntas B',
                'status' => EventStatus::ACTIVE->value,
                'capacity' => 35,
                'has_parking' => false,
                'parking_slots' => null,
                'user_email' => 'coordinacion@example.com',
            ],
            [
                'name' => 'Reunion Ejecutiva Cerrada',
                'description' => 'Evento cerrado que debe bloquear nuevas inscripciones.',
                'date' => Carbon::now()->addDays(15)->toDateString(),
                'time' => Carbon::now()->addDays(15)->setTime(8, 30, 0),
                'location' => 'Sala de Consejo',
                'status' => EventStatus::CLOSED->value,
                'capacity' => 20,
                'has_parking' => false,
                'parking_slots' => null,
                'user_email' => 'admin@example.com',
            ],
            [
                'name' => 'Evento Institucional Cancelado',
                'description' => 'Evento cancelado para validar restricciones y mensajes del sistema.',
                'date' => Carbon::now()->addDays(18)->toDateString(),
                'time' => Carbon::now()->addDays(18)->setTime(16, 0, 0),
                'location' => 'Coliseo Cubierto',
                'status' => EventStatus::CANCELLED->value,
                'capacity' => 80,
                'has_parking' => true,
                'parking_slots' => 25,
                'user_email' => 'coordinacion@example.com',
            ],
            [
                'name' => 'Taller con Cupo Limitado',
                'description' => 'Evento abierto con capacidad completa para probar lista de espera.',
                'date' => Carbon::now()->addDays(20)->toDateString(),
                'time' => Carbon::now()->addDays(20)->setTime(10, 0, 0),
                'location' => 'Laboratorio 3',
                'status' => EventStatus::OPEN->value,
                'capacity' => 1,
                'has_parking' => false,
                'parking_slots' => null,
                'user_email' => 'usuario@example.com',
            ],
            [
                'name' => 'Evento Historico Cerrado',
                'description' => 'Evento ya realizado con asistencia confirmada para revisar historicos.',
                'date' => Carbon::now()->subDays(10)->toDateString(),
                'time' => Carbon::now()->subDays(10)->setTime(11, 0, 0),
                'location' => 'Teatro Municipal',
                'status' => EventStatus::CLOSED->value,
                'capacity' => 60,
                'has_parking' => true,
                'parking_slots' => 15,
                'user_email' => 'admin@example.com',
            ],
            [
                'name' => 'Feria de Bienvenida',
                'description' => 'Actividad para nuevos usuarios y equipos de apoyo.',
                'date' => Carbon::now()->addDays(20)->toDateString(),
                'time' => Carbon::now()->addDays(20)->setTime(10, 0, 0),
                'location' => 'Plaza Central',
                'status' => EventStatus::OPEN->value,
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
