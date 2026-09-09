<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  bookedCars: Array<any>
}>()
</script>

<template>
  <AppLayout>
    <Head title="Booking Operations" />

    <template #header>
      Bookings & Reservations
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">Active & Past Bookings</h3>
          <p class="text-xs text-gray-500 mt-0.5">Review booking requests, approval states, and rental durations</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
            <tr>
              <th class="py-3.5 px-6">Booking ID</th>
              <th class="py-3.5 px-6">Customer</th>
              <th class="py-3.5 px-6">Car</th>
              <th class="py-3.5 px-6">Rental Dates</th>
              <th class="py-3.5 px-6">Total Amount</th>
              <th class="py-3.5 px-6">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr v-for="booking in bookedCars" :key="booking.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
              <td class="py-4 px-6 font-mono text-xs text-gray-500">
                #BK-{{ booking.id }}
              </td>
              <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                {{ booking.customer?.name || booking.name || 'Customer' }}
              </td>
              <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                {{ booking.car?.brand }} {{ booking.car?.model }}
              </td>
              <td class="py-4 px-6 text-xs text-gray-600 dark:text-gray-400">
                {{ booking.start_date }} &rarr; {{ booking.end_date }}
              </td>
              <td class="py-4 px-6 font-semibold text-blue-600">
                ${{ booking.total_price || booking.amount || 0 }}
              </td>
              <td class="py-4 px-6">
                <span :class="{
                  'bg-emerald-50 text-emerald-700': booking.status === 'confirmed' || booking.status === 'completed',
                  'bg-amber-50 text-amber-700': booking.status === 'pending',
                  'bg-rose-50 text-rose-700': booking.status === 'cancelled',
                }" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">
                  {{ booking.status || 'Active' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
