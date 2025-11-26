<template>
  <div class="flex items-center justify-center min-h-[400px]">
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 mb-4">
        <div class="w-16 h-16 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
      </div>
      <p v-if="message" class="text-gray-600">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Loading...'
  }
})
</script>
