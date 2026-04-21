<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detalle del evento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('status'))
                        <div class="mb-6 rounded-md bg-green-100 px-4 py-3 text-sm text-green-800 dark:bg-green-950/40 dark:text-green-100">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->has('registration') || $errors->has('status'))
                        <div class="mb-6 rounded-md bg-red-100 px-4 py-3 text-sm text-red-800 dark:bg-red-950/40 dark:text-red-100">
                            {{ $errors->first('registration') ?: $errors->first('status') }}
                        </div>
                    @endif

                    <div class="mb-6 flex items-center justify-between gap-4">
                        <h3 class="text-2xl font-semibold">{{ $event->name }}</h3>
                        <a href="{{ url()->previous() }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Volver
                        </a>
                    </div>

                    <div class="mb-6 grid gap-4 md:grid-cols-3">
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Inscritos</p>
                            <p class="mt-2 text-2xl font-semibold">{{ $event->confirmed_registrations_count }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Lista de espera</p>
                            <p class="mt-2 text-2xl font-semibold">{{ $event->waitlist_registrations_count }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Cupos disponibles</p>
                            <p class="mt-2 text-2xl font-semibold">{{ $availableSlots }}</p>
                        </div>
                    </div>

                    @if ($currentRegistration)
                        <div class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 p-4 text-indigo-900 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-100">
                            <p class="text-sm font-semibold">Tu estado de inscripcion</p>
                            <p class="mt-2 text-sm text-indigo-800 dark:text-indigo-100">{{ $currentRegistration->statusLabel() }}</p>

                            @if ($currentRegistration->status !== \App\Models\EventRegistration::STATUS_CANCELLED)
                                <form method="POST" action="{{ route('events.registrations.destroy', [$event, $currentRegistration]) }}" class="mt-4">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500"
                                    >
                                        Cancelar inscripcion
                                    </button>
                                </form>
                            @elseif (auth()->user()->can('create', [\App\Models\EventRegistration::class, $event]))
                                <form method="POST" action="{{ route('events.registrations.store', $event) }}" class="mt-4">
                                    @csrf

                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                                    >
                                        Volver a inscribirme
                                    </button>
                                </form>
                            @endif
                        </div>
                    @elseif (auth()->user()->can('create', [\App\Models\EventRegistration::class, $event]))
                        <div class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 p-4 text-indigo-900 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-100">
                            <p class="text-sm text-indigo-800 dark:text-indigo-100">Aun no tienes una inscripcion para este evento.</p>

                            <form method="POST" action="{{ route('events.registrations.store', $event) }}" class="mt-4">
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                                >
                                    Inscribirme
                                </button>
                            </form>
                        </div>
                    @endif

                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">ID</dt>
                            <dd class="mt-1 text-sm">{{ $event->id }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Nombre</dt>
                            <dd class="mt-1 text-sm">{{ $event->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Descripcion</dt>
                            <dd class="mt-1 text-sm">{{ $event->description ?: 'Sin descripcion' }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</dt>
                            <dd class="mt-1 text-sm">{{ optional($event->date)->format('d/m/Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Hora</dt>
                            <dd class="mt-1 text-sm">{{ optional($event->time)->format('H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Ubicacion</dt>
                            <dd class="mt-1 text-sm">{{ $event->location }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="mt-1 text-sm">{{ $event->status }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Capacidad</dt>
                            <dd class="mt-1 text-sm">{{ $event->capacity }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Tiene parqueadero</dt>
                            <dd class="mt-1 text-sm">{{ $event->has_parking ? 'Si' : 'No' }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Cupos de parqueadero</dt>
                            <dd class="mt-1 text-sm">
                                @if ($event->has_parking)
                                    {{ $event->parking_slots ?? 0 }}
                                @else
                                    No aplica
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Usuario creador</dt>
                            <dd class="mt-1 text-sm">{{ $event->user?->name ?? 'No asignado' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @can('viewAny', [\App\Models\EventRegistration::class, $event])
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="mb-4 text-lg font-semibold">Inscripciones</h4>

                        @if ($event->registrations->isEmpty())
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Este evento aun no tiene inscripciones.
                            </p>
                        @else
                            <div class="space-y-4">
                                @foreach ($event->registrations as $registration)
                                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div>
                                                <p class="font-semibold">{{ $registration->user?->name ?? 'Usuario no disponible' }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $registration->user?->email }}</p>
                                                <p class="mt-2 text-sm">Estado actual: {{ $registration->statusLabel() }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                                    Fecha de registro:
                                                    {{ optional($registration->registered_at)->format('d/m/Y H:i') ?: 'Sin fecha' }}
                                                </p>
                                            </div>

                                            <form method="POST" action="{{ route('events.registrations.update', [$event, $registration]) }}" class="grid gap-3 lg:min-w-96">
                                                @csrf
                                                @method('PATCH')

                                                <div>
                                                    <x-input-label :for="'status_'.$registration->id" value="Estado" />
                                                    <select
                                                        id="{{ 'status_'.$registration->id }}"
                                                        name="status"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                                    >
                                                        @foreach (\App\Models\EventRegistration::statuses() as $status)
                                                            <option value="{{ $status }}" @selected($registration->status === $status)>
                                                                {{ \App\Models\EventRegistration::labelFor($status) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <x-input-label :for="'notes_'.$registration->id" value="Notas" />
                                                    <textarea
                                                        id="{{ 'notes_'.$registration->id }}"
                                                        name="notes"
                                                        rows="2"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                                    >{{ $registration->notes }}</textarea>
                                                </div>

                                                <div class="flex justify-end">
                                                    <x-primary-button>Actualizar inscripcion</x-primary-button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
