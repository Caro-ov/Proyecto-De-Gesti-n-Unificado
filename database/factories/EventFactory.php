<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'date' => $this->faker->dateTimeBetween('+1 days', '+90 days')->format('Y-m-d'),
            'time' => $this->faker->dateTimeBetween('08:00', '18:00'),
            'location' => $this->faker->city() . ' - ' . $this->faker->address(),
            'status' => EventStatus::OPEN,
            'capacity' => $this->faker->numberBetween(10, 100),
            'has_parking' => $this->faker->boolean(),
            'parking_slots' => $this->faker->numberBetween(0, 50),
            'user_id' => User::factory(),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::OPEN,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::CLOSED,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::CANCELLED,
        ]);
    }

    public function withCapacity(int $capacity): static
    {
        return $this->state(fn (array $attributes) => [
            'capacity' => $capacity,
        ]);
    }
}
