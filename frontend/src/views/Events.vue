<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">Browse Events</h1>
      
      <!-- Filters -->
      <div class="card mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search events..."
            class="input"
            @input="loadEvents"
          />
          <select v-model="filters.status" class="input" @change="loadEvents">
            <option value="">All Events</option>
            <option value="upcoming">Upcoming</option>
            <option value="ongoing">Ongoing</option>
            <option value="on_sale">On Sale</option>
          </select>
          <select v-model="filters.sort_by" class="input" @change="loadEvents">
            <option value="start_time">Sort by Date</option>
            <option value="name">Sort by Name</option>
          </select>
          <select v-model="filters.sort_order" class="input" @change="loadEvents">
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Events Grid -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="card animate-pulse">
        <div class="h-48 bg-gray-300 rounded-lg mb-4"></div>
        <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
        <div class="h-4 bg-gray-300 rounded w-1/2"></div>
      </div>
    </div>

    <div v-else-if="events.length > 0">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <EventCard v-for="event in events" :key="event.id" :event="event" />
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total_pages > 1" class="flex justify-center space-x-2">
        <button
          v-for="page in pagination.total_pages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-colors',
            page === pagination.current_page
              ? 'bg-primary-600 text-white'
              : 'bg-white text-gray-700 hover:bg-gray-100'
          ]"
        >
          {{ page }}
        </button>
      </div>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-500 text-lg">No events found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/client'
import EventCard from '@/components/EventCard.vue'

const events = ref([])
const loading = ref(true)
const filters = ref({
  search: '',
  status: '',
  sort_by: 'start_time',
  sort_order: 'asc',
  per_page: 12
})
const pagination = ref({
  current_page: 1,
  total_pages: 1,
  total: 0
})

async function loadEvents() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    Object.entries(filters.value).forEach(([key, value]) => {
      if (value) params.append(key, value)
    })
    params.append('page', pagination.value.current_page)

    const response = await api.get(`/events?${params}`)
    events.value = response.data.data || []
    if (response.data.meta?.pagination) {
      pagination.value = response.data.meta.pagination
    }
  } catch (error) {
    console.error('Failed to load events:', error)
  } finally {
    loading.value = false
  }
}

function goToPage(page) {
  pagination.value.current_page = page
  loadEvents()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  loadEvents()
})
</script>
