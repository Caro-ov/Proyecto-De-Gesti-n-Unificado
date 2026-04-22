<?php

use App\Auth\Capability;
use App\Models\Role;
use App\Models\User;

function seedCapabilityRoles(): void
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

test('capabilities are resolved from the central role map', function () {
    seedCapabilityRoles();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $coordinator = User::factory()->create([
        'role' => 'coordinator',
    ]);

    $user = User::factory()->create([
        'role' => 'user',
    ]);

    expect($admin->hasCapability(Capability::BACKOFFICE_ACCESS))->toBeTrue()
        ->and($admin->hasCapability(Capability::EVENTS_DELETE))->toBeTrue()
        ->and($coordinator->hasCapability(Capability::BACKOFFICE_ACCESS))->toBeTrue()
        ->and($coordinator->hasCapability(Capability::EVENTS_DELETE))->toBeFalse()
        ->and($user->hasCapability(Capability::BACKOFFICE_ACCESS))->toBeFalse()
        ->and($user->hasCapability(Capability::REGISTRATIONS_CREATE))->toBeTrue();
});

test('inactive roles invalidate capabilities even if the role exists in the map', function () {
    Role::query()->updateOrCreate(
        ['name' => 'coordinator'],
        [
            'description' => 'Coordinador de eventos',
            'status' => false,
        ],
    );

    $user = User::factory()->create([
        'role' => 'coordinator',
    ]);

    expect($user->hasCapability(Capability::BACKOFFICE_ACCESS))->toBeFalse()
        ->and($user->hasCapability(Capability::EVENTS_CREATE))->toBeFalse();
});
