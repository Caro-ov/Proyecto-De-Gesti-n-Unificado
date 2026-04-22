@php
    $isAdminArea = request()->routeIs('admin.*');
    $canAccessBackoffice = auth()->user()->canAccessBackoffice();
@endphp

<div class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur">
    <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 text-slate-700 transition hover:bg-slate-100 lg:hidden"
                @click="sidebarOpen = true"
            >
                <span class="sr-only">Abrir menu lateral</span>
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
                    {{ $isAdminArea ? 'Area administrativa' : 'Portal de usuario' }}
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $isAdminArea ? 'Gestion y seguimiento operativo del sistema' : 'Autoservicio, perfil e inscripciones' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if ($canAccessBackoffice)
                <div class="hidden items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-1 lg:flex">
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="{{ $isAdminArea ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-900' }} rounded-xl px-3 py-2 text-sm font-semibold transition"
                    >
                        Admin
                    </a>
                    <a
                        href="{{ route('portal.dashboard') }}"
                        class="{{ ! $isAdminArea ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-900' }} rounded-xl px-3 py-2 text-sm font-semibold transition"
                    >
                        Portal
                    </a>
                </div>
            @endif

            <div class="hidden text-right sm:block">
                <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
            </div>

            <div class="rounded-full bg-slate-900 px-3 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-white">
                {{ ucfirst(auth()->user()->role ?? 'sin rol') }}
            </div>

            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800"
                    @click="open = ! open"
                >
                    <span class="sr-only">Abrir menu de usuario</span>
                    <span class="text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </button>

                <div
                    x-cloak
                    x-show="open"
                    x-transition.origin.top.right
                    @click.outside="open = false"
                    class="absolute right-0 mt-3 w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl shadow-slate-200/70"
                >
                    <a
                        href="{{ route('portal.profile.edit') }}"
                        class="flex items-center rounded-xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                    >
                        Perfil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50"
                        >
                            Cerrar sesion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
