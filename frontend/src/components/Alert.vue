<template>
  <div
    v-if="show"
    :class="[
      'rounded-lg p-4 mb-4 animate-slide-down',
      typeClasses[type]
    ]"
  >
    <div class="flex">
      <div class="flex-shrink-0">
        <component :is="iconComponent" class="h-5 w-5" />
      </div>
      <div class="ml-3">
        <h3 v-if="title" class="text-sm font-medium">{{ title }}</h3>
        <p class="text-sm" :class="{ 'mt-1': title }">{{ message }}</p>
      </div>
      <div v-if="dismissible" class="ml-auto pl-3">
        <button @click="$emit('close')" class="inline-flex rounded-md p-1.5 hover:bg-black/10 transition-colors">
          <XMarkIcon class="h-5 w-5" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  InformationCircleIcon,
  XCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    default: true
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    required: true
  },
  dismissible: {
    type: Boolean,
    default: true
  }
})

defineEmits(['close'])

const typeClasses = {
  success: 'bg-green-50 text-green-800 border border-green-200',
  error: 'bg-red-50 text-red-800 border border-red-200',
  warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
  info: 'bg-blue-50 text-blue-800 border border-blue-200'
}

const iconComponent = computed(() => {
  const icons = {
    success: CheckCircleIcon,
    error: XCircleIcon,
    warning: ExclamationCircleIcon,
    info: InformationCircleIcon
  }
  return icons[props.type]
})
</script>
