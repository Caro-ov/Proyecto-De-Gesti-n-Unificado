<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalDashboardController extends Controller
{
    /**
     * Display the portal dashboard for authenticated users.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $activeRegistrations = $user->registrations()
            ->where('status', '!=', EventRegistration::STATUS_CANCELLED)
            ->count();

        $waitlistRegistrations = $user->registrations()
            ->where('status', EventRegistration::STATUS_WAITLIST)
            ->count();

        $upcomingRegistrations = $user->registeredEvents()
            ->wherePivot('status', '!=', EventRegistration::STATUS_CANCELLED)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        $stats = [
            [
                'label' => 'Mis inscripciones',
                'value' => $activeRegistrations,
                'description' => 'Eventos en los que participas actualmente',
                'accent' => 'bg-sky-500',
            ],
            [
                'label' => 'Proximos eventos',
                'value' => $upcomingRegistrations->count(),
                'description' => 'Eventos pendientes a partir de hoy',
                'accent' => 'bg-emerald-500',
            ],
            [
                'label' => 'Lista de espera',
                'value' => $waitlistRegistrations,
                'description' => 'Inscripciones pendientes por liberacion de cupo',
                'accent' => 'bg-amber-500',
            ],
        ];

        return view('portal.dashboard', [
            'stats' => $stats,
            'upcomingRegistrations' => $upcomingRegistrations,
        ]);
    }
}
