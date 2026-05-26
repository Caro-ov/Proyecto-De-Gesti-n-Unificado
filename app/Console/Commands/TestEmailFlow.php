<?php

namespace App\Console\Commands;

use App\Jobs\SendRegistrationConfirmation;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestEmailFlow extends Command
{
    protected $signature = 'test:email-flow {--no-queue}';
    protected $description = 'Test the email notification flow';

    public function handle(): int
    {
        $this->info('🧪 Testing email flow...');

        try {
            // 1. Obtener o crear usuario de prueba
            $user = User::firstOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Usuario Prueba',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            $this->info("✅ Usuario: {$user->email}");

            // 2. Obtener o crear evento de prueba
            $event = Event::first();
            if (!$event) {
                $event = Event::create([
                    'name' => 'Evento de Prueba',
                    'description' => 'Evento para testing',
                    'date' => now()->addDays(5)->format('Y-m-d'),
                    'time' => now()->addDays(5)->setHour(10),
                    'location' => 'Sala de Prueba',
                    'status' => 'open',
                    'capacity' => 10,
                    'user_id' => User::first()?->id ?? 1,
                ]);
            }
            $this->info("✅ Evento: {$event->name}");

            // 3. Crear o actualizar inscripción con QR
            $registration = EventRegistration::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                ],
                [
                    'status' => 'registered',
                    'registered_at' => now(),
                    'qr_token' => Str::uuid(),
                    'checked_in_at' => null,
                ]
            );
            $this->info("✅ Inscripción: {$registration->id}");
            $this->info("   QR Token: {$registration->qr_token}");

            // 4. Enviar email
            if ($this->option('no-queue')) {
                $this->info("\n📧 Enviando email directamente (sin queue)...");
                try {
                    $job = new SendRegistrationConfirmation($registration->id);
                    $job->handle(app(\App\Services\QrGeneratorService::class));
                    $this->info("✅ Email enviado exitosamente");
                } catch (\Exception $e) {
                    $this->error("❌ Error al enviar email: " . $e->getMessage());
                    return self::FAILURE;
                }
            } else {
                $this->info("\n📧 Encolando email en queue...");
                SendRegistrationConfirmation::dispatch($registration->id);
                $this->info("✅ Email encolado. Ejecuta: php artisan queue:listen");
            }

            // 5. Verificar credenciales de email
            $this->info("\n📋 Configuración de email:");
            $this->info("   MAIL_MAILER: " . env('MAIL_MAILER'));
            $this->info("   MAIL_HOST: " . env('MAIL_HOST'));
            $this->info("   MAIL_PORT: " . env('MAIL_PORT'));
            $this->info("   MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS'));

            $this->info("\n✅ Test completado");
            $this->info("\n📍 Próximos pasos:");
            $this->info("   1. Si usaste --no-queue:");
            $this->info("      - Ver el email en Mailtrap: https://mailtrap.io");
            $this->info("   2. Si no usaste --no-queue:");
            $this->info("      - Ejecutar: php artisan queue:listen");
            $this->info("      - Ver el email en Mailtrap");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
