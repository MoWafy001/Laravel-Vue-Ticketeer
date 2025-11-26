<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-4xl font-bold text-gray-900">My Companies</h1>
      <button @click="showCreateModal = true" class="btn btn-primary">
        Create Company
      </button>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="i in 2" :key="i" class="card animate-pulse">
        <div class="h-24 bg-gray-300 rounded"></div>
      </div>
    </div>

    <div v-else-if="companies.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="company in companies" :key="company.id" class="card hover:shadow-xl transition-shadow cursor-pointer" @click="$router.push(`/organizer/companies/${company.id}`)">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ company.name }}</h3>
        <p class="text-gray-600 text-sm">Created {{ formatDate(company.created_at) }}</p>
      </div>
    </div>

    <div v-else class="card text-center py-12">
      <p class="text-gray-500 text-lg mb-4">You haven't created any companies yet</p>
      <button @click="showCreateModal = true" class="btn btn-primary">
        Create Your First Company
      </button>
    </div>

    <!-- Create Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showCreateModal = false">
      <div class="card max-w-md w-full mx-4 animate-scale-in">
        <h2 class="text-2xl font-bold mb-4">Create Company</h2>
        <form @submit.prevent="createCompany">
          <div class="mb-4">
            <label class="label">Company Name</label>
            <input v-model="newCompany.name" type="text" required class="input" placeholder="Enter company name" />
          </div>
          <div class="flex space-x-2">
            <button type="submit" :disabled="creating" class="btn btn-primary flex-1">
              {{ creating ? 'Creating...' : 'Create' }}
            </button>
            <button type="button" @click="showCreateModal = false" class="btn btn-secondary flex-1">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/client'

const companies = ref([])
const loading = ref(true)
const showCreateModal = ref(false)
const newCompany = ref({ name: '' })
const creating = ref(false)

async function loadCompanies() {
  try {
    const response = await api.get('/companies')
    companies.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load companies:', error)
  } finally {
    loading.value = false
  }
}

async function createCompany() {
  creating.value = true
  try {
    await api.post('/companies', newCompany.value)
    showCreateModal.value = false
    newCompany.value = { name: '' }
    await loadCompanies()
  } catch (error) {
    alert('Failed to create company')
  } finally {
    creating.value = false
  }
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(() => {
  loadCompanies()
})
</script>
