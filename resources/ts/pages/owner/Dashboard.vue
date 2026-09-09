<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  myCarsCount?: number
  activeRentals?: number
  earnings?: number
  recentCars?: Array<any>
}>()
</script>

<template>
  <AppLayout>
    <Head title="Owner Dashboard" />

    <template #header>
      Car Owner Fleet Portal
    </template>

    <div class="space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-2xl">
            <i class="ri-car-line" />
          </div>
          <div>
            <p class="text-xs text-gray-500 font-medium">
              My Listed Vehicles
            </p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              {{ myCarsCount || 0 }}
            </h3>
          </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-2xl">
            <i class="ri-calendar-check-line" />
          </div>
          <div>
            <p class="text-xs text-gray-500 font-medium">
              Active Rentals
            </p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              {{ activeRentals || 0 }}
            </h3>
          </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center text-2xl">
            <i class="ri-money-dollar-circle-line" />
          </div>
          <div>
            <p class="text-xs text-gray-500 font-medium">
              Total Payouts
            </p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              ${{ earnings || 0 }}
            </h3>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-between pt-4">
        <h4 class="font-bold text-lg text-gray-900 dark:text-white">
          Quick Actions
        </h4>
        <Link
          href="/owner/cars/create"
          class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5"
        >
          <i class="ri-add-line" />
          List New Vehicle
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
