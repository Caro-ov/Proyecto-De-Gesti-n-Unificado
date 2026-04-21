<?php

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;

function seedEventRoles(): void
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

test('regular users can not create events', function () {
    seedEventRoles();

    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $response = $this->actingAs($user)->get('/events/create');

    $response->assertForbidden();
});

test('coordinators can create events', function () {
    seedEventRoles();

    $user = User::factory()->create([
        'role' => 'coordinator',
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/events', [
            'name' => 'Evento autorizado',
            'description' => 'Creado por coordinacion',
            'date' => Carbon::now()->addWeek()->format('Y-m-d'),
            'time' => '10:00',
            'location' => 'Salon A',
            'status' => EventStatus::OPEN->value,
            'capacity' => 50,
            'has_parking' => 0,
            'parking_slots' => null,
        ]);

    $response->assertRedirect(route('events.create', absolute: false));
    expect(Event::query()->where('name', 'Evento autorizado')->exists())->toBeTrue();
});

test('coordinators can edit events but can not delete them', function () {
    seedEventRoles();

    $user = User::factory()->create([
        'role' => 'coordinator',
    ]);

    $event = Event::query()->create([
        'name' => 'Evento editable',
        'description' => 'Evento de prueba',
        'date' => Carbon::now()->addWeek()->toDateString(),
        'time' => Carbon::now()->addWeek()->setTime(11, 0, 0),
        'location' => 'Salon B',
        'status' => EventStatus::OPEN->value,
        'capacity' => 80,
        'has_parking' => false,
        'parking_slots' => null,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('events.edit', $event))
        ->assertOk();

    $this->actingAs($user)
        ->delete(route('events.destroy', $event))
        ->assertForbidden();
});

test('administrators can delete events', function () {
    seedEventRoles();

    $user = User::factory()->create([
        'role' => 'admin',
    ]);

    $event = Event::query()->create([
        'name' => 'Evento eliminable',
        'description' => 'Evento de prueba',
        'date' => Carbon::now()->addWeek()->toDateString(),
        'time' => Carbon::now()->addWeek()->setTime(12, 0, 0),
        'location' => 'Salon C',
        'status' => EventStatus::OPEN->value,
        'capacity' => 100,
        'has_parking' => true,
        'parking_slots' => 20,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('events.destroy', $event))
        ->assertRedirect(route('events.index', absolute: false));

    expect(Event::query()->whereKey($event->id)->exists())->toBeFalse();
});

test('regular users can view events but can not edit them', function () {
    seedEventRoles();

    $creator = User::factory()->create([
        'role' => 'admin',
    ]);

    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $event = Event::query()->create([
        'name' => 'Evento visible',
        'description' => 'Evento para consulta',
        'date' => Carbon::now()->addWeek()->toDateString(),
        'time' => Carbon::now()->addWeek()->setTime(8, 30, 0),
        'location' => 'Salon D',
        'status' => EventStatus::ACTIVE->value,
        'capacity' => 60,
        'has_parking' => false,
        'parking_slots' => null,
        'user_id' => $creator->id,
    ]);

    $this->actingAs($user)
        ->get(route('events.index'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('events.show', $event))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('events.edit', $event))
        ->assertForbidden();
});

test('users with inactive roles are redirected to login from protected routes', function () {
    Role::query()->updateOrCreate(
        ['name' => 'user'],
        [
            'description' => 'Usuario regular',
            'status' => false,
        ],
    );

    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertRedirect(route('login', absolute: false));
    $this->assertGuest();
});
