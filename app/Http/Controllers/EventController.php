<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->select(['id', 'name', 'date', 'status', 'capacity'])
            ->latest('date')
            ->get();

        return view('events.index', compact('events'));
    }

    public function mine(Request $request): View
    {
        $events = $request->user()
            ->registeredEvents()
            ->wherePivot('status', '!=', EventRegistration::STATUS_CANCELLED)
            ->with(['user'])
            ->orderByPivot('registered_at', 'desc')
            ->get();

        return view('events.mine', compact('events'));
    }

    public function show(Request $request, Event $event): View
    {
        $event->loadMissing('user');
        $event->loadCount([
            'registrations as confirmed_registrations_count' => fn ($query) => $query->whereIn('status', [
                EventRegistration::STATUS_REGISTERED,
                EventRegistration::STATUS_ATTENDED,
            ]),
            'registrations as waitlist_registrations_count' => fn ($query) => $query->where('status', EventRegistration::STATUS_WAITLIST),
        ]);

        $currentRegistration = $event->registrations()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($request->user()->can('viewAny', [EventRegistration::class, $event])) {
            $event->load([
                'registrations' => fn ($query) => $query
                    ->with('user')
                    ->orderByRaw("
                        CASE status
                            WHEN 'registered' THEN 1
                            WHEN 'attended' THEN 2
                            WHEN 'waitlist' THEN 3
                            WHEN 'cancelled' THEN 4
                            ELSE 5
                        END
                    ")
                    ->orderBy('registered_at'),
            ]);
        }

        $availableSlots = max(0, $event->capacity - $event->confirmed_registrations_count);

        return view('events.show', compact('event', 'currentRegistration', 'availableSlots'));
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        Event::create($validated);

        return redirect()
            ->route('admin.events.create')
            ->with('status', 'Evento creado correctamente.');
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $validated = $request->validated();

        $event->update($validated);

        return redirect()
            ->route('admin.events.index')
            ->with('status', 'Evento actualizado correctamente.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('status', 'Evento eliminado correctamente.');
    }

}
