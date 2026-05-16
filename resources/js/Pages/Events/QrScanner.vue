<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="mx-auto max-w-2xl px-4">
      <div class="rounded-lg bg-white shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-2">Check-in: {{ event.name }}</h1>
        <p class="text-gray-600 mb-8">
          {{ new Date(event.date).toLocaleDateString() }} a las
          {{ new Date(event.time).toLocaleTimeString() }}
        </p>

        <div v-if="message.type === 'success'" class="mb-4 rounded-lg bg-green-50 p-4 border border-green-200">
          <p class="text-green-800 font-semibold">{{ message.text }}</p>
          <p class="text-green-700">{{ message.userName }}</p>
        </div>

        <div v-if="message.type === 'error'" class="mb-4 rounded-lg bg-red-50 p-4 border border-red-200">
          <p class="text-red-800 font-semibold">{{ message.text }}</p>
        </div>

        <div class="mb-8">
          <label class="block text-lg font-semibold text-gray-900 mb-2">
            Escanea o ingresa el código QR:
          </label>
          <input
            ref="qrInput"
            v-model="qrToken"
            @keyup.enter="submitCheckIn"
            type="text"
            placeholder="Escanea un código QR..."
            class="w-full px-4 py-3 text-lg border-2 border-blue-500 rounded-lg focus:outline-none focus:border-blue-600"
            autofocus
          />
        </div>

        <button
          @click="submitCheckIn"
          :disabled="!qrToken || isLoading"
          class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 rounded-lg transition"
        >
          {{ isLoading ? 'Procesando...' : 'Validar Check-in' }}
        </button>

        <div class="mt-8 pt-8 border-t border-gray-200">
          <Link :href="route('admin.events.attendance', event.id)" class="text-blue-600 hover:text-blue-700 font-semibold">
            Ver Reporte de Asistencia →
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

defineProps({
  event: Object,
})

const qrInput = ref(null)
const qrToken = ref('')
const isLoading = ref(false)
const message = ref({ type: null, text: '', userName: '' })

const submitCheckIn = async () => {
  if (!qrToken.value) return

  isLoading.value = true
  message.value = { type: null, text: '', userName: '' }

  try {
    const response = await fetch(route('admin.events.check-in.store', { event: props.event.id }), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ qr_token: qrToken.value }),
    })

    const data = await response.json()

    if (response.ok) {
      message.value = {
        type: 'success',
        text: data.message,
        userName: `${data.user.name} (${data.user.email})`,
      }
      qrToken.value = ''
      qrInput.value?.focus()
    } else {
      message.value = { type: 'error', text: data.error }
    }
  } catch (error) {
    message.value = { type: 'error', text: 'Error al procesar check-in' }
  } finally {
    isLoading.value = false
  }
}
</script>
