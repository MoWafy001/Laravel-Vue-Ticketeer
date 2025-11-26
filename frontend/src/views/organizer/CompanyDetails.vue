<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <RouterLink to="/organizer/companies" class="text-primary-600 hover:text-primary-700 mb-4 inline-block">
      ← Back to Companies
    </RouterLink>
    
    <div v-if="loading" class="animate-pulse">
      <div class="h-8 bg-gray-300 rounded w-1/3 mb-4"></div>
      <div class="h-4 bg-gray-300 rounded w-1/4"></div>
    </div>

    <div v-else-if="company">
      <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ company.name }}</h1>
      <p class="text-gray-600 mb-8">Company Details</p>

      <div class="card">
        <h2 class="text-2xl font-bold mb-4">Company Information</h2>
        <p class="text-gray-600">Created: {{ formatDate(company.created_at) }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/api/client'

const route = useRoute()
const company = ref(null)
const loading = ref(true)

async function loadCompany() {
  try {
    const response = await api.get(`/companies/${route.params.id}`)
    company.value = response.data.data
  } catch (error) {
    console.error('Failed to load company:', error)
  } finally {
    loading.value = false
  }
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
}

onMounted(() => {
  loadCompany()
})
</script>
