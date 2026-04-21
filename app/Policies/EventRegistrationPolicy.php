<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;

class EventRegistrationPolicy
{
    /**
     * Determine whether the user can view registrations for an event.
     */
    public function viewAny(User $user, Event $event): bool
    {
        return $user->hasAnyActiveRole(['admin', 'coordinator']);
    }

    /**
     * Determine whether the user can create a registration for an event.
     */
    public function create(User $user, Event $event): bool
    {
        return $user->hasActiveRole();
    }

    /**
     * Determine whether the user can view a specific registration.
     */
    public function view(User $user, EventRegistration $registration): bool
    {
        return $user->id === $registration->user_id
            || $user->hasAnyActiveRole(['admin', 'coordinator']);
    }

    /**
     * Determine whether the user can update a registration.
     */
    public function update(User $user, EventRegistration $registration): bool
    {
        return $user->hasAnyActiveRole(['admin', 'coordinator']);
    }

    /**
     * Determine whether the user can cancel a registration.
     */
    public function delete(User $user, EventRegistration $registration): bool
    {
        return $user->id === $registration->user_id
            || $user->hasAnyActiveRole(['admin', 'coordinator']);
    }
}
