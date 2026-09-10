<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'

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
  <OwnerLayout>
    <Head title="Fleet Owner Command Hub" />

    <div class="space-y-8 max-w-7xl mx-auto">
      <!-- Welcome Banner -->
      <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-slate-900 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 relative z-10">
          <span class="px-3 py-1 rounded-full bg-white/20 text-emerald-100 text-xs font-semibold uppercase tracking-wider">
            Fleet Operations Command Hub
          </span>
          <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
            Welcome back, {{ owner?.full_name || 'Fleet Partner' }}
          </h2>
          <p class="text-xs sm:text-sm text-emerald-100/90 max-w-xl leading-relaxed">
            Monitor your vehicles, driver rosters, pending reservation approvals, and live revenue payouts in real time.
          </p>
        </div>

        <div class="flex flex-wrap gap-3 relative z-10">
          <Link
            href="/owner/cars/create"
            class="px-5 py-3.5 rounded-2xl bg-white text-emerald-950 font-bold text-xs shadow-lg hover:bg-emerald-50 transition-all flex items-center gap-2 shrink-0"
          >
            <i class="ri-add-circle-line text-base text-emerald-700" />
            <span>List New Car</span>
          </Link>
          <Link
            href="/owner/drivers"
            class="px-5 py-3.5 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-2 shrink-0"
          >
            <i class="ri-user-star-line text-base" />
            <span>Manage Chauffeurs</span>
          </Link>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              My Listed Cars
            </p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">
              {{ carsCount }}
            </h3>
            <Link
              href="/owner/cars"
              class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold hover:underline mt-2 inline-block"
            >
              View Fleet &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-car-line" />
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Driver Roster
            </p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">
              {{ driversCount }}
            </h3>
            <Link
              href="/owner/drivers"
              class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold hover:underline mt-2 inline-block"
            >
              Manage Staff &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-user-star-line" />
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Active Rentals
            </p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">
              {{ rentalsCount }}
            </h3>
            <span class="text-xs text-slate-400 mt-2 block">{{ pendingCount }} Pending approval</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-calendar-check-line" />
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Total Revenue
            </p>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
              ${{ totalEarnings }}
            </h3>
            <span class="text-xs text-slate-400 mt-2 block">Confirmed payouts</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-money-dollar-circle-line" />
          </div>
        </div>
      </div>

      <!-- Fleet Overview Section -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-black text-xl text-slate-900 dark:text-white">
              Fleet Vehicles & Chauffeur Assignments
            </h3>
            <p class="text-xs text-slate-500">
              Your registered vehicles with current driver allocations and daily pricing
            </p>
          </div>
          <Link
            href="/owner/cars"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all"
          >
            All Fleet Vehicles &rarr;
          </Link>
        </div>

        <div
          v-if="recentCars && recentCars.length > 0"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <div
            v-for="car in recentCars"
            :key="car.id"
            class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4 hover:shadow-md transition-shadow"
          >
            <div>
              <div class="flex items-start justify-between">
                <div>
                  <h4 class="font-bold text-base text-slate-900 dark:text-white">
                    {{ car.car_name }} {{ car.car_model }}
                  </h4>
                  <span class="text-xs font-mono text-slate-400">{{ car.car_number }}</span>
                </div>
                <span class="text-lg font-black text-emerald-600">${{ car.car_price_per_day }}/day</span>
              </div>

              <div class="mt-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs space-y-1.5">
                <div class="flex justify-between items-center">
                  <span class="text-slate-400">Assigned Driver:</span>
                  <span class="font-bold text-slate-800 dark:text-slate-200">
                    {{ car.driver?.name || car.driver_name || 'None Assigned' }}
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-slate-400">Capacity:</span>
                  <span class="font-semibold text-slate-700 dark:text-slate-300">{{ car.number_of_seats }} Passenger Seats</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-slate-400">Approval Status:</span>
                  <span
                    :class="car.status === 'approved' || car.status === 'active' ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-400' : 'text-amber-600 bg-amber-50 dark:bg-amber-950/60 dark:text-amber-400'"
                    class="px-2 py-0.5 rounded-full font-bold capitalize text-[11px]"
                  >
                    {{ car.status || 'Active' }}
                  </span>
                </div>
              </div>
            </div>

            <div class="flex gap-2">
              <Link
                :href="`/owner/cars/${car.id}/edit`"
                class="flex-1 py-2.5 text-center rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors"
              >
                Edit Vehicle Details
              </Link>
            </div>
          </div>
        </div>

        <div
          v-else
          class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs"
        >
          <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-3">
            <i class="ri-car-line" />
          </div>
          <h4 class="font-black text-lg text-slate-900 dark:text-white">
            No fleet vehicles listed yet
          </h4>
          <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
            Register your rental fleet vehicles to begin receiving bookings and driver assignments.
          </p>
          <Link
            href="/owner/cars/create"
            class="mt-4 inline-block px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-500/20 hover:bg-emerald-700 transition-colors"
          >
            + Add Your First Vehicle
          </Link>
        </div>
      </div>
    </div>
  </OwnerLayout>
</template>
