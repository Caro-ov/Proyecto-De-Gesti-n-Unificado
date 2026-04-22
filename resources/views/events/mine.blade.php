<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Eventos"
            title="Mis eventos"
            description="Consulta rapidamente las inscripciones asociadas a tu cuenta con el mismo lenguaje visual del resto del panel."
            :breadcrumbs="[
                ['label' => 'Portal', 'href' => route('portal.dashboard')],
                ['label' => 'Eventos', 'href' => route('portal.events.index')],
                ['label' => 'Mis eventos', 'current' => true],
            ]"
        >
            <x-slot name="actions">
                <a href="{{ route('portal.events.index') }}" class="app-link">
                    Ver todos los eventos
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        <x-panel>
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                    Seguimiento personal
                </p>
                <h2 class="mt-2 text-xl font-semibold text-slate-900">
                    Eventos en los que estas inscrito
                </h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Se muestran solo los eventos relacionados con tu usuario autenticado.
                </p>
            </div>

            @if ($events->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-600">
                        Aun no estas inscrito en ningun evento.
                    </p>
                </div>
            @else
                <div class="app-table-wrap">
                    <table class="app-table">
                        <thead class="app-table-head">
                            <tr>
                                <th scope="col" class="app-th">Evento</th>
                                <th scope="col" class="app-th">Fecha</th>
                                <th scope="col" class="app-th">Estado</th>
                                <th scope="col" class="app-th">Inscripcion</th>
                                <th scope="col" class="app-th text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($events as $event)
                                <tr>
                                    <td class="app-td-strong">{{ $event->name }}</td>
                                    <td class="app-td">{{ optional($event->date)->format('d/m/Y') }}</td>
                                    <td class="app-td">
                                        <span class="app-badge">
                                            {{ \App\Models\EventRegistration::labelFor($event->pivot->status) }}
                                        </span>
                                    </td>
                                    <td class="app-td">
                                        {{ optional($event->pivot->registered_at)->format('d/m/Y H:i') ?: 'Sin fecha' }}
                                    </td>
                                    <td class="app-td text-right">
                                        <a href="{{ route('portal.events.show', $event) }}" class="app-link">
                                            Ver detalle
                                        </a>
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
