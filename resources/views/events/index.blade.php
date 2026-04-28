@php($routePrefix = request()->routeIs('admin.*') ? 'admin' : 'portal')

<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Eventos"
            title="Eventos registrados"
            description="Consulta, administra y navega por todos los eventos desde una tabla con el mismo patron visual del panel."
            :breadcrumbs="[
                ['label' => $routePrefix === 'admin' ? 'Admin' : 'Portal', 'href' => route($routePrefix.'.dashboard')],
                ['label' => 'Eventos', 'current' => true],
            ]"
        >
            <x-slot name="actions">
                @if ($routePrefix === 'admin')
                    <a
                        href="{{ route('admin.events.create') }}"
                        class="inline-flex items-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        Crear evento
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

        <x-panel>
            <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                        Listado
                    </p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-900">
                        Catalogo de eventos
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        {{ $routePrefix === 'admin' ? 'Revisa fechas, capacidad y accesos desde una sola vista administrativa.' : 'Consulta los eventos disponibles y revisa su informacion principal.' }}
                    </p>
                </div>

                <div class="app-badge">
                    Total: {{ $events->count() }}
                </div>
            </div>

            @if ($events->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-600">
                        No hay eventos registrados por ahora.
                    </p>
                </div>
            @else
                <div class="app-table-wrap">
                    <table class="app-table">
                        <thead class="app-table-head">
                            <tr>
                                <th scope="col" class="app-th">Nombre</th>
                                <th scope="col" class="app-th">Fecha</th>
                                <th scope="col" class="app-th">Estado</th>
                                <th scope="col" class="app-th">Capacidad</th>
                                <th scope="col" class="app-th text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($events as $event)
                                <tr>
                                    <td class="app-td-strong">{{ $event->name }}</td>
                                    <td class="app-td">{{ optional($event->date)->format('d/m/Y') }}</td>
                                    <td class="app-td">
                                        <span class="app-badge">{{ $event->statusLabel() }}</span>
                                    </td>
                                    <td class="app-td">{{ $event->capacity }}</td>
                                    <td class="app-td">
                                        <div class="flex flex-wrap items-center justify-end gap-3">
                                            <a href="{{ route($routePrefix.'.events.show', $event) }}" class="app-link">
                                                Ver detalle
                                            </a>

                                            @if ($routePrefix === 'admin' && auth()->user()->can('update', $event))
                                                <a
                                                    href="{{ route('admin.events.edit', $event) }}"
                                                    class="inline-flex items-center rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100"
                                                >
                                                    Editar
                                                </a>
                                            @endif

                                            @if ($routePrefix === 'admin' && auth()->user()->can('delete', $event))
                                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center rounded-2xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500"
                                                        onclick="return confirm('Seguro que deseas eliminar este evento?')"
                                                    >
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-panel>
    </div>
</x-app-layout>
