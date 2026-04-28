@php($routePrefix = request()->routeIs('admin.*') ? 'admin' : 'portal')

<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Eventos"
            :title="$event->name"
            description="Detalle operativo del evento, su capacidad y las inscripciones asociadas dentro del mismo patron del panel."
            :breadcrumbs="[
                ['label' => $routePrefix === 'admin' ? 'Admin' : 'Portal', 'href' => route($routePrefix.'.dashboard')],
                ['label' => 'Eventos', 'href' => route($routePrefix.'.events.index')],
                ['label' => $event->name, 'current' => true],
            ]"
        >
            <x-slot name="actions">
                <a href="{{ url()->previous() }}" class="app-link-muted">
                    Volver
                </a>

                @if ($routePrefix === 'admin' && auth()->user()->can('update', $event))
                    <a href="{{ route('admin.events.edit', $event) }}" class="app-link">
                        Editar evento
                    </a>
                @endif
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        @if (session('status'))
            <div class="app-alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('registration') || $errors->has('status'))
            <div class="app-alert-danger">
                {{ $errors->first('registration') ?: $errors->first('status') }}
            </div>
        @endif

        <x-panel>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="app-panel-muted">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Inscritos</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $event->confirmed_registrations_count }}</p>
                </div>
                <div class="app-panel-muted">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Lista de espera</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $event->waitlist_registrations_count }}</p>
                </div>
                <div class="app-panel-muted">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Cupos disponibles</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $availableSlots }}</p>
                </div>
            </div>

            @php($registrationRestrictionMessage = $event->registrationRestrictionMessage())

            @if ($currentRegistration)
                <div class="app-alert-info mt-6">
                    <p class="font-semibold">Tu estado de inscripcion</p>
                    <p class="mt-2">{{ $currentRegistration->statusLabel() }}</p>

                    @if ($currentRegistration->status !== \App\Models\EventRegistration::STATUS_CANCELLED)
                        <form method="POST" action="{{ route($routePrefix.'.events.registrations.destroy', [$event, $currentRegistration]) }}" class="mt-4">
                            @csrf
                            @method('DELETE')

                            <x-danger-button>Cancelar inscripcion</x-danger-button>
                        </form>
                    @elseif ($registrationRestrictionMessage)
                        <p class="mt-4 text-sm text-amber-700">
                            {{ $registrationRestrictionMessage }}
                        </p>
                    @elseif (auth()->user()->can('create', [\App\Models\EventRegistration::class, $event]))
                        <form method="POST" action="{{ route($routePrefix.'.events.registrations.store', $event) }}" class="mt-4">
                            @csrf

                            <x-primary-button>Volver a inscribirme</x-primary-button>
                        </form>
                    @endif
                </div>
            @elseif (auth()->user()->can('create', [\App\Models\EventRegistration::class, $event]))
                <div class="app-alert-info mt-6">
                    @if ($registrationRestrictionMessage)
                        <p>{{ $registrationRestrictionMessage }}</p>
                    @else
                        <p>Aun no tienes una inscripcion para este evento.</p>
                    @endif

                    @if (! $registrationRestrictionMessage)
                        <form method="POST" action="{{ route($routePrefix.'.events.registrations.store', $event) }}" class="mt-4">
                            @csrf

                            <x-primary-button>Inscribirme</x-primary-button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="mt-8">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                    Informacion general
                </p>
                <h2 class="mt-2 text-xl font-semibold text-slate-900">
                    Ficha del evento
                </h2>
            </div>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">ID</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ $event->id }}</dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Nombre</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ $event->name }}</dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Estado</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ $event->statusLabel() }}</dd>
                </div>
                <div class="app-panel-muted sm:col-span-2 xl:col-span-3">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Descripcion</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ $event->description ?: 'Sin descripcion' }}</dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Fecha</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ optional($event->date)->format('d/m/Y') }}</dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Hora</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ optional($event->time)->format('H:i') }}</dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Ubicacion</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ $event->location }}</dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Capacidad</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ $event->capacity }}</dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Tiene parqueadero</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ $event->has_parking ? 'Si' : 'No' }}</dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Cupos de parqueadero</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">
                        @if ($event->has_parking)
                            {{ $event->parking_slots ?? 0 }}
                        @else
                            No aplica
                        @endif
                    </dd>
                </div>
                <div class="app-panel-muted">
                    <dt class="text-xs font-semibold uppercase tracking-widest text-slate-400">Usuario creador</dt>
                    <dd class="mt-2 text-sm font-medium text-slate-900">{{ $event->user?->name ?? 'No asignado' }}</dd>
                </div>
            </dl>
        </x-panel>

        @can('viewAny', [\App\Models\EventRegistration::class, $event])
            <x-panel>
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                        Inscripciones
                    </p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-900">
                        Gestion de asistentes
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Administra el estado y las notas de cada inscripcion sin salir del detalle del evento.
                    </p>
                </div>

                @if ($event->registrations->isEmpty())
                    <div class="rounded-3xl border border-dashed border-slate-300 px-6 py-10 text-center">
                        <p class="text-sm font-medium text-slate-600">
                            Este evento aun no tiene inscripciones.
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($event->registrations as $registration)
                            <div class="app-panel-muted">
                                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="lg:max-w-md">
                                        <p class="text-lg font-semibold text-slate-900">
                                            {{ $registration->user?->name ?? 'Usuario no disponible' }}
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $registration->user?->email }}</p>
                                        <p class="mt-4 text-sm text-slate-600">
                                            Estado actual: <span class="font-semibold text-slate-900">{{ $registration->statusLabel() }}</span>
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            Fecha de registro:
                                            {{ optional($registration->registered_at)->format('d/m/Y H:i') ?: 'Sin fecha' }}
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route($routePrefix.'.events.registrations.update', [$event, $registration]) }}" class="grid gap-4 lg:min-w-[24rem]">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <x-input-label :for="'status_'.$registration->id" value="Estado" />
                                            <select id="{{ 'status_'.$registration->id }}" name="status" class="app-select">
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
                                                rows="3"
                                                class="app-textarea"
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
            </x-panel>
        @endcan
    </div>
</x-app-layout>
