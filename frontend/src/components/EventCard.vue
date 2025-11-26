<template>
  <RouterLink :to="`/events/${event.id}`" class="card group cursor-pointer transform hover:scale-105 transition-all duration-200">
    <div class="relative h-48 bg-gradient-to-br from-primary-400 to-secondary-500 rounded-lg mb-4 overflow-hidden">
      <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
      <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
        <span class="inline-block px-3 py-1 bg-white/90 text-primary-700 rounded-full text-xs font-semibold">
          {{ formatDate(event.start_time) }}
        </span>
      </div>
    </div>
    
    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
      {{ event.name }}
    </h3>
    
    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
      {{ event.description || 'No description available' }}
    </p>
    
    <div class="flex items-center justify-between">
      <span class="text-sm text-gray-500">
        {{ event.company?.name || 'Unknown Organizer' }}
      </span>
      <span class="text-primary-600 font-semibold">
        View Details →
      </span>
    </div>
  </RouterLink>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
  event: {
    type: Object,
    required: true
  }
})

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
</script>
