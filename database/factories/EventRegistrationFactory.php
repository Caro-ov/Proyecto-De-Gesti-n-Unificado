<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement([
                EventRegistration::STATUS_REGISTERED,
                EventRegistration::STATUS_WAITLIST,
                EventRegistration::STATUS_CANCELLED,
                EventRegistration::STATUS_ATTENDED,
            ]),
            'registered_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            'cancelled_at' => null,
            'attended_at' => null,
            'checked_in_at' => null,
            'qr_token' => Str::uuid(),
            'notes' => $this->faker->optional()->text(100),
        ];
    }

    public function registered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventRegistration::STATUS_REGISTERED,
            'cancelled_at' => null,
            'attended_at' => null,
            'checked_in_at' => null,
        ]);
    }

    public function attended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventRegistration::STATUS_ATTENDED,
            'attended_at' => now(),
            'checked_in_at' => now(),
            'cancelled_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventRegistration::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'attended_at' => null,
            'checked_in_at' => null,
        ]);
    }

    public function waitlist(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventRegistration::STATUS_WAITLIST,
            'cancelled_at' => null,
            'attended_at' => null,
            'checked_in_at' => null,
        ]);
    }
}
