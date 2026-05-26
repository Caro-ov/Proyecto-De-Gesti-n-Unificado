<?php

namespace App\Jobs;

use App\Mail\RegistrationConfirmation;
use App\Models\EventRegistration;
use App\Services\QrGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRegistrationConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(
        public int $registrationId,
    ) {}

    public function handle(QrGeneratorService $qrService): void
    {
        Log::info("📧 SendRegistrationConfirmation: Iniciando para registration_id={$this->registrationId}");

        // Cargar el registro desde la BD (evita problemas de serialización)
        $registration = EventRegistration::with(['user', 'event'])->find($this->registrationId);

        if (!$registration) {
            Log::error("❌ Registration {$this->registrationId} not found in DB");
            throw new \Exception("Registration {$this->registrationId} not found");
        }

        Log::info("✅ Registration encontrada: {$registration->id}");

        // Verificar usuario
        if (!$registration->user) {
            Log::error("❌ Registration {$registration->id} has no associated user");
            throw new \Exception("Registration has no user");
        }

        Log::info("✅ Usuario encontrado: {$registration->user->email}");

        // Verificar evento
        if (!$registration->event) {
            Log::error("❌ Registration {$registration->id} has no associated event");
            throw new \Exception("Registration has no event");
        }

        Log::info("✅ Evento encontrado: {$registration->event->name}");

        // Generar QR
        Log::info("📱 Generando QR desde token: {$registration->qr_token}");
        $qrImage = $qrService->generateBase64($registration->qr_token);
        Log::info("✅ QR generado (tamaño: " . strlen($qrImage) . " bytes)");

        // Enviar email
        Log::info("📨 Enviando email a: {$registration->user->email}");
        Log::info("📋 Config actual - MAIL_MAILER: " . config('mail.mailer'));
        Log::info("📋 Config actual - MAIL_HOST: " . config('mail.host'));
        Log::info("📋 Config actual - MAIL_PORT: " . config('mail.port'));
        Log::info("📋 Config actual - MAIL_USERNAME: " . config('mail.username'));

        $mailable = new RegistrationConfirmation($registration, $qrImage);
        Log::info("📧 Mailable creado");

        try {
            $response = Mail::to($registration->user->email)->send($mailable);
            Log::info("✅ Mail::send() completado. Response: " . ($response ? 'true' : 'false'));

            if (!$response) {
                throw new \Exception("Mail::send() retornó false");
            }
        } catch (\Exception $e) {
            Log::error("❌ Mail::send failed: {$e->getMessage()}");
            Log::error("Stack: {$e->getTraceAsString()}");
            throw $e;
        }

        Log::info("✅ ¡Email enviado exitosamente a {$registration->user->email}!");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("❌ Job FAILED after {$this->attempts()} attempts: {$exception->getMessage()}");
    }
}
