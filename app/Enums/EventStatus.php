<?php

namespace App\Enums;

enum EventStatus: string
{
    case ACTIVE = 'activo';
    case OPEN = 'abierto';
    case CLOSED = 'cerrado';
    case CANCELLED = 'cancelado';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            self::cases(),
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Activo',
            self::OPEN => 'Abierto',
            self::CLOSED => 'Cerrado',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function acceptsRegistrations(): bool
    {
        return in_array($this, [self::ACTIVE, self::OPEN], true);
    }

    public function registrationRestrictionMessage(): ?string
    {
        return match ($this) {
            self::CANCELLED => 'No puedes inscribirte porque el evento esta cancelado.',
            self::CLOSED => 'No puedes inscribirte porque el evento esta cerrado.',
            self::ACTIVE, self::OPEN => null,
        };
    }

    public static function normalize(string $status): self
    {
        return match (mb_strtolower(trim($status))) {
            'activo', 'active', 'publicado', 'confirmado' => self::ACTIVE,
            'abierto', 'open', 'programado' => self::OPEN,
            'cerrado', 'closed' => self::CLOSED,
            'cancelado', 'cancelled' => self::CANCELLED,
            default => self::OPEN,
        };
    }
}
