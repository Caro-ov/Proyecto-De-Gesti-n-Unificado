<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function __invoke(): View
    {
        $openStatuses = [
            EventStatus::ACTIVE->value,
            EventStatus::OPEN->value,
        ];

        $stats = [
            [
                'label' => 'Total usuarios',
                'value' => User::query()->count(),
                'description' => 'Cuentas registradas en la plataforma',
                'accent' => 'bg-sky-500',
            ],
            [
                'label' => 'Usuarios activos',
                'value' => User::query()->where('status', true)->count(),
                'description' => 'Usuarios con rol activo y acceso habilitado',
                'accent' => 'bg-emerald-500',
            ],
            [
                'label' => 'Eventos abiertos',
                'value' => Event::query()->whereIn('status', $openStatuses)->count(),
                'description' => 'Eventos disponibles para gestion o registro',
                'accent' => 'bg-amber-500',
            ],
            [
                'label' => 'Inscripciones',
                'value' => EventRegistration::query()->count(),
                'description' => 'Registros totales vinculados a eventos',
                'accent' => 'bg-violet-500',
            ],
        ];

        $upcomingEvents = Event::query()
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }
}
