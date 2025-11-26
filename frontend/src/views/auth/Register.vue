<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 card animate-slide-up">
      <div>
        <h2 class="text-center text-3xl font-bold text-gray-900">
          Create your account
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
            <label for="name" class="label">Full Name</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="input"
              placeholder="John Doe"
            />
          </div>
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
              minlength="8"
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
            <span v-if="loading">Creating account...</span>
            <span v-else">Sign up</span>
          </button>
        </div>

        <div class="text-center">
          <RouterLink to="/login" class="text-primary-600 hover:text-primary-700 font-medium">
            Already have an account? Sign in
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const userType = ref('buyer')
const form = ref({
  name: '',
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
      await authStore.registerBuyer(form.value)
    } else {
      await authStore.registerOrganizer(form.value)
    }

    router.push(userType.value === 'organizer' ? '/organizer/dashboard' : '/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
