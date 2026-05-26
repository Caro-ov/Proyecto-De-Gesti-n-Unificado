<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventModifiedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public array $changedFields,
    ) {}

    public function envelope(): Envelope
    {
        try {
            $eventName = $this->event?->name ?? 'Evento';
            $subject = "Cambios en el Evento - {$eventName}";
        } catch (\Exception $e) {
            $subject = 'Cambios en el Evento';
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-modified-notification',
            with: [
                'event' => $this->event,
                'changedFields' => $this->changedFields,
            ],
        );
    }
}
