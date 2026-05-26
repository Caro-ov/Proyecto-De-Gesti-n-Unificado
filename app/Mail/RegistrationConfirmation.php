<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EventRegistration $registration,
        public string $qrImageBase64,
    ) {}

    public function envelope(): Envelope
    {
        try {
            // Recargar relaciones para evitar problemas de lazy loading
            $this->registration->load(['event', 'user']);

            $eventName = $this->registration->event?->name ?? 'Evento';
            $subject = "Confirmación de Inscripción - {$eventName}";
        } catch (\Exception $e) {
            $subject = 'Confirmación de Inscripción';
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmation',
            with: [
                'registration' => $this->registration,
                'qrImageBase64' => $this->qrImageBase64,
            ],
        );
    }
}
