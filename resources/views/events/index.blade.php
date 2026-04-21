<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Eventos registrados
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('status'))
                        <div class="mb-6 rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @can('create', \App\Models\Event::class)
                        <div class="mb-6 flex justify-end">
                            <a
                                href="{{ route('events.create') }}"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                            >
                                Crear evento
                            </a>
                        </div>
                    @endcan

                    @if ($events->isEmpty())
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            No hay eventos registrados por ahora.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/40">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Nombre
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Fecha
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Capacidad
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    @foreach ($events as $event)
                                        <tr>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $event->name }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ optional($event->date)->format('d/m/Y') }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $event->capacity }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('events.show', $event) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Ver detalle
                                                    </a>
                                                    @can('update', $event)
                                                        <a
                                                            href="{{ route('events.edit', $event) }}"
                                                            class="inline-flex items-center rounded-md bg-amber-500 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-400"
                                                        >
                                                            Editar
                                                        </a>
                                                    @endcan
                                                    @can('delete', $event)
                                                        <form method="POST" action="{{ route('events.destroy', $event) }}">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-500"
                                                                onclick="return confirm('Seguro que deseas eliminar este evento?')"
                                                            >
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
