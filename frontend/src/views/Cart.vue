<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    <div v-if="cartStore.items.length === 0" class="card text-center py-12">
      <p class="text-gray-500 text-lg mb-4">Your cart is empty</p>
      <RouterLink to="/events" class="btn btn-primary">
        Browse Events
      </RouterLink>
    </div>

    <div v-else class="space-y-6">
      <div class="card">
        <div class="space-y-4">
          <div v-for="item in cartStore.items" :key="item.ticket_id" class="flex items-center justify-between border-b border-gray-200 pb-4 last:border-0">
            <div class="flex-1">
              <h3 class="font-semibold text-lg">{{ item.ticket.type }}</h3>
              <p class="text-sm text-gray-600">{{ item.ticket.event?.name }}</p>
              <p class="text-primary-600 font-semibold">${{ item.price }} each</p>
            </div>

            <div class="flex items-center space-x-4">
              <div class="flex items-center space-x-2">
                <button
                  @click="cartStore.updateQuantity(item.ticket_id, item.quantity - 1)"
                  class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                >
                  -
                </button>
                <span class="w-12 text-center font-semibold">{{ item.quantity }}</span>
                <button
                  @click="cartStore.updateQuantity(item.ticket_id, item.quantity + 1)"
                  class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                >
                  +
                </button>
              </div>

              <p class="w-24 text-right font-bold">${{ (item.price * item.quantity).toFixed(2) }}</p>

              <button
                @click="cartStore.removeItem(item.ticket_id)"
                class="text-red-600 hover:text-red-700"
              >
                Remove
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card bg-gray-50">
        <div class="flex justify-between items-center mb-4">
          <span class="text-lg font-semibold">Total Items:</span>
          <span class="text-lg">{{ cartStore.totalItems }}</span>
        </div>
        <div class="flex justify-between items-center mb-6">
          <span class="text-2xl font-bold">Total:</span>
          <span class="text-3xl font-bold text-primary-600">${{ cartStore.totalPrice.toFixed(2) }}</span>
        </div>
        <RouterLink to="/checkout" class="btn btn-primary w-full py-3 text-lg">
          Proceed to Checkout
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '@/stores/cart'

const cartStore = useCartStore()
</script>
