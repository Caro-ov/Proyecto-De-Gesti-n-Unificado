<?php

namespace Tests\Feature;

use App\Jobs\SendEventChangeNotification;
use App\Jobs\SendRegistrationConfirmation;
use App\Mail\RegistrationConfirmation;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    public function test_registration_confirmation_email_is_queued(): void
    {
        Mail::fake();
        Queue::fake();

        $user = User::factory()->create();
        $event = Event::factory()->create(['capacity' => 10]);

        $this->actingAs($user)
            ->post(route('admin.events.registrations.store', $event))
            ->assertRedirect();

        Queue::assertPushed(SendRegistrationConfirmation::class);
    }

    public function test_registration_has_unique_qr_token(): void
    {
        $event = Event::factory()->create(['capacity' => 20]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $reg1 = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user1->id,
        ]);

        $reg2 = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user2->id,
        ]);

        $this->assertNotEquals($reg1->qr_token, $reg2->qr_token);
    }

    public function test_event_modification_notifies_registered_users(): void
    {
        Queue::fake();

        $organizer = User::factory()->create();
        $event = Event::factory()->create([
            'capacity' => 10,
            'user_id' => $organizer->id,
        ]);
        $registration = EventRegistration::factory()->registered()->create(['event_id' => $event->id]);

        $this->actingAs($organizer)
            ->patch(
                route('admin.events.update', $event),
                [
                    'name' => 'Updated Event',
                    'date' => now()->addDays(5)->format('Y-m-d'),
                    'time' => now()->addDays(5)->setHour(10),
                    'location' => $event->location,
                ]
            );

        Queue::assertPushed(SendEventChangeNotification::class);
    }

    public function test_registration_confirmation_job_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::factory()->create();
        $registration = EventRegistration::factory()->registered()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        SendRegistrationConfirmation::dispatch($registration);

        Mail::assertQueued(RegistrationConfirmation::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
