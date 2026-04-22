<?php

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Role;
use App\Models\User;

test('database seeder loads roles users and events', function () {
    $this->seed();

    expect(Role::count())->toBe(3);
    expect(User::count())->toBe(8);
    expect(Event::count())->toBe(7);
    expect(EventRegistration::count())->toBe(6);
    expect(User::where('email', 'admin@example.com')->value('role'))->toBe('admin');
    expect(User::where('email', 'inactivo@example.com')->value('status'))->toBeFalse();
    expect(
        Event::query()
            ->whereHas('user', fn ($query) => $query->where('email', 'admin@example.com'))
            ->exists()
    )->toBeTrue();
    expect(
        EventRegistration::query()
            ->whereHas('event', fn ($query) => $query->where('name', 'Jornada de Integracion'))
            ->whereHas('user', fn ($query) => $query->where('email', 'usuario@example.com'))
            ->exists()
    )->toBeTrue();
    expect(
        Event::query()
            ->get()
            ->map(fn (Event $event) => $event->status?->value ?? $event->status)
            ->all()
    )
        ->toContain(EventStatus::ACTIVE->value, EventStatus::OPEN->value, EventStatus::CLOSED->value, EventStatus::CANCELLED->value);
    expect(EventRegistration::query()->pluck('status')->all())
        ->toContain(
            EventRegistration::STATUS_REGISTERED,
            EventRegistration::STATUS_WAITLIST,
            EventRegistration::STATUS_CANCELLED,
            EventRegistration::STATUS_ATTENDED,
        );
    expect(
        EventRegistration::query()
            ->whereHas('event', fn ($query) => $query->where('name', 'Taller con Cupo Limitado'))
            ->where('status', EventRegistration::STATUS_WAITLIST)
            ->exists()
    )->toBeTrue();
});
