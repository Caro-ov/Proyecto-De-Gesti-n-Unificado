<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_is_required_when_creating_an_event(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->from('/events/create')
            ->post('/events', $this->validEventPayload(['name' => '']));

        $response
            ->assertSessionHasErrors(['name'])
            ->assertRedirect('/events/create');
    }

    public function test_date_is_required_when_creating_an_event(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->from('/events/create')
            ->post('/events', $this->validEventPayload(['date' => '']));

        $response
            ->assertSessionHasErrors(['date'])
            ->assertRedirect('/events/create');
    }

    public function test_capacity_must_be_greater_than_zero_when_creating_an_event(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->from('/events/create')
            ->post('/events', $this->validEventPayload(['capacity' => 0]));

        $response
            ->assertSessionHasErrors(['capacity'])
            ->assertRedirect('/events/create');
    }

    public function test_parking_slots_are_prohibited_when_parking_is_disabled(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->from('/events/create')
            ->post('/events', $this->validEventPayload([
                'has_parking' => 0,
                'parking_slots' => 10,
            ]));

        $response
            ->assertSessionHasErrors(['parking_slots'])
            ->assertRedirect('/events/create');
    }

    public function test_parking_slots_are_required_when_parking_is_enabled(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->from('/events/create')
            ->post('/events', $this->validEventPayload([
                'has_parking' => 1,
                'parking_slots' => null,
            ]));

        $response
            ->assertSessionHasErrors(['parking_slots'])
            ->assertRedirect('/events/create');
    }

    public function test_parking_slots_must_be_greater_than_zero_when_parking_is_enabled(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->from('/events/create')
            ->post('/events', $this->validEventPayload([
                'has_parking' => 1,
                'parking_slots' => 0,
            ]));

        $response
            ->assertSessionHasErrors(['parking_slots'])
            ->assertRedirect('/events/create');
    }

    public function test_event_datetime_cannot_be_in_the_past_when_creating_an_event(): void
    {
        $user = $this->authenticatedUser();
        $pastDateTime = Carbon::now()->subHour();

        $response = $this
            ->actingAs($user)
            ->from('/events/create')
            ->post('/events', $this->validEventPayload([
                'date' => $pastDateTime->format('Y-m-d'),
                'time' => $pastDateTime->format('H:i'),
            ]));

        $response
            ->assertSessionHasErrors(['time'])
            ->assertRedirect('/events/create');
    }

    public function test_status_must_be_a_supported_enum_value_when_creating_an_event(): void
    {
        $user = $this->authenticatedUser();

        $response = $this
            ->actingAs($user)
            ->from('/events/create')
            ->post('/events', $this->validEventPayload([
                'status' => 'programado',
            ]));

        $response
            ->assertSessionHasErrors(['status'])
            ->assertRedirect('/events/create');
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validEventPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Evento de prueba',
            'description' => 'Descripcion opcional',
            'date' => '2026-05-20',
            'time' => '14:30',
            'location' => 'Auditorio principal',
            'status' => EventStatus::OPEN->value,
            'capacity' => 120,
            'has_parking' => 1,
            'parking_slots' => 30,
        ], $overrides);
    }

    private function authenticatedUser(): User
    {
        Role::query()->firstOrCreate(
            ['name' => 'coordinator'],
            [
                'description' => 'Coordinador de eventos',
                'status' => true,
            ],
        );

        return User::factory()->create([
            'role' => 'coordinator',
        ]);
    }
}
