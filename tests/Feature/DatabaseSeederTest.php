<?php

use App\Models\Event;
use App\Models\Role;
use App\Models\User;

test('database seeder loads roles users and events', function () {
    $this->seed();

    expect(Role::count())->toBe(3);
    expect(User::count())->toBe(4);
    expect(Event::count())->toBe(3);
    expect(User::where('email', 'admin@example.com')->value('role'))->toBe('admin');
    expect(
        Event::query()
            ->whereHas('user', fn ($query) => $query->where('email', 'admin@example.com'))
            ->exists()
    )->toBeTrue();
});
