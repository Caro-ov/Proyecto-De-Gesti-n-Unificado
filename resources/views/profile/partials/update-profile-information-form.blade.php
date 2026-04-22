<section>
    <header>
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">
            Informacion personal
        </p>
        <h2 class="mt-2 text-xl font-semibold text-slate-900">
            Datos del perfil
        </h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Actualiza tu nombre y correo electronico para mantener tu cuenta al dia.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('portal.profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Correo electronico" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
                    <p class="text-sm text-amber-800">
                        Tu direccion de correo aun no ha sido verificada.
                    </p>

                    <button form="send-verification" class="mt-2 text-sm font-semibold text-amber-900 underline underline-offset-4">
                        Reenviar correo de verificacion
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-emerald-700">
                            Se envio un nuevo enlace de verificacion a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar cambios</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-500"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>
