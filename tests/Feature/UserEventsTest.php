<?php

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedUserEventsRoles(): void
{
    Role::query()->updateOrCreate(
        ['name' => 'admin'],
        [
            'description' => 'Administrador del sistema',
            'status' => true,
        ],
    );

    Role::query()->updateOrCreate(
        ['name' => 'user'],
        [
            'description' => 'Usuario regular',
            'status' => true,
        ],
    );
}

function userEventsEvent(array $overrides = []): Event
{
    $owner = User::factory()->create([
        'role' => 'admin',
    ]);

    return Event::query()->create(array_merge([
        'name' => 'Evento para usuario',
        'description' => 'Evento de prueba para el listado por usuario',
        'date' => Carbon::now()->addWeek()->toDateString(),
        'time' => Carbon::now()->addWeek()->setTime(9, 0, 0),
        'location' => 'Sala principal',
        'status' => 'programado',
        'capacity' => 30,
        'has_parking' => false,
        'parking_slots' => null,
        'user_id' => $owner->id,
    ], $overrides));
}

test('users can see only their registered events', function () {
    seedUserEventsRoles();

    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $firstEvent = userEventsEvent([
        'name' => 'Evento asociado 1',
    ]);

    $secondEvent = userEventsEvent([
        'name' => 'Evento asociado 2',
    ]);

    $otherEvent = userEventsEvent([
        'name' => 'Evento ajeno',
    ]);

    EventRegistration::query()->create([
        'event_id' => $firstEvent->id,
        'user_id' => $user->id,
        'status' => EventRegistration::STATUS_REGISTERED,
        'registered_at' => now()->subDay(),
    ]);

    EventRegistration::query()->create([
        'event_id' => $secondEvent->id,
        'user_id' => $user->id,
        'status' => EventRegistration::STATUS_WAITLIST,
        'registered_at' => now()->subHour(),
    ]);

    EventRegistration::query()->create([
        'event_id' => userEventsEvent([
            'name' => 'Evento cancelado',
        ])->id,
        'user_id' => $user->id,
        'status' => EventRegistration::STATUS_CANCELLED,
        'registered_at' => now()->subMinutes(45),
    ]);

    EventRegistration::query()->create([
        'event_id' => $otherEvent->id,
        'user_id' => User::factory()->create([
            'role' => 'user',
        ])->id,
        'status' => EventRegistration::STATUS_REGISTERED,
        'registered_at' => now()->subMinutes(30),
    ]);

    $response = $this->actingAs($user)
        ->get(route('portal.events.mine'));

    $response->assertOk();
    $response->assertSeeText('Evento asociado 1');
    $response->assertSeeText('Evento asociado 2');
    $response->assertDontSeeText('Evento ajeno');
    $response->assertDontSeeText('Evento cancelado');
    $response->assertSeeText('Inscrito');
    $response->assertSeeText('Lista de espera');
});
