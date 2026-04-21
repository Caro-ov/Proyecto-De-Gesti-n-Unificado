<?php

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;

function seedRegistrationRoles(): void
{
    Role::query()->updateOrCreate(
        ['name' => 'admin'],
        [
            'description' => 'Administrador del sistema',
            'status' => true,
        ],
    );

    Role::query()->updateOrCreate(
        ['name' => 'coordinator'],
        [
            'description' => 'Coordinador de eventos',
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

function registrationEvent(array $overrides = []): Event
{
    $owner = User::factory()->create([
        'role' => 'admin',
    ]);

    return Event::query()->create(array_merge([
        'name' => 'Evento de registro',
        'description' => 'Evento para probar inscripciones',
        'date' => Carbon::now()->addWeek()->toDateString(),
        'time' => Carbon::now()->addWeek()->setTime(10, 0, 0),
        'location' => 'Salon de pruebas',
        'status' => 'programado',
        'capacity' => 2,
        'has_parking' => false,
        'parking_slots' => null,
        'user_id' => $owner->id,
    ], $overrides));
}

test('users can register for an event', function () {
    seedRegistrationRoles();

    $event = registrationEvent();
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $response = $this->actingAs($user)
        ->post(route('events.registrations.store', $event));

    $response->assertRedirect(route('events.show', $event, absolute: false));

    $registration = EventRegistration::query()
        ->where('event_id', $event->id)
        ->where('user_id', $user->id)
        ->first();

    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(EventRegistration::STATUS_REGISTERED);
});

test('users are added to the waitlist when the event has no remaining capacity', function () {
    seedRegistrationRoles();

    $event = registrationEvent([
        'capacity' => 1,
    ]);

    $registeredUser = User::factory()->create([
        'role' => 'user',
    ]);

    EventRegistration::query()->create([
        'event_id' => $event->id,
        'user_id' => $registeredUser->id,
        'status' => EventRegistration::STATUS_REGISTERED,
        'registered_at' => now()->subHour(),
    ]);

    $waitlistedUser = User::factory()->create([
        'role' => 'user',
    ]);

    $this->actingAs($waitlistedUser)
        ->post(route('events.registrations.store', $event))
        ->assertRedirect(route('events.show', $event, absolute: false))
        ->assertSessionHasErrors([
            'registration' => 'El evento ya no tiene cupos disponibles. Tu inscripción quedó registrada en lista de espera y te avisaremos si se libera un lugar.',
        ]);

    expect(
        EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('user_id', $waitlistedUser->id)
            ->value('status')
    )->toBe(EventRegistration::STATUS_WAITLIST);
});

test('cancelling a confirmed registration promotes the first waitlisted user', function () {
    seedRegistrationRoles();

    $event = registrationEvent([
        'capacity' => 1,
    ]);

    $registeredUser = User::factory()->create([
        'role' => 'user',
    ]);

    $waitlistedUser = User::factory()->create([
        'role' => 'user',
    ]);

    $registered = EventRegistration::query()->create([
        'event_id' => $event->id,
        'user_id' => $registeredUser->id,
        'status' => EventRegistration::STATUS_REGISTERED,
        'registered_at' => now()->subHours(2),
    ]);

    $waitlisted = EventRegistration::query()->create([
        'event_id' => $event->id,
        'user_id' => $waitlistedUser->id,
        'status' => EventRegistration::STATUS_WAITLIST,
        'registered_at' => now()->subHour(),
    ]);

    $this->actingAs($registeredUser)
        ->delete(route('events.registrations.destroy', [$event, $registered]))
        ->assertRedirect(route('events.show', $event, absolute: false));

    expect($registered->fresh()->status)->toBe(EventRegistration::STATUS_CANCELLED);
    expect($waitlisted->fresh()->status)->toBe(EventRegistration::STATUS_REGISTERED);
});

test('coordinators can update the status of an event registration', function () {
    seedRegistrationRoles();

    $event = registrationEvent();
    $coordinator = User::factory()->create([
        'role' => 'coordinator',
    ]);
    $attendee = User::factory()->create([
        'role' => 'user',
    ]);

    $registration = EventRegistration::query()->create([
        'event_id' => $event->id,
        'user_id' => $attendee->id,
        'status' => EventRegistration::STATUS_REGISTERED,
        'registered_at' => now()->subHour(),
    ]);

    $this->actingAs($coordinator)
        ->patch(route('events.registrations.update', [$event, $registration]), [
            'status' => EventRegistration::STATUS_ATTENDED,
            'notes' => 'Ingreso confirmado en puerta',
        ])
        ->assertRedirect(route('events.show', $event, absolute: false));

    $registration->refresh();

    expect($registration->status)->toBe(EventRegistration::STATUS_ATTENDED);
    expect($registration->attended_at)->not->toBeNull();
    expect($registration->notes)->toBe('Ingreso confirmado en puerta');
});

test('regular users can not update registrations that are not theirs', function () {
    seedRegistrationRoles();

    $event = registrationEvent();
    $owner = User::factory()->create([
        'role' => 'user',
    ]);
    $otherUser = User::factory()->create([
        'role' => 'user',
    ]);

    $registration = EventRegistration::query()->create([
        'event_id' => $event->id,
        'user_id' => $owner->id,
        'status' => EventRegistration::STATUS_REGISTERED,
        'registered_at' => now()->subHour(),
    ]);

    $this->actingAs($otherUser)
        ->patch(route('events.registrations.update', [$event, $registration]), [
            'status' => EventRegistration::STATUS_CANCELLED,
        ])
        ->assertForbidden();
});
