<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    public const STATUS_REGISTERED = 'registered';

    public const STATUS_WAITLIST = 'waitlist';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_ATTENDED = 'attended';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'registered_at',
        'cancelled_at',
        'attended_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'user_id' => 'integer',
            'registered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'attended_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_REGISTERED,
            self::STATUS_WAITLIST,
            self::STATUS_CANCELLED,
            self::STATUS_ATTENDED,
        ];
    }

    public function statusLabel(): string
    {
        return self::labelFor($this->status);
    }

    public static function labelFor(string $status): string
    {
        return match ($status) {
            self::STATUS_REGISTERED => 'Inscrito',
            self::STATUS_WAITLIST => 'Lista de espera',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_ATTENDED => 'Asistio',
            default => ucfirst($status),
        };
    }

    /**
     * Determine whether the registration consumes a seat.
     */
    public function usesCapacity(): bool
    {
        return in_array($this->status, [
            self::STATUS_REGISTERED,
            self::STATUS_ATTENDED,
        ], true);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
