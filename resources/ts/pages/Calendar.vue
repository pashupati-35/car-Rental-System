<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

defineProps<{
  events?: Array<any>
}>()
</script>

<template>
  <FrontendLayout>
    <Head title="Vehicle Calendar & Availability" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
      <div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Fleet Availability Calendar</h1>
        <p class="text-sm text-gray-500 mt-1">Check scheduled bookings and available rental slots across all vehicles</p>
      </div>

      <div class="p-8 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm text-center">
        <div class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 mx-auto flex items-center justify-center text-3xl mb-4">
          <i class="ri-calendar-event-line"></i>
        </div>
        <h3 class="font-bold text-lg text-gray-900 dark:text-white">Live Booking Schedules</h3>
        <p class="text-xs text-gray-500 max-w-md mx-auto mt-1">
          Real-time reservation timetable is synchronized across all vehicle owners and customer bookings.
        </p>
      </div>
    </div>
  </FrontendLayout>
</template>
