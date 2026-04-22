<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventRegistrationSeeder extends Seeder
{
    /**
     * Seed the application's event registrations.
     */
    public function run(): void
    {
        $registrations = [
            [
                'event_name' => 'Jornada de Integracion',
                'user_email' => 'usuario@example.com',
                'status' => EventRegistration::STATUS_REGISTERED,
                'registered_at' => now()->subDays(2),
                'cancelled_at' => null,
                'attended_at' => null,
                'notes' => 'Registro confirmado para el flujo base',
            ],
            [
                'event_name' => 'Jornada de Integracion',
                'user_email' => 'cancelado@example.com',
                'status' => EventRegistration::STATUS_CANCELLED,
                'registered_at' => now()->subDays(4),
                'cancelled_at' => now()->subDays(1),
                'attended_at' => null,
                'notes' => 'Cancelado por cambio de agenda',
            ],
            [
                'event_name' => 'Capacitacion Operativa',
                'user_email' => 'coordinacion@example.com',
                'status' => EventRegistration::STATUS_REGISTERED,
                'registered_at' => now()->subDay(),
                'cancelled_at' => null,
                'attended_at' => null,
                'notes' => 'Coordinacion inscrita para seguimiento',
            ],
            [
                'event_name' => 'Taller con Cupo Limitado',
                'user_email' => 'usuario2@example.com',
                'status' => EventRegistration::STATUS_REGISTERED,
                'registered_at' => now()->subHours(8),
                'cancelled_at' => null,
                'attended_at' => null,
                'notes' => 'Ocupa el unico cupo disponible',
            ],
            [
                'event_name' => 'Taller con Cupo Limitado',
                'user_email' => 'listaespera@example.com',
                'status' => EventRegistration::STATUS_WAITLIST,
                'registered_at' => now()->subHours(4),
                'cancelled_at' => null,
                'attended_at' => null,
                'notes' => 'Pendiente por liberacion de cupo',
            ],
            [
                'event_name' => 'Evento Historico Cerrado',
                'user_email' => 'historico@example.com',
                'status' => EventRegistration::STATUS_ATTENDED,
                'registered_at' => now()->subDays(12),
                'cancelled_at' => null,
                'attended_at' => now()->subDays(10),
                'notes' => 'Asistencia historica confirmada',
            ],
        ];

        foreach ($registrations as $registration) {
            $eventId = Event::query()
                ->where('name', $registration['event_name'])
                ->sole()
                ->id;

            $userId = User::query()
                ->where('email', $registration['user_email'])
                ->sole()
                ->id;

            EventRegistration::query()->updateOrCreate(
                [
                    'event_id' => $eventId,
                    'user_id' => $userId,
                ],
                [
                    'status' => $registration['status'],
                    'registered_at' => $registration['registered_at'],
                    'cancelled_at' => $registration['cancelled_at'],
                    'attended_at' => $registration['attended_at'],
                    'notes' => $registration['notes'],
                ],
            );
        }
    }
}
