<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Portal"
            title="Mi portal"
            description="Resumen de tus inscripciones, proximos eventos y accesos frecuentes dentro del portal de usuario."
            :breadcrumbs="[
                ['label' => 'Portal', 'href' => route('portal.dashboard')],
                ['label' => 'Inicio', 'current' => true],
            ]"
        />
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-3">
            @foreach ($stats as $stat)
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                            <p class="mt-4 text-4xl font-semibold tracking-tight text-slate-900">
                                {{ number_format($stat['value']) }}
                            </p>
                        </div>

                        <span class="{{ $stat['accent'] }} h-3 w-3 rounded-full"></span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-slate-500">
                        {{ $stat['description'] }}
                    </p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
            <x-panel>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                            Agenda personal
                        </p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900">
                            Proximos eventos inscritos
                        </h2>
                    </div>

                    <a href="{{ route('portal.events.mine') }}" class="app-link">
                        Ver mis eventos
                    </a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($upcomingRegistrations as $event)
                        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-lg font-semibold text-slate-900">{{ $event->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ optional($event->date)->format('d/m/Y') }} - {{ optional($event->time)->format('H:i') }} - {{ $event->location }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="app-badge">
                                    {{ \App\Models\EventRegistration::labelFor($event->pivot->status) }}
                                </span>
                                <a
                                    href="{{ route('portal.events.show', $event) }}"
                                    class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                                >
                                    Abrir
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 px-6 py-10 text-center">
                            <p class="text-sm font-medium text-slate-600">
                                No tienes eventos proximos registrados.
                            </p>
                        </div>
                    @endforelse
                </div>
            </x-panel>

            <article class="rounded-3xl border border-slate-200 bg-slate-950 p-6 text-slate-100 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-300">
                    Acciones rapidas
                </p>
                <h3 class="mt-2 text-xl font-semibold">
                    Accesos del portal
                </h3>
                <p class="mt-3 text-sm leading-6 text-slate-400">
                    Navega rapidamente entre tus secciones principales sin salir del portal.
                </p>

                <div class="mt-6 grid gap-3">
                    <a
                        href="{{ route('portal.events.index') }}"
                        class="rounded-2xl bg-white/10 px-4 py-4 text-sm font-medium text-white transition hover:bg-white/15"
                    >
                        Explorar eventos
                    </a>
                    <a
                        href="{{ route('portal.events.mine') }}"
                        class="rounded-2xl bg-white/10 px-4 py-4 text-sm font-medium text-white transition hover:bg-white/15"
                    >
                        Mis eventos
                    </a>
                    <a
                        href="{{ route('portal.profile.edit') }}"
                        class="rounded-2xl bg-sky-400 px-4 py-4 text-sm font-semibold text-slate-950 transition hover:bg-sky-300"
                    >
                        Editar perfil
                    </a>
                </div>
            </article>
        </section>
    </div>
</x-app-layout>
