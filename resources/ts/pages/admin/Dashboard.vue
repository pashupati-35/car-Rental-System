<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  totalCars?: number
  totalCustomers?: number
  totalBookings?: number
  totalRevenue?: number
  recentBookings?: Array<any>
}>()
</script>

<template>
  <AppLayout>
    <Head title="Admin Dashboard" />

    <template #header>
      Admin Control Center
    </template>

    <div class="space-y-6">
      <!-- Stats Overview Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-2xl">
            <i class="ri-car-line" />
          </div>
          <div>
            <p class="text-xs text-gray-500 font-medium">
              Total Fleet
            </p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              {{ totalCars || 0 }}
            </h3>
          </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-2xl">
            <i class="ri-calendar-check-line" />
          </div>
          <div>
            <p class="text-xs text-gray-500 font-medium">
              Total Bookings
            </p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              {{ totalBookings || 0 }}
            </h3>
          </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center text-2xl">
            <i class="ri-user-smile-line" />
          </div>
          <div>
            <p class="text-xs text-gray-500 font-medium">
              Registered Customers
            </p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              {{ totalCustomers || 0 }}
            </h3>
          </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 flex items-center justify-center text-2xl">
            <i class="ri-money-dollar-circle-line" />
          </div>
          <div>
            <p class="text-xs text-gray-500 font-medium">
              Estimated Revenue
            </p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">
              ${{ totalRevenue || 0 }}
            </h3>
          </div>
        </div>
      </div>

      <!-- Quick Action Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <Link
          href="/admin/cars"
          class="p-6 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-500/20 hover:scale-[1.01] transition-all"
        >
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3">
            <i class="ri-car-fill" />
          </div>
          <h4 class="font-bold text-lg">
            Manage Fleet
          </h4>
          <p class="text-xs text-blue-100 mt-1">
            Review car registrations, verify documents, and manage car status.
          </p>
        </Link>

        <Link
          href="/admin/booked-cars"
          class="p-6 rounded-2xl bg-gradient-to-br from-purple-600 to-pink-700 text-white shadow-lg shadow-purple-500/20 hover:scale-[1.01] transition-all"
        >
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3">
            <i class="ri-calendar-2-fill" />
          </div>
          <h4 class="font-bold text-lg">
            Review Bookings
          </h4>
          <p class="text-xs text-purple-100 mt-1">
            Approve, verify payments, and inspect booking lifecycles.
          </p>
        </Link>

        <Link
          href="/admin/email-templates"
          class="p-6 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-lg shadow-emerald-500/20 hover:scale-[1.01] transition-all"
        >
          <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl mb-3">
            <i class="ri-mail-settings-fill" />
          </div>
          <h4 class="font-bold text-lg">
            Email Templates
          </h4>
          <p class="text-xs text-emerald-100 mt-1">
            Manage system notifications, MFA verification emails & alerts.
          </p>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
