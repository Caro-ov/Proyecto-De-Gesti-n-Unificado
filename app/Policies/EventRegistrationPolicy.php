<?php

namespace App\Policies;

use App\Auth\Capability;
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
        return $user->hasCapability(Capability::REGISTRATIONS_VIEW_ANY);
    }

    /**
     * Determine whether the user can create a registration for an event.
     */
    public function create(User $user, Event $event): bool
    {
        return $user->hasCapability(Capability::REGISTRATIONS_CREATE);
    }

    /**
     * Determine whether the user can view a specific registration.
     */
    public function view(User $user, EventRegistration $registration): bool
    {
        return ($user->id === $registration->user_id
            && $user->hasCapability(Capability::REGISTRATIONS_VIEW_OWN))
            || $user->hasCapability(Capability::REGISTRATIONS_VIEW_ANY);
    }

    /**
     * Determine whether the user can update a registration.
     */
    public function update(User $user, EventRegistration $registration): bool
    {
        return $user->hasCapability(Capability::REGISTRATIONS_UPDATE_ANY);
    }

    /**
     * Determine whether the user can cancel a registration.
     */
    public function delete(User $user, EventRegistration $registration): bool
    {
        return ($user->id === $registration->user_id
            && $user->hasCapability(Capability::REGISTRATIONS_DELETE_OWN))
            || $user->hasCapability(Capability::REGISTRATIONS_DELETE_ANY);
    }
}
