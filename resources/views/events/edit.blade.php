<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar evento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $event->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Descripcion" />
                            <textarea
                                id="description"
                                name="description"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                rows="4"
                            >{{ old('description', $event->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="date" value="Fecha" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', optional($event->date)->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="time" value="Hora" />
                                <x-text-input id="time" name="time" type="time" class="mt-1 block w-full" :value="old('time', optional($event->time)->format('H:i'))" required />
                                <x-input-error :messages="$errors->get('time')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="location" value="Ubicacion" />
                                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $event->location)" required />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" value="Estado" />
                                <select
                                    id="status"
                                    name="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    required
                                >
                                    @foreach (\App\Enums\EventStatus::cases() as $status)
                                        <option
                                            value="{{ $status->value }}"
                                            @selected(old('status', $event->status?->value ?? $event->status) === $status->value)
                                        >
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="capacity" value="Capacidad" />
                                <x-text-input id="capacity" name="capacity" type="number" min="1" class="mt-1 block w-full" :value="old('capacity', $event->capacity)" required />
                                <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="parking_slots" value="Cupos de parqueadero" />
                                <x-text-input id="parking_slots" name="parking_slots" type="number" min="1" class="mt-1 block w-full" :value="old('parking_slots', $event->parking_slots)" />
                                <x-input-error :messages="$errors->get('parking_slots')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <label for="has_parking" class="inline-flex items-center gap-2">
                                <input
                                    id="has_parking"
                                    name="has_parking"
                                    type="checkbox"
                                    value="1"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    @checked(old('has_parking', $event->has_parking))
                                >
                                <span>Tiene parqueadero</span>
                            </label>
                            <x-input-error :messages="$errors->get('has_parking')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Actualizar evento</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
