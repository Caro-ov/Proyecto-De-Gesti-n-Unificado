@php
    $isAdminArea = request()->routeIs('admin.*');
@endphp

<div class="flex h-full flex-col overflow-hidden">
    <div class="border-b border-slate-800 px-6 py-6">
        <a href="{{ $isAdminArea ? route('admin.dashboard') : route('portal.dashboard') }}" class="block">
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-sky-300">
                {{ $isAdminArea ? 'Admin Panel' : 'Portal' }}
            </p>
            <h1 class="mt-2 text-xl font-semibold text-white">
                {{ config('app.name', 'Laravel') }}
            </h1>
            <p class="mt-2 text-sm leading-6 text-slate-400">
                {{ $isAdminArea ? 'Gestion interna y supervision operativa.' : 'Acceso personal a eventos, perfil e inscripciones.' }}
            </p>
        </a>
    </div>

    <div class="flex-1 px-4 py-6">
        <p class="px-3 text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">
            {{ $isAdminArea ? 'Menu admin' : 'Menu portal' }}
        </p>

        @if ($isAdminArea)
            <nav class="mt-4 space-y-2">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'bg-white text-slate-950 shadow-lg shadow-slate-950/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} flex items-center rounded-2xl px-4 py-3 text-sm font-semibold transition"
                >
                    Inicio
                </a>

                <div x-data="{ open: {{ request()->routeIs('admin.events.*') ? 'true' : 'false' }} }" class="rounded-2xl border border-slate-800 bg-slate-900/60">
                    <button
                        type="button"
                        class="{{ request()->routeIs('admin.events.*') ? 'text-white' : 'text-slate-300 hover:text-white' }} flex w-full items-center justify-between gap-3 px-4 py-3 text-left text-sm font-semibold transition"
                        @click="open = !open"
                    >
                        <span>Eventos</span>
                        <svg class="h-4 w-4 shrink-0 transition" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-cloak x-show="open" x-transition class="border-t border-slate-800 px-3 pb-3">
                        <div class="space-y-2 pt-3">
                            <a
                                href="{{ route('admin.events.index') }}"
                                class="{{ request()->routeIs('admin.events.index') || request()->routeIs('admin.events.show') || request()->routeIs('admin.events.edit') ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} block rounded-xl px-4 py-3 text-sm font-medium transition"
                            >
                                Listar eventos
                            </a>

                            @can('create', \App\Models\Event::class)
                                <a
                                    href="{{ route('admin.events.create') }}"
                                    class="{{ request()->routeIs('admin.events.create') ? 'bg-sky-400 text-slate-950' : 'text-sky-200 hover:bg-sky-400/15 hover:text-white' }} block rounded-xl px-4 py-3 text-sm font-semibold transition"
                                >
                                    Crear evento
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </nav>
        @else
            <nav class="mt-4 space-y-2">
                <a
                    href="{{ route('portal.dashboard') }}"
                    class="{{ request()->routeIs('portal.dashboard') ? 'bg-white text-slate-950 shadow-lg shadow-slate-950/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} flex items-center rounded-2xl px-4 py-3 text-sm font-semibold transition"
                >
                    Inicio
                </a>

                <div x-data="{ open: {{ request()->routeIs('portal.events.*') ? 'true' : 'false' }} }" class="rounded-2xl border border-slate-800 bg-slate-900/60">
                    <button
                        type="button"
                        class="{{ request()->routeIs('portal.events.*') ? 'text-white' : 'text-slate-300 hover:text-white' }} flex w-full items-center justify-between gap-3 px-4 py-3 text-left text-sm font-semibold transition"
                        @click="open = !open"
                    >
                        <span>Eventos</span>
                        <svg class="h-4 w-4 shrink-0 transition" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-cloak x-show="open" x-transition class="border-t border-slate-800 px-3 pb-3">
                        <div class="space-y-2 pt-3">
                            <a
                                href="{{ route('portal.events.index') }}"
                                class="{{ request()->routeIs('portal.events.index') || request()->routeIs('portal.events.show') ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} block rounded-xl px-4 py-3 text-sm font-medium transition"
                            >
                                Explorar eventos
                            </a>

                            <a
                                href="{{ route('portal.events.mine') }}"
                                class="{{ request()->routeIs('portal.events.mine') ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} block rounded-xl px-4 py-3 text-sm font-medium transition"
                            >
                                Mis eventos
                            </a>
                        </div>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('portal.profile.*') ? 'true' : 'false' }} }" class="rounded-2xl border border-slate-800 bg-slate-900/60">
                    <button
                        type="button"
                        class="{{ request()->routeIs('portal.profile.*') ? 'text-white' : 'text-slate-300 hover:text-white' }} flex w-full items-center justify-between gap-3 px-4 py-3 text-left text-sm font-semibold transition"
                        @click="open = !open"
                    >
                        <span>Cuenta</span>
                        <svg class="h-4 w-4 shrink-0 transition" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-cloak x-show="open" x-transition class="border-t border-slate-800 px-3 pb-3">
                        <div class="space-y-2 pt-3">
                            <a
                                href="{{ route('portal.profile.edit') }}"
                                class="{{ request()->routeIs('portal.profile.*') ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} block rounded-xl px-4 py-3 text-sm font-medium transition"
                            >
                                Perfil y seguridad
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        @endif
    </div>

    <div class="border-t border-slate-800 px-6 py-5">
        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
        <p class="mt-1 text-sm text-slate-400">{{ auth()->user()->email }}</p>
        <div class="mt-4 inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">
            {{ ucfirst(auth()->user()->role ?? 'sin rol') }}
        </div>
    </div>
</div>
