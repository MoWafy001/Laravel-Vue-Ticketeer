<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8">
      <div>
        <RouterLink to="/organizer/companies" class="text-primary-600 hover:text-primary-700 text-sm mb-2 inline-block">
          ← Back to Companies
        </RouterLink>
        <h1 class="text-4xl font-bold text-gray-900">{{ company?.name }}</h1>
      </div>
    </div>

    <LoadingSpinner v-if="loading" message="Loading company details..." />

    <div v-else-if="company">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
          <p class="text-sm opacity-90 mb-1">Total Events</p>
          <p class="text-3xl font-bold">{{ analytics.total_events || 0 }}</p>
        </div>
        <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
          <p class="text-sm opacity-90 mb-1">Tickets Sold</p>
          <p class="text-3xl font-bold">{{ analytics.total_tickets_sold || 0 }}</p>
        </div>
        <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
          <p class="text-sm opacity-90 mb-1">Total Revenue</p>
          <p class="text-3xl font-bold">${{ (analytics.total_revenue || 0).toFixed(2) }}</p>
        </div>
        <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white">
          <p class="text-sm opacity-90 mb-1">Active Events</p>
          <p class="text-3xl font-bold">{{ analytics.active_events || 0 }}</p>
        </div>
      </div>

      <!-- Tabs -->
      <div class="mb-6">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8">
            <button
              @click="activeTab = 'overview'"
              :class="[
                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                activeTab === 'overview'
                  ? 'border-primary-500 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              Overview
            </button>
            <button
              @click="activeTab = 'members'"
              :class="[
                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                activeTab === 'members'
                  ? 'border-primary-500 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              Members ({{ members.length }})
            </button>
            <button
              @click="activeTab = 'analytics'"
              :class="[
                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                activeTab === 'analytics'
                  ? 'border-primary-500 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              Analytics
            </button>
          </nav>
        </div>
      </div>

      <!-- Tab Content -->
      <div v-if="activeTab === 'overview'" class="space-y-6">
        <div class="card">
          <h2 class="text-2xl font-bold mb-4">Company Information</h2>
          <div class="space-y-3">
            <div>
              <p class="text-sm text-gray-500">Company Name</p>
              <p class="font-semibold">{{ company.name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Created</p>
              <p class="font-semibold">{{ formatDate(company.created_at) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Total Members</p>
              <p class="font-semibold">{{ members.length }}</p>
            </div>
          </div>
        </div>

        <div class="card">
          <h2 class="text-2xl font-bold mb-4">Quick Stats</h2>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Upcoming Events</p>
              <p class="text-2xl font-bold text-blue-600">{{ analytics.upcoming_events || 0 }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Past Events</p>
              <p class="text-2xl font-bold text-gray-600">{{ analytics.past_events || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'members'" class="card">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold">Team Members</h2>
          <button class="btn btn-primary" disabled>+ Add Member</button>
        </div>

        <div v-if="members.length > 0" class="space-y-3">
          <div
            v-for="member in members"
            :key="member.id"
            class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-500 to-secondary-600 flex items-center justify-center text-white font-bold">
                  {{ member.organizer?.name?.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <p class="font-semibold">{{ member.organizer?.name }}</p>
                  <p class="text-sm text-gray-500">{{ member.organizer?.email }}</p>
                </div>
              </div>
              <div class="text-sm text-gray-500">
                Added {{ formatDate(member.created_at) }}
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-12">
          <p class="text-gray-500">No members yet</p>
        </div>
      </div>

      <div v-if="activeTab === 'analytics'" class="space-y-6">
        <div class="card">
          <h2 class="text-2xl font-bold mb-4">Revenue by Event</h2>
          <div v-if="analytics.revenue_by_event && analytics.revenue_by_event.length > 0" class="space-y-3">
            <div
              v-for="event in analytics.revenue_by_event"
              :key="event.event_id"
              class="flex items-center justify-between p-3 border-b border-gray-100 last:border-0"
            >
              <div>
                <p class="font-semibold">{{ event.event_name }}</p>
                <p class="text-sm text-gray-500">{{ event.tickets_sold }} tickets sold</p>
              </div>
              <div class="text-right">
                <p class="text-lg font-bold text-green-600">${{ event.revenue.toFixed(2) }}</p>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            No revenue data available
          </div>
        </div>

        <div class="card">
          <h2 class="text-2xl font-bold mb-4">Sales by Date</h2>
          <div v-if="analytics.sales_by_date && analytics.sales_by_date.length > 0" class="space-y-2">
            <div
              v-for="sale in analytics.sales_by_date.slice(0, 10)"
              :key="sale.date"
              class="flex items-center justify-between p-2 border-b border-gray-100 last:border-0"
            >
              <p class="text-sm">{{ formatDate(sale.date) }}</p>
              <div class="text-right">
                <p class="text-sm font-semibold">{{ sale.tickets_sold }} tickets</p>
                <p class="text-xs text-gray-500">${{ sale.revenue.toFixed(2) }}</p>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            No sales data available
          </div>
        </div>
      </div>
    </div>

    <div v-else class="card text-center py-12">
      <p class="text-gray-500 text-lg">Company not found</p>
      <RouterLink to="/organizer/companies" class="btn btn-primary mt-4">
        Back to Companies
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { companiesApi } from '@/api'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const route = useRoute()
const loading = ref(true)
const company = ref(null)
const members = ref([])
const analytics = ref({})
const activeTab = ref('overview')

async function loadCompany() {
  loading.value = true
  try {
    // Load company details
    const companyRes = await companiesApi.getCompany(route.params.id)
    company.value = companyRes.data.data

    // Load members
    try {
      const membersRes = await companiesApi.getMembers(route.params.id)
      members.value = membersRes.data.data || []
    } catch (error) {
      console.log('Members not loaded:', error)
      members.value = []
    }

    // Load analytics
    try {
      const analyticsRes = await companiesApi.getCompanyAnalytics(route.params.id)
      analytics.value = analyticsRes.data.data || {}
    } catch (error) {
      console.log('Analytics not loaded:', error)
      analytics.value = {}
    }
  } catch (error) {
    console.error('Failed to load company:', error)
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
  loadCompany()
})
</script>
