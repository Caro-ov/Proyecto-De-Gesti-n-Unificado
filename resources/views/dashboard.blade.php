<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Admin"
            title="Vista general del sistema"
            description="Resumen operativo para administradores y coordinadores con acceso rapido a los modulos principales."
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.dashboard')],
                ['label' => 'Dashboard', 'current' => true],
            ]"
        >
            <x-slot name="actions">
                <div class="inline-flex items-center rounded-2xl bg-slate-100 px-4 py-3 text-sm font-medium text-slate-600">
                    Sesion activa: {{ ucfirst(auth()->user()->role ?? 'sin rol') }}
                </div>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="app-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
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
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                            Agenda
                        </p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900">
                            Proximos eventos
                        </h3>
                    </div>

                    <a
                        href="{{ route('admin.events.index') }}"
                        class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-900"
                    >
                        Ver todos
                    </a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($upcomingEvents as $event)
                        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-slate-50 px-5 py-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-lg font-semibold text-slate-900">{{ $event->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ optional($event->date)->format('d/m/Y') }} - {{ optional($event->time)->format('H:i') }} - {{ $event->location }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded-full bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                                    {{ $event->statusLabel() }}
                                </span>
                                <a
                                    href="{{ route('admin.events.show', $event) }}"
                                    class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                                >
                                    Abrir
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 px-6 py-10 text-center">
                            <p class="text-sm font-medium text-slate-600">
                                No hay eventos cargados todavia.
                            </p>
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="rounded-3xl border border-slate-200 bg-slate-950 p-6 text-slate-100 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-300">
                    Acciones rapidas
                </p>
                <h3 class="mt-2 text-xl font-semibold">
                    Atajos de gestion
                </h3>
                <p class="mt-3 text-sm leading-6 text-slate-400">
                    Accede a las tareas mas frecuentes del panel sin salir del contexto principal.
                </p>

                <div class="mt-6 grid gap-3">
                    <a
                        href="{{ route('portal.events.mine') }}"
                        class="rounded-2xl bg-white/10 px-4 py-4 text-sm font-medium text-white transition hover:bg-white/15"
                    >
                        Revisar mis eventos
                    </a>

                    @can('create', \App\Models\Event::class)
                        <a
                            href="{{ route('admin.events.create') }}"
                            class="rounded-2xl bg-sky-400 px-4 py-4 text-sm font-semibold text-slate-950 transition hover:bg-sky-300"
                        >
                            Crear nuevo evento
                        </a>
                    @endcan

                    <a
                        href="{{ route('portal.profile.edit') }}"
                        class="rounded-2xl bg-white/10 px-4 py-4 text-sm font-medium text-white transition hover:bg-white/15"
                    >
                        Actualizar perfil
                    </a>
                </div>

                <div class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-5">
                    <p class="text-sm font-medium text-slate-300">Usuario actual</p>
                    <p class="mt-3 text-lg font-semibold">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ auth()->user()->email }}</p>
                    <div class="mt-4 inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-emerald-300">
                        {{ ucfirst(auth()->user()->role ?? 'sin rol') }}
                    </div>
                </div>
            </article>
        </section>
    </div>
</x-app-layout>
