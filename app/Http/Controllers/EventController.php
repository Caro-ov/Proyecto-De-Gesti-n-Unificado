<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEvent($request);
        $validated['user_id'] = $request->user()->id;

        Event::create($validated);

        return redirect()
            ->route('events.create')
            ->with('status', 'Evento creado correctamente.');
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $this->validateEvent($request);

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

    protected function validateEvent(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1'],
            'has_parking' => ['nullable', 'boolean'],
            'parking_slots' => [
                'nullable',
                'integer',
                'min:1',
                Rule::requiredIf(fn () => $request->boolean('has_parking')),
                Rule::prohibitedIf(fn () => ! $request->boolean('has_parking')),
            ],
        ]);

        $validated['has_parking'] = $request->boolean('has_parking');
        $validated['parking_slots'] = $validated['has_parking']
            ? $validated['parking_slots']
            : null;
        $validated['time'] = Carbon::createFromFormat(
            'Y-m-d H:i',
            "{$validated['date']} {$validated['time']}"
        );

        return $validated;
    }
}
