<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Organizer Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="card bg-gradient-to-br from-primary-500 to-secondary-600 text-white">
        <h3 class="text-lg font-semibold mb-2">Total Companies</h3>
        <p class="text-4xl font-bold">{{ stats.companies }}</p>
      </div>
      <div class="card bg-gradient-to-br from-secondary-500 to-accent-600 text-white">
        <h3 class="text-lg font-semibold mb-2">Total Events</h3>
        <p class="text-4xl font-bold">{{ stats.events }}</p>
      </div>
      <div class="card bg-gradient-to-br from-accent-500 to-pink-600 text-white">
        <h3 class="text-lg font-semibold mb-2">Tickets Sold</h3>
        <p class="text-4xl font-bold">{{ stats.tickets }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="card">
        <h2 class="text-2xl font-bold mb-4">Quick Actions</h2>
        <div class="space-y-2">
          <RouterLink to="/organizer/companies" class="btn btn-primary w-full">
            Manage Companies
          </RouterLink>
          <RouterLink to="/organizer/events" class="btn btn-secondary w-full">
            Manage Events
          </RouterLink>
        </div>
      </div>

      <div class="card">
        <h2 class="text-2xl font-bold mb-4">Recent Activity</h2>
        <p class="text-gray-500">No recent activity</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/client'

const stats = ref({
  companies: 0,
  events: 0,
  tickets: 0
})

onMounted(async () => {
  try {
    const companiesRes = await api.get('/companies')
    stats.value.companies = companiesRes.data.meta?.pagination?.total || 0
  } catch (error) {
    console.error('Failed to load stats:', error)
  }
})
</script>
