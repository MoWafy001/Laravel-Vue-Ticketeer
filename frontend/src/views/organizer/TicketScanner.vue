<template>
  <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="card">
      <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Ticket Scanner</h1>

      <Alert
        v-if="alert.show"
        :type="alert.type"
        :message="alert.message"
        :title="alert.title"
        @close="alert.show = false"
      />

      <!-- Manual QR Input -->
      <div class="mb-6">
        <FormInput
          v-model="qrCode"
          label="Enter QR Code"
          placeholder="Scan or enter QR code..."
          @keyup.enter="scanTicket"
        />
        <button
          @click="scanTicket"
          :disabled="!qrCode || scanning"
          class="btn btn-primary w-full mt-4"
        >
          {{ scanning ? 'Scanning...' : 'Scan Ticket' }}
        </button>
      </div>

      <!-- Scan Result -->
      <div v-if="lastScannedTicket" class="mt-8 pt-8 border-t border-gray-200">
        <h2 class="text-xl font-bold mb-4">Last Scanned Ticket</h2>
        
        <div
          :class="[
            'p-6 rounded-lg',
            lastScannedTicket.valid ? 'bg-green-50 border-2 border-green-500' : 'bg-red-50 border-2 border-red-500'
          ]"
        >
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-lg font-semibold">{{ lastScannedTicket.event?.name }}</h3>
              <p class="text-sm text-gray-600">{{ lastScannedTicket.ticket_type }}</p>
            </div>
            <div
              :class="[
                'px-4 py-2 rounded-full font-bold',
                lastScannedTicket.valid ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
              ]"
            >
              {{ lastScannedTicket.valid ? 'VALID' : 'INVALID' }}
            </div>
          </div>

          <div class="space-y-2 text-sm">
            <p>
              <span class="font-semibold">Status:</span>
              <span :class="lastScannedTicket.valid ? 'text-green-700' : 'text-red-700'">
                {{ lastScannedTicket.status }}
              </span>
            </p>
            <p v-if="lastScannedTicket.buyer">
              <span class="font-semibold">Buyer:</span> {{ lastScannedTicket.buyer.name }}
            </p>
            <p v-if="lastScannedTicket.buyer">
              <span class="font-semibold">Email:</span> {{ lastScannedTicket.buyer.email }}
            </p>
            <p v-if="lastScannedTicket.used_at">
              <span class="font-semibold">Used At:</span> {{ formatDateTime(lastScannedTicket.used_at) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Scan History -->
      <div v-if="scanHistory.length > 0" class="mt-8 pt-8 border-t border-gray-200">
        <h2 class="text-xl font-bold mb-4">Scan History</h2>
        <div class="space-y-2">
          <div
            v-for="(scan, index) in scanHistory.slice(0, 10)"
            :key="index"
            class="flex items-center justify-between p-3 border rounded-lg"
            :class="scan.valid ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'"
          >
            <div>
              <p class="font-semibold text-sm">{{ scan.ticket_type }}</p>
              <p class="text-xs text-gray-600">{{ scan.buyer?.name }}</p>
            </div>
            <div class="text-right">
              <span
                :class="[
                  'inline-block px-2 py-1 rounded text-xs font-semibold',
                  scan.valid ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
                ]"
              >
                {{ scan.valid ? 'VALID' : 'INVALID' }}
              </span>
              <p class="text-xs text-gray-500 mt-1">{{ formatTime(scan.scannedAt) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { ticketsApi } from '@/api'
import FormInput from '@/components/FormInput.vue'
import Alert from '@/components/Alert.vue'

const qrCode = ref('')
const scanning = ref(false)
const lastScannedTicket = ref(null)
const scanHistory = ref([])

const alert = reactive({
  show: false,
  type: 'success',
  title: '',
  message: ''
})

async function scanTicket() {
  if (!qrCode.value.trim()) return

  scanning.value = true
  alert.show = false

  try {
    const response = await ticketsApi.scanTicket(qrCode.value)
    const ticket = response.data.data

    lastScannedTicket.value = ticket

    // Add to history
    scanHistory.value.unshift({
      ...ticket,
      scannedAt: new Date()
    })

    // Show alert
    if (ticket.valid) {
      showAlert('success', '✓ Valid Ticket', `Ticket successfully validated for ${ticket.buyer?.name}`)
    } else {
      showAlert('error', '✗ Invalid Ticket', ticket.status === 'used' ? 'Ticket has already been used' : 'Ticket is not valid')
    }

    // Clear input
    qrCode.value = ''
  } catch (error) {
    showAlert('error', 'Scan Failed', error.response?.data?.message || 'Failed to scan ticket')
  } finally {
    scanning.value = false
  }
}

function showAlert(type, title, message) {
  alert.type = type
  alert.title = title
  alert.message = message
  alert.show = true

  // Auto-hide after 5 seconds
  setTimeout(() => {
    alert.show = false
  }, 5000)
}

function formatDateTime(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatTime(date) {
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
