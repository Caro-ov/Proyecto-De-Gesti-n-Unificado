<x-app-layout>
    <x-slot name="header">
        <x-page-header
            eyebrow="Cuenta"
            title="Perfil"
            description="Gestiona tus datos personales, seguridad y configuracion de acceso desde una estructura uniforme con el resto del panel."
            :breadcrumbs="[
                ['label' => 'Portal', 'href' => route('portal.dashboard')],
                ['label' => 'Perfil', 'current' => true],
            ]"
        />
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        <x-panel>
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-panel>

        <x-panel>
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-panel>

        <x-panel class="border-rose-200">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-panel>
    </div>
</x-app-layout>
