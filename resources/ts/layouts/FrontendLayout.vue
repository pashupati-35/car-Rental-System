<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const auth = computed(() => page.props.auth as any)
const user = computed(() => auth.value?.admin || auth.value?.owner || auth.value?.customer)
const showAuthMenu = ref(false)
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <Link href="/" class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-blue-500/20">
            CR
          </div>
          <span class="font-extrabold text-xl tracking-tight text-gray-900 dark:text-white">AutoRent</span>
        </Link>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
          <Link href="/" class="text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">
            Home
          </Link>
          <Link href="/cars" class="text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">
            Browse Fleet
          </Link>
          <Link href="/car-calendar" class="text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">
            Availability Calendar
          </Link>
          <Link href="/ai-chat" class="text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors flex items-center gap-1.5">
            <span class="text-amber-500">✨</span> AI Assistant
          </Link>
        </nav>

        <div class="flex items-center gap-2.5">
          <template v-if="user">
            <Link
              :href="auth?.admin ? '/admin/dashboard' : auth?.owner ? '/owner/dashboard' : '/customer/dashboard'"
              class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all"
            >
              Go to Dashboard &rarr;
            </Link>
          </template>
          <template v-else>
            <!-- Sign In Portal Link -->
            <Link
              href="/customer/login"
              class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
            >
              Sign In
            </Link>

            <!-- Register Button with Customer & Owner Options -->
            <div class="relative">
              <Link
                href="/customer/register"
                class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all inline-block"
              >
                Register
              </Link>
            </div>

            <!-- Owner Portal Shortcut -->
            <Link
              href="/owner/login"
              class="hidden sm:inline-flex px-3 py-2 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold hover:bg-emerald-100 transition-colors"
            >
              Fleet Owner Portal
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
    <footer class="bg-gray-950 text-gray-400 py-12 border-t border-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-gray-800">
          <div class="space-y-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                CR
              </div>
              <span class="font-extrabold text-lg text-white">AutoRent Fleet</span>
            </div>
            <p class="text-xs text-gray-400 leading-relaxed">
              Premium car rental marketplace with zero-overlap calendar booking, dedicated chauffeur rosters, and instant verification.
            </p>
          </div>

          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-200 mb-3">Customer Services</h4>
            <ul class="space-y-2 text-xs">
              <li><Link href="/cars" class="hover:text-white transition-colors">Browse Available Cars</Link></li>
              <li><Link href="/car-calendar" class="hover:text-white transition-colors">Calendar Schedule</Link></li>
              <li><Link href="/customer/register" class="hover:text-white transition-colors">Customer Register</Link></li>
              <li><Link href="/customer/login" class="hover:text-white transition-colors">Customer Sign In</Link></li>
            </ul>
          </div>

          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-200 mb-3">Fleet Owner Portal</h4>
            <ul class="space-y-2 text-xs">
              <li><Link href="/owner/register" class="hover:text-emerald-400 transition-colors">Register as Fleet Owner</Link></li>
              <li><Link href="/owner/login" class="hover:text-emerald-400 transition-colors">Owner Login Portal</Link></li>
              <li><Link href="/owner/dashboard" class="hover:text-emerald-400 transition-colors">Fleet Roster & Earnings</Link></li>
            </ul>
          </div>

          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-200 mb-3">Administration</h4>
            <ul class="space-y-2 text-xs">
              <li><Link href="/admin/login" class="hover:text-indigo-400 transition-colors">Master Admin Login</Link></li>
              <li><Link href="/ai-chat" class="hover:text-amber-400 transition-colors">AI Fleet Assistant</Link></li>
            </ul>
          </div>
        </div>

        <div class="pt-6 text-center text-xs text-gray-500">
          &copy; 2026 AutoRent Car Rental & Fleet Management System. All rights reserved.
        </div>
      </div>
    </footer>
  </div>
</template>
