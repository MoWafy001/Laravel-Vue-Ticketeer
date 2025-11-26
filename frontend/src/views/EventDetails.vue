<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div v-if="loading" class="animate-pulse">
      <div class="h-64 bg-gray-300 rounded-xl mb-8"></div>
      <div class="h-8 bg-gray-300 rounded w-1/2 mb-4"></div>
      <div class="h-4 bg-gray-300 rounded w-3/4"></div>
    </div>

    <div v-else-if="event" class="space-y-8">
      <!-- Event Header -->
      <div class="card">
        <div class="h-64 bg-gradient-to-br from-primary-500 to-secondary-600 rounded-xl mb-6 flex items-center justify-center">
          <h1 class="text-5xl font-bold text-white">{{ event.name }}</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Event Details</h2>
            <p class="text-gray-700 mb-4">{{ event.description }}</p>
            
            <div class="space-y-2 text-sm">
              <p><span class="font-semibold">Organizer:</span> {{ event.company?.name }}</p>
              <p><span class="font-semibold">Start:</span> {{ formatDateTime(event.start_time) }}</p>
              <p><span class="font-semibold">End:</span> {{ formatDateTime(event.end_time) }}</p>
              <p><span class="font-semibold">Sale Period:</span> {{ formatDateTime(event.sale_start_time) }} - {{ formatDateTime(event.sale_end_time) }}</p>
            </div>
          </div>

          <!-- Tickets -->
          <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Available Tickets</h2>
            <div class="space-y-4">
              <div v-for="ticket in event.tickets" :key="ticket.id" class="border border-gray-200 rounded-lg p-4 hover:border-primary-500 transition-colors">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <h3 class="font-semibold text-lg">{{ ticket.type }}</h3>
                    <p class="text-sm text-gray-600">Code: {{ ticket.code }}</p>
                  </div>
                  <p class="text-2xl font-bold text-primary-600">${{ ticket.price }}</p>
                </div>
                
                <div class="flex items-center justify-between">
                  <p class="text-sm text-gray-600">
                    {{ ticket.available || ticket.quantity }} available
                  </p>
                  
                  <div class="flex items-center space-x-2">
                    <input
                      v-model.number="ticketQuantities[ticket.id]"
                      type="number"
                      min="1"
                      :max="ticket.available || ticket.quantity"
                      class="w-20 input text-center"
                    />
                    <button
                      @click="addToCart(ticket)"
                      class="btn btn-primary"
                      :disabled="!authStore.isBuyer"
                    >
                      Add to Cart
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="!authStore.isBuyer" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <p class="text-yellow-800">Please <RouterLink to="/login" class="font-semibold underline">login as a buyer</RouterLink> to purchase tickets</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-500 text-lg">Event not found</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import api from '@/api/client'

const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()

const event = ref(null)
const loading = ref(true)
const ticketQuantities = reactive({})

async function loadEvent() {
  try {
    const response = await api.get(`/events/${route.params.id}`)
    event.value = response.data.data
    
    // Load tickets separately
    const ticketsResponse = await api.get(`/events/${route.params.id}/tickets`)
    event.value.tickets = ticketsResponse.data.data || event.value.ticket_types || []
    
    // Initialize quantities
    event.value.tickets?.forEach(ticket => {
      ticketQuantities[ticket.id] = 1
    })
  } catch (error) {
    console.error('Failed to load event:', error)
  } finally {
    loading.value = false
  }
}

function addToCart(ticket) {
  const quantity = ticketQuantities[ticket.id] || 1
  cartStore.addItem(ticket, quantity)
  alert(`Added ${quantity} ticket(s) to cart!`)
}

function formatDateTime(dateString) {
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
  loadEvent()
})
</script>
