<?php

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Role;
use App\Models\User;

test('database seeder loads roles users and events', function () {
    $this->seed();

    expect(Role::count())->toBe(3);
    expect(User::count())->toBe(4);
    expect(Event::count())->toBe(3);
    expect(EventRegistration::count())->toBe(2);
    expect(User::where('email', 'admin@example.com')->value('role'))->toBe('admin');
    expect(
        Event::query()
            ->whereHas('user', fn ($query) => $query->where('email', 'admin@example.com'))
            ->exists()
    )->toBeTrue();
    expect(
        EventRegistration::query()
            ->whereHas('event', fn ($query) => $query->where('name', 'La ultima gran party'))
            ->whereHas('user', fn ($query) => $query->where('email', 'usuario@example.com'))
            ->exists()
    )->toBeTrue();
});
