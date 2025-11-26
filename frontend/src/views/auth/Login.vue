<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 card animate-slide-up">
      <div>
        <h2 class="text-center text-3xl font-bold text-gray-900">
          Sign in to Ticketeer
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Choose your account type
        </p>
      </div>

      <div class="flex space-x-4 mb-6">
        <button
          @click="userType = 'buyer'"
          :class="[
            'flex-1 py-3 px-4 rounded-lg font-medium transition-all',
            userType === 'buyer'
              ? 'bg-gradient-to-r from-primary-600 to-secondary-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          Buyer
        </button>
        <button
          @click="userType = 'organizer'"
          :class="[
            'flex-1 py-3 px-4 rounded-lg font-medium transition-all',
            userType === 'organizer'
              ? 'bg-gradient-to-r from-primary-600 to-secondary-600 text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          Organizer
        </button>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="handleSubmit">
        <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
          {{ error }}
        </div>

        <div class="space-y-4">
          <div>
            <label for="email" class="label">Email address</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="input"
              placeholder="you@example.com"
            />
          </div>
          <div>
            <label for="password" class="label">Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="input"
              placeholder="••••••••"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="w-full btn btn-primary py-3"
          >
            <span v-if="loading">Signing in...</span>
            <span v-else>Sign in</span>
          </button>
        </div>

        <div class="text-center">
          <RouterLink to="/register" class="text-primary-600 hover:text-primary-700 font-medium">
            Don't have an account? Sign up
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const userType = ref('buyer')
const form = ref({
  email: '',
  password: ''
})
const loading = ref(false)
const error = ref('')

async function handleSubmit() {
  loading.value = true
  error.value = ''

  try {
    if (userType.value === 'buyer') {
      await authStore.loginBuyer(form.value)
    } else {
      await authStore.loginOrganizer(form.value)
    }

    const redirect = route.query.redirect || (userType.value === 'organizer' ? '/organizer/dashboard' : '/')
    router.push(redirect)
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed. Please check your credentials.'
  } finally {
    loading.value = false
  }
}
</script>
