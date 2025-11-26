<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-4xl font-bold text-gray-900">My Events</h1>
      <button @click="openCreateModal" class="btn btn-primary">
        + Create Event
      </button>
    </div>

    <Alert
      v-if="alert.show"
      :type="alert.type"
      :message="alert.message"
      @close="alert.show = false"
    />

    <LoadingSpinner v-if="loading" message="Loading events..." />

    <!-- Filter Section -->
    <div v-else class="mb-6">
      <div class="card">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <select v-model="filters.company_id" @change="loadEvents" class="input">
            <option value="">All Companies</option>
            <option v-for="company in companies" :key="company.id" :value="company.id">
              {{ company.name }}
            </option>
          </select>
          <select v-model="filters.status" @change="loadEvents" class="input">
            <option value="">All Status</option>
            <option value="upcoming">Upcoming</option>
            <option value="ongoing">Ongoing</option>
            <option value="past">Past</option>
          </select>
          <input
            v-model="filters.search"
            @input="loadEvents"
            type="text"
            placeholder="Search events..."
            class="input"
          />
        </div>
      </div>
    </div>

    <!-- Events Grid -->
    <div v-if="!loading && events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="event in events" :key="event.id" class="card group hover:shadow-xl transition-all">
        <div class="h-48 bg-gradient-to-br from-primary-400 to-secondary-500 rounded-lg mb-4 flex items-center justify-center">
          <CalendarIcon class="h-16 w-16 text-white opacity-50" />
        </div>
        
        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ event.name }}</h3>
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ event.description || 'No description' }}</p>
        
        <div class="space-y-2 text-sm text-gray-600 mb-4">
          <div class="flex items-center">
            <BuildingOfficeIcon class="h-4 w-4 mr-2" />
            <span>{{ event.company?.name }}</span>
          </div>
          <div class="flex items-center">
            <ClockIcon class="h-4 w-4 mr-2" />
            <span>{{ formatDate(event.start_time) }}</span>
          </div>
        </div>

        <div class="flex space-x-2">
          <RouterLink
            :to="`/organizer/events/${event.id}`"
            class="btn btn-primary flex-1 text-center"
          >
            Manage
          </RouterLink>
          <button
            @click="openEditModal(event)"
            class="btn btn-secondary"
          >
            <PencilIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>

    <div v-else-if="!loading && events.length === 0" class="card text-center py-16">
      <CalendarIcon class="h-16 w-16 text-gray-400 mx-auto mb-4" />
      <p class="text-gray-500 text-lg mb-4">You haven't created any events yet</p>
      <button @click="openCreateModal" class="btn btn-primary">
        Create Your First Event
      </button>
    </div>

    <!-- Create/Edit Event Modal -->
    <Modal
      :show="modal.show"
      :title="modal.isEdit ? 'Edit Event' : 'Create Event'"
      :confirm-text="modal.isEdit ? 'Save Changes' : 'Create Event'"
      :loading="modal.loading"
      @close="closeModal"
      @confirm="handleSubmit"
    >
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="label">Company <span class="text-red-500">*</span></label>
          <select v-model="modal.form.company_id" required class="input">
            <option value="">Select a company</option>
            <option v-for="company in companies" :key="company.id" :value="company.id">
              {{ company.name }}
            </option>
          </select>
          <p v-if="companies.length === 0" class="text-sm text-yellow-600 mt-1">
            You need to create a company first.
            <RouterLink to="/organizer/companies" class="underline">Create one here</RouterLink>
          </p>
        </div>

        <FormInput
          v-model="modal.form.name"
          label="Event Name"
          placeholder="Enter event name"
          required
          :error="modal.errors.name"
        />

        <FormTextarea
          v-model="modal.form.description"
          label="Description"
          placeholder="Enter event description"
          :rows="3"
          :error="modal.errors.description"
        />

        <div class="grid grid-cols-2 gap-4">
          <FormInput
            v-model="modal.form.start_time"
            label="Start Date & Time"
            type="datetime-local"
            required
            :error="modal.errors.start_time"
          />
          <FormInput
            v-model="modal.form.end_time"
            label="End Date & Time"
            type="datetime-local"
            required
            :error="modal.errors.end_time"
          />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <FormInput
            v-model="modal.form.sale_start_time"
            label="Sales Start"
            type="datetime-local"
            required
            :error="modal.errors.sale_start_time"
          />
          <FormInput
            v-model="modal.form.sale_end_time"
            label="Sales End"
            type="datetime-local"
            required
            :error="modal.errors.sale_end_time"
          />
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import {
  CalendarIcon,
  BuildingOfficeIcon,
  ClockIcon,
  PencilIcon
} from '@heroicons/vue/24/outline'
import { eventsApi, companiesApi } from '@/api'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Alert from '@/components/Alert.vue'
import Modal from '@/components/Modal.vue'
import FormInput from '@/components/FormInput.vue'
import FormTextarea from '@/components/FormTextarea.vue'

