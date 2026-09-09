<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  bookings: Array<any>
}>()
</script>

<template>
  <AppLayout>
    <Head title="My Bookings" />

    <template #header>
      My Booking History
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">
            Active & Past Rentals
          </h3>
          <p class="text-xs text-gray-500 mt-0.5">
            Track your reservations, invoices, and vehicle returns
          </p>
        </div>
        <Link
          href="/cars"
          class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition-all"
        >
          Book Another Car
        </Link>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
            <tr>
              <th class="py-3.5 px-6">
                Booking Reference
              </th>
              <th class="py-3.5 px-6">
                Vehicle
              </th>
              <th class="py-3.5 px-6">
                Rental Dates
              </th>
              <th class="py-3.5 px-6">
                Amount
              </th>
              <th class="py-3.5 px-6">
                Status
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr
              v-for="booking in bookings"
              :key="booking.id"
              class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40"
            >
              <td class="py-4 px-6 font-mono text-xs text-gray-500">
                #BK-{{ booking.id }}
              </td>
              <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                {{ booking.car?.brand }} {{ booking.car?.model }}
              </td>
              <td class="py-4 px-6 text-xs text-gray-600 dark:text-gray-400">
                {{ booking.start_date }} &rarr; {{ booking.end_date }}
              </td>
              <td class="py-4 px-6 font-semibold text-blue-600">
                ${{ booking.total_price || booking.amount || 0 }}
              </td>
              <td class="py-4 px-6">
                <span
                  :class="{
                    'bg-emerald-50 text-emerald-700': booking.status === 'confirmed' || booking.status === 'completed',
                    'bg-amber-50 text-amber-700': booking.status === 'pending',
                    'bg-rose-50 text-rose-700': booking.status === 'cancelled',
                  }"
                  class="px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                >
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
