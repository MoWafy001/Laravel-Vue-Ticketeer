<template>
  <div id="app">
    <Navbar v-if="!isAuthPage" />
    <main class="min-h-screen">
      <RouterView />
    </main>
    <Footer v-if="!isAuthPage" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'

const route = useRoute()
const isAuthPage = computed(() => {
  return route.name === 'login' || route.name === 'register'
})
</script>
