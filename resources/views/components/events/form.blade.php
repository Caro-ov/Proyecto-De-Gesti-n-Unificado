@props([
    'action',
    'method' => 'POST',
    'submitLabel',
    'event' => null,
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf

    @if (! in_array(strtoupper($method), ['GET', 'POST'], true))
        @method($method)
    @endif

    <x-panel>
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                Informacion principal
            </p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">
                Datos del evento
            </h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">
                Define la informacion base que se mostrara a los usuarios en el listado y en el detalle del evento.
            </p>
        </div>

        <div class="space-y-6">
            <div>
                <x-input-label for="name" value="Nombre" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $event?->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="Descripcion" />
                <textarea
                    id="description"
                    name="description"
                    class="app-textarea"
                    rows="4"
                >{{ old('description', $event?->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="date" value="Fecha" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', optional($event?->date)->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="time" value="Hora" />
                    <x-text-input id="time" name="time" type="time" class="mt-1 block w-full" :value="old('time', optional($event?->time)->format('H:i'))" required />
                    <x-input-error :messages="$errors->get('time')" class="mt-2" />
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="location" value="Ubicacion" />
                    <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $event?->location)" required />
                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Estado" />
                    <select id="status" name="status" class="app-select" required>
                        @foreach (\App\Enums\EventStatus::cases() as $status)
                            <option
                                value="{{ $status->value }}"
                                @selected(old('status', $event?->status?->value ?? $event?->status ?? \App\Enums\EventStatus::OPEN->value) === $status->value)
                            >
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
            </div>
        </div>
    </x-panel>

    <x-panel>
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                Capacidad
            </p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">
                Control de cupos y acceso
            </h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">
                Configura la capacidad del evento y si requiere gestionar parqueadero para los asistentes.
            </p>
        </div>

        <div class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="capacity" value="Capacidad" />
                    <x-text-input id="capacity" name="capacity" type="number" min="1" class="mt-1 block w-full" :value="old('capacity', $event?->capacity)" required />
                    <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="parking_slots" value="Cupos de parqueadero" />
                    <x-text-input id="parking_slots" name="parking_slots" type="number" min="1" class="mt-1 block w-full" :value="old('parking_slots', $event?->parking_slots)" />
                    <x-input-error :messages="$errors->get('parking_slots')" class="mt-2" />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <label for="has_parking" class="inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                    <input
                        id="has_parking"
                        name="has_parking"
                        type="checkbox"
                        value="1"
                        class="app-checkbox"
                        @checked(old('has_parking', $event?->has_parking))
                    >
                    <span>El evento cuenta con parqueadero disponible</span>
                </label>
                <x-input-error :messages="$errors->get('has_parking')" class="mt-2" />
            </div>
        </div>
    </x-panel>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a
            href="{{ route('admin.events.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
            Cancelar
        </a>

        <x-primary-button>{{ $submitLabel }}</x-primary-button>
    </div>
</form>
