<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Eventos"
            title="Editar evento"
            description="Actualiza la informacion del evento manteniendo el mismo patron visual y operativo del resto del panel."
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.dashboard')],
                ['label' => 'Eventos', 'href' => route('admin.events.index')],
                ['label' => $event->name, 'href' => route('admin.events.show', $event)],
                ['label' => 'Editar', 'current' => true],
            ]"
        >
            <x-slot name="actions">
                <a href="{{ route('admin.events.show', $event) }}" class="app-link">
                    Ver detalle
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        <x-events.form
            :action="route('admin.events.update', $event)"
            method="PATCH"
            submit-label="Actualizar evento"
            :event="$event"
        />
    </div>
</x-app-layout>
