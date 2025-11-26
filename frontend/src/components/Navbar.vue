<template>
  <nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <RouterLink to="/" class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-xl">T</span>
            </div>
            <span class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
              Ticketeer
            </span>
          </RouterLink>
          
          <div class="hidden md:ml-10 md:flex md:space-x-8">
            <RouterLink to="/events" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
              Events
            </RouterLink>
            <RouterLink v-if="authStore.isOrganizer" to="/organizer/dashboard" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
              Dashboard
            </RouterLink>
          </div>
        </div>

        <div class="flex items-center space-x-4">
          <RouterLink v-if="authStore.isBuyer" to="/cart" class="relative p-2 text-gray-700 hover:text-primary-600 transition-colors">
            <ShoppingCartIcon class="h-6 w-6" />
            <span v-if="cartStore.totalItems > 0" class="absolute -top-1 -right-1 bg-accent-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-scale-in">
              {{ cartStore.totalItems }}
            </span>
          </RouterLink>

          <div v-if="authStore.isAuthenticated" class="flex items-center space-x-3">
            <RouterLink v-if="authStore.isBuyer" to="/my-tickets" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
              My Tickets
            </RouterLink>
            
            <!-- Profile Dropdown -->
            <div class="relative">
              <button
                @click="showProfileMenu = !showProfileMenu"
                class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              >
                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 flex items-center justify-center text-white font-medium">
                  {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                </div>
                <span class="hidden md:inline">{{ authStore.user?.name }}</span>
                <ChevronDownIcon class="h-4 w-4" />
              </button>

              <!-- Dropdown Menu -->
              <div
                v-if="showProfileMenu"
                @click="showProfileMenu = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200 animate-slide-down"
              >
                <div class="px-4 py-2 border-b border-gray-200">
                  <p class="text-sm font-semibold text-gray-900">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
                </div>
                
                <RouterLink
                  v-if="authStore.isOrganizer"
                  to="/organizer/dashboard"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                >
                  Dashboard
                </RouterLink>
                
                <RouterLink
                  v-if="authStore.isBuyer"
                  to="/my-tickets"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                >
                  My Tickets
                </RouterLink>
                
                <button
                  @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>

          <div v-else class="flex items-center space-x-2">
            <RouterLink to="/login" class="btn btn-secondary text-sm">
              Login
            </RouterLink>
            <RouterLink to="/register" class="btn btn-primary text-sm">
              Sign Up
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useRouter } from 'vue-router'
import { ShoppingCartIcon, ChevronDownIcon } from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()
const showProfileMenu = ref(false)

async function handleLogout() {
  showProfileMenu.value = false
  await authStore.logout()
  router.push('/')
}
</script>
