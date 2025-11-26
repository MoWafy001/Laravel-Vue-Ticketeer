<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Checkout</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Order Summary -->
      <div class="card">
        <h2 class="text-2xl font-bold mb-4">Order Summary</h2>
        <div class="space-y-3">
          <div v-for="item in cartStore.items" :key="item.ticket_id" class="flex justify-between text-sm">
            <span>{{ item.quantity }}x {{ item.ticket.type }}</span>
            <span class="font-semibold">${{ (item.price * item.quantity).toFixed(2) }}</span>
          </div>
          <div class="border-t pt-3 flex justify-between font-bold text-lg">
            <span>Total:</span>
            <span class="text-primary-600">${{ cartStore.totalPrice.toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Payment -->
      <div class="card">
        <h2 class="text-2xl font-bold mb-4">Payment</h2>
        
        <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
          {{ error }}
        </div>

        <div v-if="success" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
          Order placed successfully! Redirecting...
        </div>

        <button
          @click="handleCheckout"
          :disabled="loading || success"
          class="btn btn-primary w-full py-3 text-lg"
        >
          <span v-if="loading">Processing...</span>
          <span v-else-if="success">Order Placed!</span>
          <span v-else>Place Order (${{ cartStore.totalPrice.toFixed(2) }})</span>
        </button>

        <p class="text-xs text-gray-500 mt-4 text-center">
          Stripe integration placeholder - order will be created
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import api from '@/api/client'

const router = useRouter()
const cartStore = useCartStore()

const loading = ref(false)
const error = ref('')
const success = ref(false)

async function handleCheckout() {
  loading.value = true
  error.value = ''

  // Validation: check cart items
  if (!cartStore.items.length) {
    error.value = 'Your cart is empty.'
    loading.value = false
    return
  }
  for (const item of cartStore.items) {
    // Use item.ticket_id for backend compatibility
    if (!item.ticket_id || !item.quantity || item.quantity < 1) {
      error.value = 'Invalid ticket or quantity in cart.'
      loading.value = false
      return
    }
  }

  try {
    // Create order
    const orderData = {
      items: cartStore.items.map(item => ({
        ticket_id: item.ticket_id,
        quantity: item.quantity
      }))
    }

    const orderResponse = await api.post('/orders', orderData)
    const orderId = orderResponse.data.data.id

    // Stripe integration
    const paymentResponse = await api.post('/payments/checkout', {
      order_id: orderId,
      payment_method: 'stripe',
      return_url: window.location.origin + '/my-tickets',
    })
    const checkoutUrl = paymentResponse.data.data.checkout_url
    window.location.href = checkoutUrl

    // Remove success.value = true and cartStore.clearCart here, as Stripe will redirect after payment
  } catch (err) {
    error.value = err.response?.data?.message || 'Checkout failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
