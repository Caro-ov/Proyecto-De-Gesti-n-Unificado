<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Grant administrators full access to event actions.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasActiveRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasActiveRole();
    }

    /**
     * Determine whether the user can view an event.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->hasActiveRole();
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyActiveRole(['admin', 'coordinator']);
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->hasAnyActiveRole(['admin', 'coordinator']);
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        return false;
    }
}
