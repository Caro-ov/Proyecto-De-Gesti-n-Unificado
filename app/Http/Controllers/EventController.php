<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->select(['id', 'name', 'date', 'capacity'])
            ->latest('date')
            ->get();

        return view('events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        $event->loadMissing('user');

        return view('events.show', compact('event'));
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
            ->route('events.create')
            ->with('status', 'Evento creado correctamente.');
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $validated = $request->validated();

        $event->update($validated);

        return redirect()
            ->route('events.index')
            ->with('status', 'Evento actualizado correctamente.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('status', 'Evento eliminado correctamente.');
    }

}
