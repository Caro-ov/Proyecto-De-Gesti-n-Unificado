<section class="space-y-6">
    <header>
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-rose-400">
            Zona critica
        </p>
        <h2 class="mt-2 text-xl font-semibold text-slate-900">
            Eliminar cuenta
        </h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Esta accion es permanente y eliminara los datos asociados a tu cuenta.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Eliminar cuenta</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('portal.profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-xl font-semibold text-slate-900">
                Confirmar eliminacion de la cuenta
            </h2>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Ingresa tu contrasena para confirmar que deseas eliminar permanentemente la cuenta.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Contrasena" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="Contrasena"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button>
                    Eliminar cuenta
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
