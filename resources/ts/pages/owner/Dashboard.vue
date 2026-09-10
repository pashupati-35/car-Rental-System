<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{
  owner?: any
  stats?: {
    myCarsCount: number
    myDriversCount: number
    activeRentals: number
    pendingBookings: number
    earnings: number
  }
  myCarsCount?: number
  activeRentals?: number
  earnings?: number
  recentCars?: Array<any>
  recentDrivers?: Array<any>
}>()

const carsCount = props.stats?.myCarsCount ?? props.myCarsCount ?? 0
const driversCount = props.stats?.myDriversCount ?? 0
const rentalsCount = props.stats?.activeRentals ?? props.activeRentals ?? 0
const pendingCount = props.stats?.pendingBookings ?? 0
const totalEarnings = props.stats?.earnings ?? props.earnings ?? 0
</script>

<template>
  <AppLayout>
    <Head title="Car Owner Fleet Dashboard" />

    <template #header>
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" />
        Owner Fleet Management Dashboard
      </div>
    </template>

    <div class="space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Welcome Banner -->
      <div class="p-8 rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-slate-900 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 relative z-10">
          <span class="px-3 py-1 rounded-full bg-white/20 text-emerald-100 text-xs font-semibold uppercase tracking-wider">
            Fleet Operations Hub
          </span>
          <h2 class="text-3xl font-black tracking-tight">
            Welcome back, {{ owner?.full_name || 'Fleet Owner' }}
          </h2>
          <p class="text-xs text-emerald-100/80 max-w-xl leading-relaxed">
            Monitor your vehicles, driver rosters, pending bookings, and total rental payouts in real-time.
          </p>
        </div>

        <div class="flex flex-wrap gap-3 relative z-10">
          <Link
            href="/owner/cars/create"
            class="px-5 py-3 rounded-2xl bg-white text-emerald-900 font-bold text-xs shadow-lg hover:bg-emerald-50 transition-all flex items-center gap-2"
          >
            <span>+ List New Car</span>
          </Link>
          <Link
            href="/owner/drivers"
            class="px-5 py-3 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-2"
          >
            <span>👨‍✈️ Manage Drivers</span>
          </Link>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
              My Listed Cars
            </p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">
              {{ carsCount }}
            </h3>
            <Link
              href="/owner/cars"
              class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold hover:underline mt-2 inline-block"
            >
              View Fleet &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
            🚗
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
              Driver Roster
            </p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">
              {{ driversCount }}
            </h3>
            <Link
              href="/owner/drivers"
              class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold hover:underline mt-2 inline-block"
            >
              Manage Staff &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
            👨‍✈️
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
              Active Rentals
            </p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">
              {{ rentalsCount }}
            </h3>
            <span class="text-xs text-gray-500 mt-2 block">{{ pendingCount }} Pending approval</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
            📅
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
              Total Earnings
            </p>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
              ${{ totalEarnings }}
            </h3>
            <span class="text-xs text-gray-400 mt-2 block">Confirmed rentals</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
            💰
          </div>
        </div>
      </div>

      <!-- Fleet Overview Section -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-xl text-gray-900 dark:text-white">
              Fleet Cars & Driver Assignments
            </h3>
            <p class="text-xs text-gray-500">
              Your registered cars with assigned drivers and pricing
            </p>
          </div>
          <Link
            href="/owner/cars"
            class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold transition-all"
          >
            All Cars &rarr;
          </Link>
        </div>

        <div
          v-if="recentCars && recentCars.length > 0"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <div
            v-for="car in recentCars"
            :key="car.id"
            class="p-5 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col justify-between space-y-4"
          >
            <div>
              <div class="flex items-start justify-between">
                <div>
                  <h4 class="font-bold text-base text-gray-900 dark:text-white">
                    {{ car.car_name }} {{ car.car_model }}
                  </h4>
                  <span class="text-xs font-mono text-gray-500">{{ car.car_number }}</span>
                </div>
                <span class="text-lg font-black text-emerald-600">${{ car.car_price_per_day }}/d</span>
              </div>

              <div class="mt-3 p-3 rounded-2xl bg-gray-50 dark:bg-gray-800/60 text-xs space-y-1">
                <div class="flex justify-between">
                  <span class="text-gray-400">Assigned Driver:</span>
                  <span class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ car.driver?.name || car.driver_name || 'None Assigned' }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-400">Seats:</span>
                  <span>{{ car.number_of_seats }} Seats</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-400">Status:</span>
                  <span class="font-bold text-emerald-600 capitalize">{{ car.status }}</span>
                </div>
              </div>
            </div>

            <div class="flex gap-2">
              <Link
                :href="`/owner/cars/${car.id}/edit`"
                class="flex-1 py-2 text-center rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-200"
              >
                Edit Vehicle
              </Link>
            </div>
          </div>
        </div>

        <div
          v-else
          class="p-10 text-center bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800"
        >
          <p class="text-xs text-gray-500">
            No cars registered yet.
          </p>
          <Link
            href="/owner/cars/create"
            class="mt-2 inline-block text-xs font-bold text-emerald-600 hover:underline"
          >
            + Add your first vehicle
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
