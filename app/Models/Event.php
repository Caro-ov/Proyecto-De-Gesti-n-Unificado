<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'date',
        'time',
        'location',
        'status',
        'capacity',
        'has_parking',
        'parking_slots',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time' => 'datetime',
            'status' => EventStatus::class,
            'has_parking' => 'boolean',
            'capacity' => 'integer',
            'parking_slots' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registrations')
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

    public function confirmedRegistrationsCount(?int $excludeRegistrationId = null): int
    {
        return $this->registrations()
            ->when($excludeRegistrationId !== null, fn ($query) => $query->whereKeyNot($excludeRegistrationId))
            ->whereIn('status', [
                EventRegistration::STATUS_REGISTERED,
                EventRegistration::STATUS_ATTENDED,
            ])
            ->count();
    }

    public function remainingCapacity(?int $excludeRegistrationId = null): int
    {
        return max(0, $this->capacity - $this->confirmedRegistrationsCount($excludeRegistrationId));
    }

    public function statusEnum(): ?EventStatus
    {
        if ($this->status instanceof EventStatus) {
            return $this->status;
        }

        return EventStatus::tryFrom((string) $this->status);
    }

    public function acceptsRegistrations(): bool
    {
        return $this->statusEnum()?->acceptsRegistrations() ?? false;
    }

    public function registrationRestrictionMessage(): ?string
    {
        return $this->statusEnum()?->registrationRestrictionMessage()
            ?? 'Las inscripciones solo estan disponibles para eventos activos o abiertos.';
    }

    public function statusLabel(): string
    {
        return $this->statusEnum()?->label()
            ?? ucfirst((string) $this->status);
    }
}
