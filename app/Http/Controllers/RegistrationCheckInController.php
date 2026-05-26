<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RegistrationCheckInController extends Controller
{
    public function show(Event $event)
    {
        $this->authorize('update', $event);

        return Inertia::render('Events/QrScanner', [
            'event' => $event->only('id', 'name', 'date', 'time'),
        ]);
    }

    public function checkIn(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate(['qr_token' => 'required|string']);

        return DB::transaction(function () use ($request, $event) {
            $registration = EventRegistration::lockForUpdate()
                ->where('qr_token', $request->qr_token)
                ->first();

            if (!$registration) {
                return response()->json(
                    ['error' => 'Código QR no válido'],
                    422
                );
            }

            if ($registration->event_id !== $event->id) {
                return response()->json(
                    ['error' => 'El código QR no corresponde a este evento'],
                    422
                );
            }

            if ($registration->isCheckedIn()) {
                return response()->json(
                    ['error' => 'Este código QR ya fue validado'],
                    422
                );
            }

            if (!$registration->canCheckIn()) {
                return response()->json(
                    ['error' => 'La inscripción no es válida para check-in'],
                    422
                );
            }

            $registration->update([
                'checked_in_at' => now(),
                'status' => EventRegistration::STATUS_ATTENDED,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in confirmado para ' . $registration->user->name,
                'user' => [
                    'name' => $registration->user->name,
                    'email' => $registration->user->email,
                ],
            ]);
        });
    }
}
