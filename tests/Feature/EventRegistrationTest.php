<?php

use App\Enums\EventStatus;
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
        'status' => EventStatus::OPEN->value,
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
        ->post(route('portal.events.registrations.store', $event));

    $response->assertRedirect(route('portal.events.show', $event, absolute: false));

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
        ->post(route('portal.events.registrations.store', $event))
        ->assertRedirect(route('portal.events.show', $event, absolute: false))
        ->assertSessionHasErrors([
            'registration' => 'El evento ya no tiene cupos disponibles. Tu inscripcion quedo registrada en lista de espera y te avisaremos si se libera un lugar.',
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
        ->delete(route('portal.events.registrations.destroy', [$event, $registered]))
        ->assertRedirect(route('portal.events.show', $event, absolute: false));

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
        ->patch(route('admin.events.registrations.update', [$event, $registration]), [
            'status' => EventRegistration::STATUS_ATTENDED,
            'notes' => 'Ingreso confirmado en puerta',
        ])
        ->assertRedirect(route('admin.events.show', $event, absolute: false));

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
        ->patch(route('portal.events.registrations.update', [$event, $registration]), [
            'status' => EventRegistration::STATUS_CANCELLED,
        ])
        ->assertForbidden();
});

test('users can not register for cancelled events', function () {
    seedRegistrationRoles();

    $event = registrationEvent([
        'status' => EventStatus::CANCELLED->value,
    ]);
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $this->from(route('portal.events.show', $event))
        ->actingAs($user)
        ->post(route('portal.events.registrations.store', $event))
        ->assertRedirect(route('portal.events.show', $event, absolute: false))
        ->assertSessionHasErrors([
            'registration' => 'No puedes inscribirte porque el evento esta cancelado.',
        ]);

    expect(
        EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists()
    )->toBeFalse();
});

test('users can not register for closed events', function () {
    seedRegistrationRoles();

    $event = registrationEvent([
        'status' => EventStatus::CLOSED->value,
    ]);
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $this->from(route('portal.events.show', $event))
        ->actingAs($user)
        ->post(route('portal.events.registrations.store', $event))
        ->assertRedirect(route('portal.events.show', $event, absolute: false))
        ->assertSessionHasErrors([
            'registration' => 'No puedes inscribirte porque el evento esta cerrado.',
        ]);

    expect(
        EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists()
    )->toBeFalse();
});

test('event detail shows restriction message instead of the registration button when the event is closed', function () {
    seedRegistrationRoles();

    $event = registrationEvent([
        'status' => EventStatus::CLOSED->value,
    ]);
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $this->actingAs($user)
        ->get(route('portal.events.show', $event))
        ->assertOk()
        ->assertSee('No puedes inscribirte porque el evento esta cerrado.')
        ->assertDontSee('Inscribirme');
});

test('administrators can see the registration button for open events in the admin area', function () {
    seedRegistrationRoles();

    $event = registrationEvent([
        'status' => EventStatus::OPEN->value,
    ]);
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.events.show', $event))
        ->assertOk()
        ->assertSee('Inscribirme')
        ->assertDontSee('Las inscripciones solo estan disponibles para eventos activos o abiertos.');
});

test('coordinators can register for active events from the admin area', function () {
    seedRegistrationRoles();

    $event = registrationEvent([
        'status' => EventStatus::ACTIVE->value,
    ]);
    $coordinator = User::factory()->create([
        'role' => 'coordinator',
    ]);

    $this->actingAs($coordinator)
        ->post(route('admin.events.registrations.store', $event))
        ->assertRedirect(route('admin.events.show', $event, absolute: false))
        ->assertSessionHasNoErrors();

    expect(
        EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('user_id', $coordinator->id)
            ->value('status')
    )->toBe(EventRegistration::STATUS_REGISTERED);
});
