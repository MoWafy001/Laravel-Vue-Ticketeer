<template>
  <div class="card print-card" :class="{ 'print-mode': printMode }">
    <div class="flex flex-col md:flex-row gap-6">
      <!-- QR Code Section -->
      <div class="flex-shrink-0">
        <div class="bg-white p-4 rounded-lg border-2 border-gray-200">
          <qrcode-vue
            v-if="ticket.qr_code"
            :value="ticket.qr_code"
            :size="180"
            level="H"
          />
          <div v-else class="text-gray-400 text-sm text-center py-8">
            No QR code available
          </div>
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
      </div>
    </div>
    <div class="flex justify-end mt-6">
      <button class="btn btn-primary" @click="printTicket">
        Print Ticket
      </button>
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
const printMode = ref(false)

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
    // Ensure response is a valid PDF blob
    const blob = new Blob([response.data], { type: 'application/pdf' })
    if (blob.size < 1000) {
      alert('PDF file is invalid. Please contact support.')
      downloading.value = false
      return
    }
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `ticket-${props.ticket.id}.pdf`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Failed to download PDF:', error)
    alert('Failed to download ticket PDF. Please try again.')
  } finally {
    downloading.value = false
  }
}

function printTicket() {
  printMode.value = true
  setTimeout(() => {
    window.print()
    printMode.value = false
  }, 100)
}
</script>

<style scoped>
.print-card {
  background: #fff;
  box-shadow: 0 0 0 1px #e5e7eb;
  padding: 2rem;
  margin: 2rem auto;
  max-width: 600px;
}
@media print {
  body * {
    visibility: hidden !important;
  }
  .print-card, .print-card * {
    visibility: visible !important;
  }
  .print-card {
    position: absolute !important;
    left: 0;
    top: 0;
    width: 100vw;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 0 !important;
    z-index: 9999 !important;
  }
  .btn {
    display: none !important;
  }
  nav, .navbar, .bg-white.shadow-lg.sticky.top-0.z-50 {
    display: none !important;
    visibility: hidden !important;
    height: 0 !important;
    position: static !important;
  }
}
</style>
