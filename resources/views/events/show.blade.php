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
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <h3 class="text-2xl font-semibold">{{ $event->name }}</h3>
                        <a href="{{ url()->previous() }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Volver
                        </a>
                    </div>

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
        </div>
    </div>
</x-app-layout>