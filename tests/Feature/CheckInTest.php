<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    public function test_valid_qr_token_marks_attendance(): void
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create([
            'capacity' => 10,
            'user_id' => $organizer->id,
        ]);
        $registration = EventRegistration::factory()->registered()->create([
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($organizer)
            ->postJson(
                route('admin.events.check-in.store', $event),
                ['qr_token' => $registration->qr_token]
            );

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $registration->refresh();
        $this->assertEquals(EventRegistration::STATUS_ATTENDED, $registration->status);
        $this->assertNotNull($registration->checked_in_at);
    }

    public function test_invalid_qr_token_returns_error(): void
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create([
            'capacity' => 10,
            'user_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)
            ->postJson(
                route('admin.events.check-in.store', $event),
                ['qr_token' => 'invalid-token']
            );

        $response->assertStatus(422);
        $response->assertJsonPath('error', 'Código QR no válido');
    }

    public function test_already_checked_in_qr_is_rejected(): void
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create([
            'capacity' => 10,
            'user_id' => $organizer->id,
        ]);
        $registration = EventRegistration::factory()->attended()->create([
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($organizer)
            ->postJson(
                route('admin.events.check-in.store', $event),
                ['qr_token' => $registration->qr_token]
            );

        $response->assertStatus(422);
        $response->assertJsonPath('error', 'Este código QR ya fue validado');
    }

    public function test_qr_from_different_event_is_rejected(): void
    {
        $organizer = User::factory()->create();
        $event1 = Event::factory()->create([
            'capacity' => 10,
            'user_id' => $organizer->id,
        ]);
        $event2 = Event::factory()->create(['capacity' => 10]);

        $registration = EventRegistration::factory()->registered()->create([
            'event_id' => $event2->id,
        ]);

        $response = $this->actingAs($organizer)
            ->postJson(
                route('admin.events.check-in.store', $event1),
                ['qr_token' => $registration->qr_token]
            );

        $response->assertStatus(422);
        $response->assertJsonPath('error', 'El código QR no corresponde a este evento');
    }

    public function test_attendance_report_shows_correct_stats(): void
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create([
            'capacity' => 10,
            'user_id' => $organizer->id,
        ]);

        EventRegistration::factory()->registered()->count(3)->create([
            'event_id' => $event->id,
        ]);

        EventRegistration::factory()->attended()->count(2)->create([
            'event_id' => $event->id,
        ]);

        EventRegistration::factory()->cancelled()->create([
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($organizer)
            ->get(route('admin.events.attendance', $event));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('Events/AttendanceReport')
                ->has('stats', fn ($stats) => $stats
                    ->where('total_registered', 3)
                    ->where('total_attended', 2)
                    ->where('total_cancelled', 1)
                )
        );
    }
}
