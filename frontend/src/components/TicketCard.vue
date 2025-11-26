<template>
  <div class="card">
    <div class="flex flex-col md:flex-row gap-6">
      <!-- QR Code Section -->
      <div class="flex-shrink-0">
        <div class="bg-white p-4 rounded-lg border-2 border-gray-200">
          <qrcode-vue
            :value="ticket.qr_code"
            :size="180"
            level="H"
          />
        </div>
        <div class="mt-3 text-center">
          <span :class="[
            'inline-block px-3 py-1 rounded-full text-sm font-semibold',
            statusClass
          ]">
            {{ statusText }}
          </span>
        </div>
      </div>

      <!-- Ticket Details -->
      <div class="flex-1">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">
              {{ ticket.ticket?.event?.name }}
            </h3>
            <p class="text-gray-600">
              {{ ticket.ticket?.event?.company?.name }}
            </p>
          </div>
          <button
            v-if="showDownload"
            @click="downloadPDF"
            class="btn btn-secondary flex items-center gap-2"
            :disabled="downloading"
          >
            <ArrowDownTrayIcon class="h-5 w-5" />
            {{ downloading ? 'Downloading...' : 'Download PDF' }}
          </button>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <p class="text-sm text-gray-500 mb-1">Ticket Type</p>
            <p class="font-semibold">{{ ticket.ticket?.type }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500 mb-1">Price</p>
            <p class="font-semibold">
              {{ typeof ticket.ticket?.price === 'number' ? '$' + ticket.ticket.price.toFixed(2) : (typeof ticket.ticket?.price === 'string' ? '$' + Number(ticket.ticket.price).toFixed(2) : 'N/A') }}
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-500 mb-1">Event Date</p>
            <p class="font-semibold">{{ formatDateTime(ticket.ticket?.event?.start_time) }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500 mb-1">Purchase Date</p>
            <p class="font-semibold">{{ formatDate(ticket.purchased_at) }}</p>
          </div>
        </div>

        <!-- Time remaining -->
        <div v-if="ticket.status === 'valid' && ticket.time_remaining" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
          <p class="text-sm text-blue-800">
            <span class="font-semibold">Time until event:</span> {{ ticket.time_remaining }}
          </p>
        </div>

        <!-- Used status -->
        <div v-if="ticket.used_at" class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
          <p class="text-sm text-gray-700">
            <span class="font-semibold">Used on:</span> {{ formatDateTime(ticket.used_at) }}
          </p>
        </div>

        <!-- Refund section -->
        <div v-if="ticket.can_refund && ticket.status === 'valid'" class="mt-4 pt-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-700">Eligible for refund</p>
              <p class="text-xs text-gray-500">Until {{ formatDate(ticket.refund_deadline) }}</p>
            </div>
            <button
              @click="$emit('cancel')"
              class="btn bg-red-600 text-white hover:bg-red-700"
            >
              Cancel & Refund
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import QrcodeVue from 'qrcode-vue3'
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import { ticketsApi } from '@/api'

const props = defineProps({
  ticket: {
    type: Object,
    required: true
  },
  showDownload: {
    type: Boolean,
    default: true
  }
})

defineEmits(['cancel'])

const downloading = ref(false)

const statusClass = computed(() => {
  const classes = {
    valid: 'bg-green-100 text-green-800',
    expired: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-red-100 text-red-800',
    used: 'bg-blue-100 text-blue-800'
  }
  return classes[props.ticket.status] || 'bg-gray-100 text-gray-800'
})

const statusText = computed(() => {
  return props.ticket.status.charAt(0).toUpperCase() + props.ticket.status.slice(1)
})

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

function formatDateTime(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

async function downloadPDF() {
  downloading.value = true
  try {
    const response = await ticketsApi.downloadTicketPDF(props.ticket.id)
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `ticket-${props.ticket.id}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Failed to download PDF:', error)
    alert('Failed to download ticket PDF. Please try again.')
  } finally {
    downloading.value = false
  }
}
</script>
