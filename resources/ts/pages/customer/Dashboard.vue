<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  activeBookings?: Array<any>
  totalRentedCars?: number
}>()
</script>

<template>
  <AppLayout>
    <Head title="Customer Dashboard" />

    <template #header>
      Customer Portal
    </template>

    <div class="space-y-6">
      <div class="p-8 rounded-3xl bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-xl shadow-blue-500/20 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
          <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-semibold uppercase tracking-wider">Premium Car Rental</span>
          <h2 class="text-3xl font-extrabold mt-2">Ready for your next journey?</h2>
          <p class="text-blue-100 text-sm mt-1 max-w-md">Browse verified vehicles with transparent pricing, instant booking confirmation, and AI assistance.</p>
        </div>
        <Link
          href="/cars"
          class="px-6 py-3 rounded-2xl bg-white text-blue-700 font-bold text-sm shadow-lg hover:bg-blue-50 transition-all shrink-0"
        >
          Explore Available Cars &rarr;
        </Link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <Link
          href="/customer/bookings"
          class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm hover:border-blue-500/30 transition-all flex items-center gap-4"
        >
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
            <i class="ri-car-line"></i>
          </div>
          <div>
            <h4 class="font-bold text-base text-gray-900 dark:text-white">My Bookings</h4>
            <p class="text-xs text-gray-500 mt-0.5">Check current rental status, invoices, and pickup dates</p>
          </div>
        </Link>

        <Link
          href="/ai-chat"
          class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm hover:border-amber-500/30 transition-all flex items-center gap-4"
        >
          <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl">
            <i class="ri-sparkling-fill"></i>
          </div>
          <div>
            <h4 class="font-bold text-base text-gray-900 dark:text-white">AI Fleet Assistant</h4>
            <p class="text-xs text-gray-500 mt-0.5">Get instant answers on vehicle recommendations and policies</p>
          </div>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
