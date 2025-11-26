<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    @click.self="closeModal"
  >
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <!-- Backdrop -->
      <div
        class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
        @click="closeModal"
      ></div>

      <!-- Modal panel -->
      <div
        class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle animate-scale-in"
      >
        <!-- Close button -->
        <button
          @click="closeModal"
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 transition-colors"
        >
          <XMarkIcon class="h-6 w-6" />
        </button>

        <!-- Header -->
        <div v-if="title" class="mb-4">
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            {{ title }}
          </h3>
        </div>

        <!-- Content -->
        <div class="mt-2">
          <slot></slot>
        </div>

        <!-- Footer -->
        <div v-if="showFooter" class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse gap-3">
          <button
            v-if="confirmText"
            @click="$emit('confirm')"
            type="button"
            class="btn btn-primary w-full sm:w-auto"
            :disabled="loading"
          >
            {{ loading ? 'Processing...' : confirmText }}
          </button>
          <button
            v-if="cancelText"
            @click="closeModal"
            type="button"
            class="btn btn-secondary w-full sm:w-auto mt-3 sm:mt-0"
            :disabled="loading"
          >
            {{ cancelText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  showFooter: {
    type: Boolean,
    default: true
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'confirm'])

function closeModal() {
  if (!props.loading) {
    emit('close')
  }
}
</script>
