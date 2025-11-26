<template>
  <div class="min-h-screen">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-secondary-600 to-accent-600 text-white py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center animate-fade-in">
          <h1 class="text-5xl md:text-6xl font-bold mb-6">
            Discover Amazing Events
          </h1>
          <p class="text-xl md:text-2xl mb-8 text-purple-100">
            Book tickets for concerts, conferences, and more
          </p>
          <RouterLink to="/events" class="btn btn-accent text-lg px-8 py-3 inline-block">
            Browse Events
          </RouterLink>
        </div>
      </div>
      <div class="absolute inset-0 bg-black opacity-10"></div>
    </section>

    <!-- Featured Events -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <h2 class="text-3xl font-bold mb-8 text-gray-900">Featured Events</h2>
      
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="i in 3" :key="i" class="card animate-pulse">
          <div class="h-48 bg-gray-300 rounded-lg mb-4"></div>
          <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
          <div class="h-4 bg-gray-300 rounded w-1/2"></div>
        </div>
      </div>

      <div v-else-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <EventCard v-for="event in events" :key="event.id" :event="event" />
      </div>

      <div v-else class="text-center py-12">
        <p class="text-gray-500 text-lg">No events available at the moment.</p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/client'
import EventCard from '@/components/EventCard.vue'

const events = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get('/events?per_page=6&status=upcoming')
    events.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load events:', error)
  } finally {
    loading.value = false
  }
})
</script>
