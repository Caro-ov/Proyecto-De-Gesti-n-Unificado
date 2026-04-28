@php($canAccessBackoffice = Auth::user()->canAccessBackoffice())

<nav x-data="{ open: false }" class="bg-white border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ $canAccessBackoffice ? route('admin.dashboard') : route('portal.dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-slate-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if ($canAccessBackoffice)
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Admin
                        </x-nav-link>
                        <x-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                            Gestion eventos
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('portal.dashboard')" :active="request()->routeIs('portal.dashboard')">
                        Portal
                    </x-nav-link>
                    <x-nav-link :href="route('portal.events.index')" :active="request()->routeIs('portal.events.index') || request()->routeIs('portal.events.show')">
                        Eventos
                    </x-nav-link>
                    <x-nav-link :href="route('portal.events.mine')" :active="request()->routeIs('portal.events.mine')">
                        Mis eventos
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-slate-500 transition hover:text-slate-700 focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('portal.profile.edit')">
                            Perfil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar sesion
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            @if ($canAccessBackoffice)
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Admin
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                    Gestion eventos
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('portal.dashboard')" :active="request()->routeIs('portal.dashboard')">
                Portal
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('portal.events.index')" :active="request()->routeIs('portal.events.index') || request()->routeIs('portal.events.show')">
                Eventos
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('portal.events.mine')" :active="request()->routeIs('portal.events.mine')">
                Mis eventos
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-slate-200 pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-medium text-slate-800">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('portal.profile.edit')">
                    Perfil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar sesion
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