const events = ref([])
const companies = ref([])
const loading = ref(true)

const filters = reactive({
  company_id: '',
  status: '',
  search: ''
})

const alert = reactive({
  show: false,
  type: 'success',
  message: ''
})

const modal = reactive({
  show: false,
  isEdit: false,
  loading: false,
  eventId: null,
  form: {
    company_id: '',
    name: '',
    description: '',
    start_time: '',
    end_time: '',
    sale_start_time: '',
    sale_end_time: ''
  },
  errors: {}
})

async function loadEvents() {
  loading.value = true
  try {
    const params = {}
    if (filters.company_id) params.company_id = filters.company_id
    if (filters.status) params.status = filters.status
    if (filters.search) params.search = filters.search

    const response = await eventsApi.getEvents(params)
    events.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load events:', error)
    showAlert('error', 'Failed to load events')
  } finally {
    loading.value = false
  }
}

async function loadCompanies() {
  try {
    const response = await companiesApi.getCompanies()
    companies.value = response.data.data || []
  } catch (error) {
    console.error('Failed to load companies:', error)
  }
}

function openCreateModal() {
  modal.show = true
  modal.isEdit = false
  modal.form = {
    company_id: '',
    name: '',
    description: '',
    start_time: '',
    end_time: '',
    sale_start_time: '',
    sale_end_time: ''
  }
  modal.errors = {}
}

function openEditModal(event) {
  modal.show = true
  modal.isEdit = true
  modal.eventId = event.id
  modal.form = {
    company_id: event.company_id,
    name: event.name,
    description: event.description || '',
    start_time: formatDateTimeForInput(event.start_time),
    end_time: formatDateTimeForInput(event.end_time),
    sale_start_time: formatDateTimeForInput(event.sale_start_time),
    sale_end_time: formatDateTimeForInput(event.sale_end_time)
  }
  modal.errors = {}
}

function closeModal() {
  modal.show = false
  modal.errors = {}
}

async function handleSubmit() {
  modal.loading = true
  modal.errors = {}

  try {
    if (modal.isEdit) {
      await eventsApi.updateEvent(modal.eventId, modal.form)
      showAlert('success', 'Event updated successfully')
    } else {
      await eventsApi.createEvent(modal.form)
      showAlert('success', 'Event created successfully')
    }

    closeModal()
    await loadEvents()
  } catch (error) {
    if (error.response?.data?.error?.field) {
      modal.errors[error.response.data.error.field] = error.response.data.error.details
    } else {
      showAlert('error', error.response?.data?.message || 'Failed to save event')
    }
  } finally {
    modal.loading = false
  }
}

function showAlert(type, message) {
  alert.type = type
  alert.message = message
  alert.show = true
}

function formatDate(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatDateTimeForInput(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toISOString().slice(0, 16)
}

onMounted(async () => {
  await loadCompanies()
  await loadEvents()
})
</script>
