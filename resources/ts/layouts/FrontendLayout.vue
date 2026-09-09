<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const auth = computed(() => page.props.auth as any)
const user = computed(() => auth.value?.admin || auth.value?.owner || auth.value?.customer)
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-gray-100 dark:border-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <Link
          href="/"
          class="flex items-center gap-2.5"
        >
          <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-blue-500/20">
            CR
          </div>
          <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">AutoRent</span>
        </Link>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
          <Link
            href="/"
            class="text-gray-600 hover:text-blue-600 dark:text-gray-300 transition-colors"
          >
            Home
          </Link>
          <Link
            href="/cars"
            class="text-gray-600 hover:text-blue-600 dark:text-gray-300 transition-colors"
          >
            Browse Cars
          </Link>
          <Link
            href="/car-calendar"
            class="text-gray-600 hover:text-blue-600 dark:text-gray-300 transition-colors"
          >
            Availability
          </Link>
          <Link
            href="/ai-chat"
            class="text-gray-600 hover:text-blue-600 dark:text-gray-300 transition-colors flex items-center gap-1.5"
          >
            <i class="ri-sparkling-fill text-amber-500" /> AI Assistant
          </Link>
        </nav>

        <div class="flex items-center gap-3">
          <template v-if="user">
            <Link
              :href="auth?.admin ? '/admin/dashboard' : auth?.owner ? '/owner/dashboard' : '/customer/dashboard'"
              class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-md shadow-blue-500/20 transition-all"
            >
              Dashboard
            </Link>
          </template>
          <template v-else>
            <Link
              href="/customer/login"
              class="px-4 py-2 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 text-sm font-medium transition-colors"
            >
              Sign In
            </Link>
            <Link
              href="/customer/register"
              class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-md shadow-blue-500/20 transition-all"
            >
              Register
            </Link>
          </template>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
        <p>&copy; 2026 Car Rental & Fleet Management System. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>
