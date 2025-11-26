<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
      <div v-if="loading">
        <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-primary-600 mx-auto"></div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Verifying Payment...</h2>
        <p class="mt-2 text-sm text-gray-600">Please wait while we confirm your transaction.</p>
      </div>

      <div v-else-if="success">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
          <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Payment Successful!</h2>
        <p class="mt-2 text-sm text-gray-600">Your order has been confirmed.</p>
        <div class="mt-6">
          <router-link to="/my-tickets" class="btn btn-primary w-full">
            View My Tickets
          </router-link>
        </div>
      </div>

      <div v-else>
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
          <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Payment Failed</h2>
        <p class="mt-2 text-sm text-gray-600">{{ error }}</p>
        <div class="mt-6 space-y-3">
          <router-link to="/checkout" class="btn btn-primary w-full">
            Try Again
          </router-link>
          <router-link to="/cart" class="btn btn-secondary w-full">
            Return to Cart
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import api from '@/api/client'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

const loading = ref(true)
const success = ref(false)
const error = ref('')

onMounted(async () => {
  const sessionId = route.query.session_id
  const isCancel = route.query.cancel

  if (isCancel) {
    loading.value = false
    error.value = 'Payment was cancelled.'
    return
  }

  if (!sessionId) {
    loading.value = false
    error.value = 'Invalid payment session.'
    return
  }

  try {
    await api.post('/payments/verify', { session_id: sessionId })
    success.value = true
    cartStore.clearCart()
    
    // Auto redirect after a short delay
    setTimeout(() => {
      router.push('/my-tickets')
    }, 3000)
  } catch (err) {
    console.error('Payment verification failed:', err)
    error.value = err.response?.data?.message || 'Failed to verify payment.'
  } finally {
    loading.value = false
  }
})
</script>
