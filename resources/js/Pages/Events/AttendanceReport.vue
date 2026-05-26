<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="mx-auto max-w-6xl px-4">
      <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Reporte de Asistencia</h1>
        <p class="text-gray-600">{{ event.name }}</p>
        <p class="text-sm text-gray-500">
          {{ new Date(event.date).toLocaleDateString() }} a las
          {{ new Date(event.time).toLocaleTimeString() }}
        </p>
      </div>

      <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="rounded-lg bg-white p-4 shadow-sm border-l-4 border-blue-500">
          <p class="text-gray-600 text-sm font-semibold">Total Inscritos</p>
          <p class="text-3xl font-bold text-gray-900">{{ stats.total_registered + stats.total_attended }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm border-l-4 border-green-500">
          <p class="text-gray-600 text-sm font-semibold">Presentes</p>
          <p class="text-3xl font-bold text-green-600">{{ stats.total_attended }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm border-l-4 border-yellow-500">
          <p class="text-gray-600 text-sm font-semibold">Lista de Espera</p>
          <p class="text-3xl font-bold text-yellow-600">{{ stats.total_waitlist }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm border-l-4 border-red-500">
          <p class="text-gray-600 text-sm font-semibold">Cancelados</p>
          <p class="text-3xl font-bold text-red-600">{{ stats.total_cancelled }}</p>
        </div>
      </div>

      <div class="rounded-lg bg-white shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nombre</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estado</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Inscripción</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Check-in</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Asistencia</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="reg in registrations" :key="reg.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">{{ reg.user_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ reg.user_email }}</td>
                <td class="px-6 py-4">
                  <span
                    :class="{
                      'bg-blue-100 text-blue-800': reg.status === 'registered',
                      'bg-green-100 text-green-800': reg.status === 'attended',
                      'bg-yellow-100 text-yellow-800': reg.status === 'waitlist',
                      'bg-red-100 text-red-800': reg.status === 'cancelled',
                    }"
                    class="px-3 py-1 rounded-full text-xs font-semibold"
                  >
                    {{ reg.status_label }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ reg.registered_at || '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ reg.checked_in_at || '—' }}</td>
                <td class="px-6 py-4 text-center">
                  <span v-if="reg.is_attended" class="text-xl">✅</span>
                  <span v-else class="text-gray-400">—</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-8">
        <Link :href="route('admin.events.check-in', event.id)" class="text-blue-600 hover:text-blue-700 font-semibold">
          ← Volver a Check-in
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  event: Object,
  registrations: Array,
  stats: Object,
})
</script>
