<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>
    <body class="bg-slate-100 font-sans antialiased text-slate-900">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full overflow-y-auto border-r border-slate-200 bg-slate-950 text-slate-100 shadow-2xl transition-transform duration-300 lg:translate-x-0"
                :class="{ 'translate-x-0': sidebarOpen }"
            >
                @include('layouts.sidebar')
            </aside>

            <div class="lg:pl-72">
                @include('layouts.topbar')

                <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    @isset($header)
                        <header class="mb-8">
                            {{ $header }}
                        </header>
                    @endisset

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
