<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Inertia\Inertia;

class AttendanceReportController extends Controller
{
    public function show(Event $event)
    {
        $this->authorize('update', $event);

        $registrations = $event->registrations()
            ->with('user')
            ->get()
            ->map(fn (EventRegistration $r) => [
                'id' => $r->id,
                'user_name' => $r->user->name,
                'user_email' => $r->user->email,
                'status' => $r->status,
                'status_label' => $r->statusLabel(),
                'registered_at' => $r->registered_at?->format('d/m/Y H:i'),
                'checked_in_at' => $r->checked_in_at?->format('d/m/Y H:i'),
                'is_attended' => $r->status === EventRegistration::STATUS_ATTENDED,
            ]);

        $stats = [
            'total_registered' => $registrations->where('status', EventRegistration::STATUS_REGISTERED)->count(),
            'total_attended' => $registrations->where('status', EventRegistration::STATUS_ATTENDED)->count(),
            'total_cancelled' => $registrations->where('status', EventRegistration::STATUS_CANCELLED)->count(),
            'total_waitlist' => $registrations->where('status', EventRegistration::STATUS_WAITLIST)->count(),
        ];

        return Inertia::render('Events/AttendanceReport', [
            'event' => $event->only('id', 'name', 'date', 'time', 'location', 'capacity'),
            'registrations' => $registrations,
            'stats' => $stats,
        ]);
    }
}
