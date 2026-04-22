<?php

namespace App\Models;

use App\Auth\Capability;
use App\Auth\RoleCapabilities;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * El rol de este usuario
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    /**
     * Determine whether the user has the given role name.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName;
    }

    /**
     * Determine whether the user has any of the given role names.
     *
     * @param array<int, string> $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Determine whether the user account is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    /**
     * Determine whether the user has an active role.
     */
    public function hasActiveRole(?string $roleName = null): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $query = $this->role()->where('status', true);

        if ($roleName !== null) {
            $query->where('name', $roleName);
        }

        return $query->exists();
    }

    /**
     * Determine whether the user has any active role from the given list.
     *
     * @param array<int, string> $roles
     */
    public function hasAnyActiveRole(array $roles): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        return $this->role()
            ->where('status', true)
            ->whereIn('name', $roles)
            ->exists();
    }

    /**
     * Determine whether the user has the given capability through an active role.
     */
    public function hasCapability(Capability|string $capability): bool
    {
        if (! $this->hasActiveRole()) {
            return false;
        }

        return RoleCapabilities::has($this->role, $capability);
    }

    /**
     * Determine whether the user can access the administrative area.
     */
    public function canAccessBackoffice(): bool
    {
        return $this->hasCapability(Capability::BACKOFFICE_ACCESS);
    }

    /**
     * Los eventos creados por este usuario
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Las inscripciones del usuario a eventos.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Los eventos a los que el usuario se ha inscrito.
     */
    public function registeredEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_registrations')
            ->withPivot([
                'id',
                'status',
                'registered_at',
                'cancelled_at',
                'attended_at',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }
}
