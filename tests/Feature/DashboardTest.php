<?php

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Role;
use App\Models\User;

test('admin can view the dashboard with basic metrics', function () {
    Role::query()->create([
        'name' => 'admin',
        'description' => 'Administrador del sistema',
        'status' => true,
    ]);

    Role::query()->create([
        'name' => 'user',
        'description' => 'Usuario regular',
        'status' => true,
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $assistantUser = User::factory()->create([
        'role' => 'user',
    ]);

    $event = Event::query()->create([
        'name' => 'Lanzamiento del portal',
        'description' => 'Presentacion general del sistema',
        'date' => now()->addDays(3)->toDateString(),
        'time' => now()->addDays(3)->setTime(10, 30),
        'location' => 'Auditorio principal',
        'status' => EventStatus::ACTIVE->value,
        'capacity' => 80,
        'has_parking' => false,
        'parking_slots' => null,
        'user_id' => $admin->id,
    ]);

    EventRegistration::query()->create([
        'event_id' => $event->id,
        'user_id' => $assistantUser->id,
        'status' => EventRegistration::STATUS_REGISTERED,
        'registered_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSeeText('Vista general del sistema');
    $response->assertSeeText('Total usuarios');
    $response->assertSeeText('2');
    $response->assertSeeText('Lanzamiento del portal');
});

test('regular users can view the portal dashboard', function () {
    Role::query()->create([
        'name' => 'user',
        'description' => 'Usuario regular',
        'status' => true,
    ]);

    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $response = $this->actingAs($user)->get(route('portal.dashboard'));

    $response->assertOk();
    $response->assertSeeText('Mi portal');
});
