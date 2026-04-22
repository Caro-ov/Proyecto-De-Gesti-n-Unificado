<?php

namespace App\Auth;

class RoleCapabilities
{
    /**
     * Return the capabilities assigned to a role.
     *
     * @return array<int, Capability>
     */
    public static function forRole(?string $roleName): array
    {
        return match ($roleName) {
            'admin' => [
                Capability::SYSTEM_MANAGE_ALL,
                Capability::BACKOFFICE_ACCESS,
                Capability::EVENTS_VIEW,
                Capability::EVENTS_CREATE,
                Capability::EVENTS_UPDATE,
                Capability::EVENTS_DELETE,
                Capability::REGISTRATIONS_VIEW_ANY,
                Capability::REGISTRATIONS_VIEW_OWN,
                Capability::REGISTRATIONS_CREATE,
                Capability::REGISTRATIONS_UPDATE_ANY,
                Capability::REGISTRATIONS_DELETE_ANY,
                Capability::REGISTRATIONS_DELETE_OWN,
                Capability::USERS_VIEW,
                Capability::USERS_CREATE,
                Capability::USERS_UPDATE,
                Capability::USERS_ASSIGN_ROLE,
            ],
            'coordinator' => [
                Capability::BACKOFFICE_ACCESS,
                Capability::EVENTS_VIEW,
                Capability::EVENTS_CREATE,
                Capability::EVENTS_UPDATE,
                Capability::REGISTRATIONS_VIEW_ANY,
                Capability::REGISTRATIONS_VIEW_OWN,
                Capability::REGISTRATIONS_CREATE,
                Capability::REGISTRATIONS_UPDATE_ANY,
                Capability::REGISTRATIONS_DELETE_ANY,
                Capability::REGISTRATIONS_DELETE_OWN,
            ],
            'user' => [
                Capability::EVENTS_VIEW,
                Capability::REGISTRATIONS_VIEW_OWN,
                Capability::REGISTRATIONS_CREATE,
                Capability::REGISTRATIONS_DELETE_OWN,
            ],
            default => [],
        };
    }

    public static function has(?string $roleName, Capability|string $capability): bool
    {
        $resolvedCapability = $capability instanceof Capability
            ? $capability
            : Capability::tryFrom($capability);

        if ($resolvedCapability === null) {
            return false;
        }

        return in_array($resolvedCapability, static::forRole($roleName), true);
    }
}
