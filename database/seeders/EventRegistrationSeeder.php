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
                'event_name' => 'La ultima gran party',
                'user_email' => 'usuario@example.com',
                'status' => EventRegistration::STATUS_REGISTERED,
                'notes' => 'Registro inicial de prueba',
            ],
            [
                'event_name' => 'Capacitacion de Coordinadores',
                'user_email' => 'admin@example.com',
                'status' => EventRegistration::STATUS_ATTENDED,
                'notes' => 'Asistencia confirmada',
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
                    'registered_at' => now()->subDays(2),
                    'cancelled_at' => null,
                    'attended_at' => $registration['status'] === EventRegistration::STATUS_ATTENDED ? now()->subDay() : null,
                    'notes' => $registration['notes'],
                ],
            );
        }
    }
}
