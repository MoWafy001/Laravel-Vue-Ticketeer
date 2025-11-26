<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">My Tickets</h1>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="i in 4" :key="i" class="card animate-pulse">
        <div class="h-32 bg-gray-300 rounded"></div>
      </div>
    </div>

    <div v-else-if="tickets.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="ticket in tickets" :key="ticket.id" class="card">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold">{{ ticket.ticket?.event?.name }}</h3>
            <p class="text-gray-600">{{ ticket.ticket?.type }}</p>
          </div>
          <span :class="[
            'px-3 py-1 rounded-full text-xs font-semibold',
            ticket.status === 'valid' ? 'bg-green-100 text-green-800' :
            ticket.status === 'used' ? 'bg-gray-100 text-gray-800' :
            'bg-red-100 text-red-800'
          ]">
            {{ ticket.status }}
          </span>
        </div>

        <div class="space-y-2 text-sm text-gray-600 mb-4">
          <p><span class="font-semibold">QR Code:</span> {{ ticket.qr_code?.substring(0, 16) }}...</p>
          <p><span class="font-semibold">Valid Until:</span> {{ formatDate(ticket.valid_until) }}</p>
          <p v-if="ticket.used_at"><span class="font-semibold">Used At:</span> {{ formatDate(ticket.used_at) }}</p>
        </div>

        <div class="flex space-x-2">
          <button class="btn btn-secondary flex-1">View QR Code</button>
          <button class="btn btn-secondary flex-1">Download PDF</button>
        </div>
      </div>
    </div>

    <div v-else class="card text-center py-12">
      <p class="text-gray-500 text-lg mb-4">You don't have any tickets yet</p>
      <RouterLink to="/events" class="btn btn-primary">
        Browse Events
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/client'

const tickets = ref([])
const loading = ref(true)

async function loadTickets() {
  try {
    const response = await api.get('/buyer/tickets')
    tickets.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load tickets:', error)
  } finally {
    loading.value = false
  }
}

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadTickets()
})
</script>
