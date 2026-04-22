<?php

namespace App\Policies;

use App\Auth\Capability;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Grant administrators full access to event actions.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasCapability(Capability::SYSTEM_MANAGE_ALL)) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasCapability(Capability::EVENTS_VIEW);
    }

    /**
     * Determine whether the user can view an event.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->hasCapability(Capability::EVENTS_VIEW);
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return $user->hasCapability(Capability::EVENTS_CREATE);
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->hasCapability(Capability::EVENTS_UPDATE);
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        return false;
    }
}
