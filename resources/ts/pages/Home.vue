<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

defineProps<{
  featuredCars?: Array<any>
}>()
</script>

<template>
  <FrontendLayout>
    <Head title="Premium Car Rental Service" />

    <!-- Hero Section -->
    <section class="relative overflow-hidden py-20 lg:py-28 bg-gradient-to-b from-blue-50/50 via-white to-white dark:from-gray-900 dark:via-gray-950 dark:to-gray-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 text-xs font-semibold uppercase tracking-wider mb-6">
          <i class="ri-shield-star-fill text-amber-500" /> Seamless Vehicle Rentals
        </span>
        <h1 class="text-4xl sm:text-6xl font-black text-gray-900 dark:text-white tracking-tight max-w-4xl mx-auto leading-tight">
          Drive the Best Cars for Every <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Journey & Occasion</span>
        </h1>
        <p class="mt-6 text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
          Choose from hundreds of well-maintained sedans, SUVs, luxury cars, and EVs with 24/7 support and instant online booking.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
          <Link
            href="/cars"
            class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xl shadow-blue-500/25 transition-all"
          >
            Explore All Vehicles
          </Link>
          <Link
            href="/ai-chat"
            class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-bold text-sm transition-all flex items-center justify-center gap-2"
          >
            <i class="ri-sparkling-fill text-amber-500" /> Ask AI Assistant
          </Link>
        </div>
      </div>
    </section>
  </FrontendLayout>
</template>
