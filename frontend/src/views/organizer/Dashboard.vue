<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-4xl font-bold text-gray-900">Organizer Dashboard</h1>
      <div class="flex items-center space-x-3">
        <RouterLink to="/organizer/companies" class="btn btn-secondary">
          Manage Companies
        </RouterLink>
        <RouterLink to="/organizer/events" class="btn btn-primary">
          Manage Events
        </RouterLink>
      </div>
    </div>

    <LoadingSpinner v-if="loading" message="Loading dashboard..." />

    <div v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="card bg-gradient-to-br from-primary-500 to-secondary-600 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm opacity-90 mb-1">Total Companies</p>
              <p class="text-3xl font-bold">{{ stats.totalCompanies }}</p>
            </div>
            <BuildingOfficeIcon class="h-12 w-12 opacity-50" />
          </div>
        </div>

        <div class="card bg-gradient-to-br from-secondary-500 to-accent-600 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm opacity-90 mb-1">Total Events</p>
              <p class="text-3xl font-bold">{{ stats.totalEvents }}</p>
            </div>
            <CalendarIcon class="h-12 w-12 opacity-50" />
          </div>
        </div>

        <div class="card bg-gradient-to-br from-green-500 to-emerald-600 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm opacity-90 mb-1">Upcoming Events</p>
              <p class="text-3xl font-bold">{{ stats.upcomingEvents }}</p>
            </div>
            <ClockIcon class="h-12 w-12 opacity-50" />
          </div>
        </div>

        <div class="card bg-gradient-to-br from-accent-500 to-pink-600 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm opacity-90 mb-1">Active Events</p>
              <p class="text-3xl font-bold">{{ stats.activeEvents }}</p>
            </div>
            <TicketIcon class="h-12 w-12 opacity-50" />
          </div>
        </div>

        <div class="card bg-gradient-to-br from-gray-500 to-slate-600 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm opacity-90 mb-1">Past Events</p>
              <p class="text-3xl font-bold">{{ stats.pastEvents }}</p>
            </div>
            <CalendarIcon class="h-12 w-12 opacity-50" />
          </div>
        </div>
      </div>

      <!-- Companies and Events -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Companies -->
        <div class="card">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Your Companies</h2>
            <RouterLink to="/organizer/companies" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
              View All →
            </RouterLink>
          </div>

          <div v-if="companies.length > 0" class="space-y-3">
            <RouterLink
              v-for="company in companies.slice(0, 5)"
              :key="company.id"
              :to="`/organizer/companies/${company.id}`"
              class="block p-4 border border-gray-200 rounded-lg hover:border-primary-500 hover:shadow-md transition-all"
            >
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="font-semibold text-gray-900">{{ company.name }}</h3>
                  <p class="text-sm text-gray-500">
                    {{ company.events_count || 0 }} events · {{ company.members_count || 0 }} members
                  </p>
                </div>
                <ChevronRightIcon class="h-5 w-5 text-gray-400" />
              </div>
            </RouterLink>
          </div>

          <div v-else class="text-center py-8">
            <p class="text-gray-500 mb-4">No companies yet</p>
            <RouterLink to="/organizer/companies" class="btn btn-primary">
              Create Your First Company
            </RouterLink>
          </div>
        </div>

        <!-- Recent Events -->
        <div class="card">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Recent Events</h2>
            <RouterLink to="/organizer/events" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
              View All →
            </RouterLink>
          </div>

          <div v-if="events.length > 0" class="space-y-3">
            <RouterLink
              v-for="event in events.slice(0, 5)"
              :key="event.id"
              :to="`/organizer/events/${event.id}`"
              class="block p-4 border border-gray-200 rounded-lg hover:border-primary-500 hover:shadow-md transition-all"
            >
              <h3 class="font-semibold text-gray-900 mb-1">{{ event.name }}</h3>
              <p class="text-sm text-gray-600 mb-2">{{ event.company?.name }}</p>
              <div class="flex items-center text-xs text-gray-500">
                <CalendarIcon class="h-4 w-4 mr-1" />
                {{ formatDate(event.start_time) }}
              </div>
            </RouterLink>
          </div>

          <div v-else class="text-center py-8">
            <p class="text-gray-500 mb-4">No events yet</p>
            <RouterLink to="/organizer/events" class="btn btn-primary">
              Create Your First Event
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
  BuildingOfficeIcon,
  CalendarIcon,
  TicketIcon,
  ClockIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'
import { companiesApi, eventsApi } from '@/api'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const loading = ref(true)
const companies = ref([])
const events = ref([])
const stats = ref({
  totalCompanies: 0,
  totalEvents: 0,
  activeEvents: 0,
  upcomingEvents: 0,
  pastEvents: 0
})

async function loadDashboard() {
  try {
    loading.value = true

    // Load companies
    const companiesRes = await companiesApi.getCompanies({ per_page: 100 })
    companies.value = companiesRes.data.data || []
    stats.value.totalCompanies = companiesRes.data.meta?.pagination?.total || companies.value.length

    // Get all company IDs for filtering events
    const companyIds = companies.value.map(c => c.id)

    if (companyIds.length > 0) {
      // Load all events (without status filter to get all events)
      const allEventsRes = await eventsApi.getEvents({ per_page: 1000 })
      const allEvents = allEventsRes.data.data || []
      
      // Filter events that belong to organizer's companies
      const organizerEvents = allEvents.filter(event => 
        companyIds.includes(event.company_id)
      )
      
      // Store recent events for display (sort by start_time descending)
      events.value = organizerEvents.sort((a, b) => 
        new Date(b.start_time) - new Date(a.start_time)
      )
      
      // Calculate stats
      const now = new Date()
      stats.value.totalEvents = organizerEvents.length
      
      stats.value.activeEvents = organizerEvents.filter(event => {
        const startTime = new Date(event.start_time)
        const endTime = new Date(event.end_time)
        return startTime <= now && endTime >= now
      }).length
      
      stats.value.upcomingEvents = organizerEvents.filter(event => {
        const startTime = new Date(event.start_time)
        return startTime > now
      }).length
      
      stats.value.pastEvents = organizerEvents.filter(event => {
        const endTime = new Date(event.end_time)
        return endTime < now
      }).length
    } else {
      events.value = []
      stats.value.totalEvents = 0
      stats.value.activeEvents = 0
      stats.value.upcomingEvents = 0
      stats.value.pastEvents = 0
    }
  } catch (error) {
    console.error('Failed to load dashboard:', error)
  } finally {
    loading.value = false
  }
}

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

onMounted(() => {
  loadDashboard()
})
</script>
