<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Eventos"
            title="Crear evento"
            description="Usa esta vista para registrar un nuevo evento con una estructura consistente para agenda, capacidad y estado."
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.dashboard')],
                ['label' => 'Eventos', 'href' => route('admin.events.index')],
                ['label' => 'Crear evento', 'current' => true],
            ]"
        />
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        @if (session('status'))
            <div class="app-alert-success">
                {{ session('status') }}
            </div>
        @endif

        <x-events.form
            :action="route('admin.events.store')"
            method="POST"
            submit-label="Guardar evento"
        />
    </div>
</x-app-layout>
