<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EventRegistrationController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('create', [EventRegistration::class, $event]);

        $registration = DB::transaction(function () use ($event, $request): EventRegistration {
            $lockedEvent = Event::query()
                ->lockForUpdate()
                ->findOrFail($event->id);

            $existingRegistration = EventRegistration::query()
                ->where('event_id', $lockedEvent->id)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->first();

            if ($existingRegistration !== null && $existingRegistration->status !== EventRegistration::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'registration' => 'Ya tienes una inscripcion activa para este evento.',
                ]);
            }

            if ($lockedEvent->time !== null && $lockedEvent->time->isPast()) {
                throw ValidationException::withMessages([
                    'registration' => 'No puedes inscribirte en un evento que ya ocurrio.',
                ]);
            }

            $status = $lockedEvent->remainingCapacity(
                $existingRegistration?->id
            ) > 0
                ? EventRegistration::STATUS_REGISTERED
                : EventRegistration::STATUS_WAITLIST;

            $payload = [
                'status' => $status,
                'registered_at' => now(),
                'cancelled_at' => null,
                'attended_at' => null,
                'notes' => null,
            ];

            if ($existingRegistration !== null) {
                $existingRegistration->update($payload);

                return $existingRegistration->refresh();
            }

            return EventRegistration::query()->create([
                'event_id' => $lockedEvent->id,
                'user_id' => $request->user()->id,
                ...$payload,
            ]);
        });

        $message = $registration->status === EventRegistration::STATUS_WAITLIST
            ? 'El evento alcanzo su cupo. Quedaste en lista de espera.'
            : 'Te inscribiste correctamente en el evento.';

        return redirect()
            ->route('events.show', $event)
            ->with('status', $message);
    }

    public function update(Request $request, Event $event, EventRegistration $registration): RedirectResponse
    {
        $this->ensureRegistrationBelongsToEvent($event, $registration);
        $this->authorize('update', $registration);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(EventRegistration::statuses())],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($event, $registration, $validated): void {
            $lockedEvent = Event::query()
                ->lockForUpdate()
                ->findOrFail($event->id);

            $lockedRegistration = EventRegistration::query()
                ->lockForUpdate()
                ->findOrFail($registration->id);

            $status = $validated['status'];

            if (in_array($status, [
                EventRegistration::STATUS_REGISTERED,
                EventRegistration::STATUS_ATTENDED,
            ], true) && $lockedEvent->remainingCapacity($lockedRegistration->id) === 0) {
                throw ValidationException::withMessages([
                    'status' => 'No hay cupos disponibles para asignar este registro.',
                ]);
            }

            $payload = [
                'status' => $status,
                'notes' => $validated['notes'] ?? null,
                'registered_at' => $lockedRegistration->registered_at ?? now(),
                'cancelled_at' => null,
                'attended_at' => null,
            ];

            if ($status === EventRegistration::STATUS_CANCELLED) {
                $payload['cancelled_at'] = now();
                $payload['attended_at'] = null;
            }

            if ($status === EventRegistration::STATUS_WAITLIST) {
                $payload['attended_at'] = null;
            }

            if ($status === EventRegistration::STATUS_ATTENDED) {
                $payload['attended_at'] = now();
            }

            $lockedRegistration->update($payload);

            if ($status === EventRegistration::STATUS_CANCELLED) {
                $this->promoteFirstWaitlistedRegistration($lockedEvent);
            }
        });

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'La inscripcion fue actualizada correctamente.');
    }

    public function destroy(Event $event, EventRegistration $registration): RedirectResponse
    {
        $this->ensureRegistrationBelongsToEvent($event, $registration);
        $this->authorize('delete', $registration);

        DB::transaction(function () use ($event, $registration): void {
            $lockedEvent = Event::query()
                ->lockForUpdate()
                ->findOrFail($event->id);

            $lockedRegistration = EventRegistration::query()
                ->lockForUpdate()
                ->findOrFail($registration->id);

            $lockedRegistration->update([
                'status' => EventRegistration::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'attended_at' => null,
            ]);

            $this->promoteFirstWaitlistedRegistration($lockedEvent);
        });

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'La inscripcion fue cancelada correctamente.');
    }

    private function promoteFirstWaitlistedRegistration(Event $event): void
    {
        if ($event->remainingCapacity() === 0) {
            return;
        }

        $waitlistedRegistration = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('status', EventRegistration::STATUS_WAITLIST)
            ->orderBy('registered_at')
            ->lockForUpdate()
            ->first();

        if ($waitlistedRegistration === null) {
            return;
        }

        $waitlistedRegistration->update([
            'status' => EventRegistration::STATUS_REGISTERED,
            'registered_at' => $waitlistedRegistration->registered_at ?? Carbon::now(),
            'cancelled_at' => null,
            'attended_at' => null,
        ]);
    }

    private function ensureRegistrationBelongsToEvent(Event $event, EventRegistration $registration): void
    {
        abort_unless($registration->event_id === $event->id, 404);
    }
}
