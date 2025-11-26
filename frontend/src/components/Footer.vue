<template>
  <footer class="bg-gray-900 text-white mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-xl font-bold mb-4">Ticketeer</h3>
          <p class="text-gray-400">Your premier ticket booking platform for amazing events.</p>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Quick Links</h4>
          <ul class="space-y-2 text-gray-400">
            <li><RouterLink to="/events" class="hover:text-white transition-colors">Browse Events</RouterLink></li>
            <li><RouterLink to="/login" class="hover:text-white transition-colors">Login</RouterLink></li>
            <li><RouterLink to="/register" class="hover:text-white transition-colors">Sign Up</RouterLink></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4">Contact</h4>
          <p class="text-gray-400">support@ticketeer.com</p>
        </div>
      </div>
      <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; 2025 Ticketeer. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>
