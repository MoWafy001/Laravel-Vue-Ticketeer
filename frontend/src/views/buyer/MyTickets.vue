<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-4xl font-bold text-gray-900">My Tickets</h1>

      <!-- Filters -->
      <div class="flex items-center space-x-4">
        <select v-model="filters.status" @change="loadTickets" class="input">
          <option value="">All Status</option>
          <option value="valid">Valid</option>
          <option value="used">Used</option>
          <option value="cancelled">Cancelled</option>
          <option value="expired">Expired</option>
        </select>
      </div>
    </div>

    <Alert
      v-if="alert.show"
      :type="alert.type"
      :message="alert.message"
      @close="alert.show = false"
    />

    <LoadingSpinner v-if="loading" message="Loading your tickets..." />

    <div v-else-if="tickets.length > 0" class="space-y-6">
      <TicketCard
        v-for="ticket in tickets"
        :key="ticket.id"
        :ticket="ticket"
        @cancel="confirmCancel(ticket)"
      />
    </div>

    <div v-else class="card text-center py-12">
      <p class="text-gray-500 text-lg mb-4">You don't have any tickets yet</p>
      <RouterLink to="/events" class="btn btn-primary">
        Browse Events
      </RouterLink>
    </div>

    <!-- Cancel Confirmation Modal -->
    <Modal
      :show="cancelModal.show"
      title="Cancel Ticket"
      confirm-text="Confirm Cancellation"
      cancel-text="Keep Ticket"
      :loading="cancelModal.loading"
      @close="cancelModal.show = false"
      @confirm="handleCancelTicket"
    >
      <p class="text-gray-700">
        Are you sure you want to cancel this ticket? You will receive a refund of
        <span class="font-bold">${{ cancelModal.ticket?.ticket?.price?.toFixed(2) }}</span>.
      </p>
      <p class="text-sm text-gray-500 mt-2">
        This action cannot be undone.
      </p>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { ticketsApi } from '@/api'
import TicketCard from '@/components/TicketCard.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Alert from '@/components/Alert.vue'
import Modal from '@/components/Modal.vue'

const tickets = ref([])
const loading = ref(true)
const filters = reactive({
  status: ''
})

const alert = reactive({
  show: false,
  type: 'success',
  message: ''
})

const cancelModal = reactive({
  show: false,
  loading: false,
  ticket: null
})

async function loadTickets() {
  loading.value = true
  try {
    const params = {}
    if (filters.status) params.status = filters.status

    const response = await ticketsApi.getMyTickets(params)
    tickets.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load tickets:', error)
    showAlert('error', 'Failed to load tickets. Please try again.')
  } finally {
    loading.value = false
  }
}

function confirmCancel(ticket) {
  cancelModal.ticket = ticket
  cancelModal.show = true
}

async function handleCancelTicket() {
  cancelModal.loading = true
  try {
    await ticketsApi.cancelTicket(cancelModal.ticket.id)
    showAlert('success', 'Ticket cancelled successfully. Refund is being processed.')
    cancelModal.show = false
    await loadTickets()
  } catch (error) {
    showAlert('error', error.response?.data?.message || 'Failed to cancel ticket.')
  } finally {
    cancelModal.loading = false
  }
}

function showAlert(type, message) {
  alert.type = type
  alert.message = message
  alert.show = true
}

const route = useRoute()
const cartStore = useCartStore()

onMounted(() => {
  // Clear cart if redirected from Stripe success
  if (route.query.success === 'true') {
    cartStore.clearCart()
  }
  loadTickets()
})
</script>
