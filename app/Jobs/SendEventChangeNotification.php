<?php

namespace App\Jobs;

use App\Mail\EventModifiedNotification;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEventChangeNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public array $changedFields,
    ) {}

    public function handle(): void
    {
        $registrations = $this->event->registrations()
            ->where('status', '!=', 'cancelled')
            ->with('user')
            ->get();

        foreach ($registrations as $registration) {
            Mail::to($registration->user->email)->send(
                new EventModifiedNotification($this->event, $this->changedFields)
            );
        }
    }
}
