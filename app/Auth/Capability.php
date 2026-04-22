<?php

namespace App\Auth;

enum Capability: string
{
    case SYSTEM_MANAGE_ALL = 'system.manage_all';
    case BACKOFFICE_ACCESS = 'backoffice.access';
    case EVENTS_VIEW = 'events.view';
    case EVENTS_CREATE = 'events.create';
    case EVENTS_UPDATE = 'events.update';
    case EVENTS_DELETE = 'events.delete';
    case REGISTRATIONS_VIEW_ANY = 'registrations.view_any';
    case REGISTRATIONS_VIEW_OWN = 'registrations.view_own';
    case REGISTRATIONS_CREATE = 'registrations.create';
    case REGISTRATIONS_UPDATE_ANY = 'registrations.update_any';
    case REGISTRATIONS_DELETE_ANY = 'registrations.delete_any';
    case REGISTRATIONS_DELETE_OWN = 'registrations.delete_own';
    case USERS_VIEW = 'users.view';
    case USERS_CREATE = 'users.create';
    case USERS_UPDATE = 'users.update';
    case USERS_ASSIGN_ROLE = 'users.assign_role';
}
