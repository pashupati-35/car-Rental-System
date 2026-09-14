<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'

const props = defineProps<{
  owner?: any
  stats?: {
    totalCars: number
    verifiedCars: number
    pendingCars: number
    rejectedCars: number
    totalDrivers: number
    activeDrivers: number
    totalBookings: number
    pendingBookings: number
    confirmedBookings: number
    completedBookings: number
    cancelledBookings: number
    totalRevenue: number
    thisMonthRevenue: number
  }
  recentCars?: Array<any>
  recentBookings?: Array<any>
  recentDrivers?: Array<any>
  revenueTrends?: {
    labels: string[]
    revenue: number[]
    bookings: number[]
  }
}>()

const confirmBooking = (id: number) => {
  if (confirm('Are you sure you want to confirm this reservation?')) {
    router.post(`/owner/bookings/${id}/confirm`)
  }
}

const cancelBooking = (id: number) => {
  if (confirm('Are you sure you want to cancel this booking?')) {
    router.post(`/owner/bookings/${id}/cancel`)
  }
}

const maxTrendRevenue = Math.max(...(props.revenueTrends?.revenue || [100]), 100)
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
            class="px-5 py-3.5 rounded-2xl bg-white text-emerald-950 font-bold text-xs shadow-lg hover:bg-emerald-50 transition-all flex items-center gap-2 shrink-0 cursor-pointer"
          >
            <i class="ri-add-circle-line text-base text-emerald-700" />
            <span>List New Car</span>
          </Link>
          <Link
            href="/owner/drivers"
            class="px-5 py-3.5 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-2 shrink-0 cursor-pointer"
          >
            <i class="ri-user-star-line text-base" />
            <span>Manage Chauffeurs</span>
          </Link>
          <Link
            href="/owner/bookings"
            class="px-5 py-3.5 rounded-2xl bg-teal-800/80 hover:bg-teal-800 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-2 shrink-0 cursor-pointer"
          >
            <i class="ri-calendar-check-line text-base" />
            <span>Reservations</span>
          </Link>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Fleet Cars Card -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Fleet Vehicles
            </p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">
              {{ stats?.totalCars ?? 0 }}
            </h3>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full">
                {{ stats?.verifiedCars ?? 0 }} Active
              </span>
              <span
                v-if="(stats?.pendingCars ?? 0) > 0"
                class="text-[11px] font-bold text-amber-600 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-full"
              >
                {{ stats?.pendingCars }} Pending
              </span>
            </div>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-car-line" />
          </div>
        </div>

        <!-- Driver Staff Card -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Driver Roster
            </p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">
              {{ stats?.totalDrivers ?? 0 }}
            </h3>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-[11px] font-bold text-teal-600 bg-teal-50 dark:bg-teal-950/60 px-2 py-0.5 rounded-full">
                {{ stats?.activeDrivers ?? 0 }} Available
              </span>
            </div>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-user-star-line" />
          </div>
        </div>

        <!-- Bookings Card -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Total Reservations
            </p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">
              {{ stats?.totalBookings ?? 0 }}
            </h3>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-[11px] font-bold text-amber-600 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-full">
                {{ stats?.pendingBookings ?? 0 }} Pending
              </span>
              <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full">
                {{ stats?.confirmedBookings ?? 0 }} Confirmed
              </span>
            </div>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-calendar-check-line" />
          </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Total Revenue
            </p>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
              ${{ stats?.totalRevenue ?? 0 }}
            </h3>
            <span class="text-[11px] text-slate-400 mt-2 block font-medium">
              This Month: <strong class="text-emerald-600 dark:text-emerald-400">${{ stats?.thisMonthRevenue ?? 0 }}</strong>
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-money-dollar-circle-line" />
          </div>
        </div>
      </div>

      <!-- 6-Month Revenue Trend Visualizer -->
      <div
        v-if="revenueTrends?.labels && revenueTrends.labels.length > 0"
        class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-base text-slate-900 dark:text-white">
              6-Month Earnings & Reservation Flow
            </h3>
            <p class="text-xs text-slate-400">
              Monthly revenue breakdown from confirmed and completed reservations
            </p>
          </div>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
            <i class="ri-line-chart-line text-sm" />
            <span>Performance Insights</span>
          </span>
        </div>

        <!-- Bar Charts Representation -->
        <div class="grid grid-cols-6 gap-3 sm:gap-6 pt-4 items-end min-h-[160px]">
          <div
            v-for="(label, i) in revenueTrends.labels"
            :key="label"
            class="flex flex-col items-center gap-2 group"
          >
            <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">
              ${{ revenueTrends.revenue[i] }}
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-2xl h-28 flex items-end p-1">
              <div
                class="w-full rounded-xl bg-gradient-to-t from-emerald-600 to-teal-500 transition-all duration-500 group-hover:brightness-110"
                :style="{ height: `${Math.max((revenueTrends.revenue[i] / maxTrendRevenue) * 100, 8)}%` }"
              />
            </div>
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 truncate max-w-full text-center">
              {{ label }}
            </span>
            <span class="text-[10px] text-slate-400 font-mono">
              {{ revenueTrends.bookings[i] }} trips
            </span>
          </div>
        </div>
      </div>

      <!-- Recent Bookings Section -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-base text-slate-900 dark:text-white">
              Recent Fleet Reservations
            </h3>
            <p class="text-xs text-slate-400">
              Latest bookings for your vehicles with quick confirmation actions
            </p>
          </div>
          <Link
            href="/owner/bookings"
            class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1"
          >
            <span>All Reservations</span>
            <i class="ri-arrow-right-line" />
          </Link>
        </div>

        <div
          v-if="recentBookings && recentBookings.length > 0"
          class="overflow-x-auto"
        >
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase tracking-wider font-extrabold text-[10px]">
                <th class="py-3 px-3">
                  Vehicle
                </th>
                <th class="py-3 px-3">
                  Customer
                </th>
                <th class="py-3 px-3">
                  Trip Dates
                </th>
                <th class="py-3 px-3">
                  Amount
                </th>
                <th class="py-3 px-3">
                  Status
                </th>
                <th class="py-3 px-3 text-right">
                  Action
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr
                v-for="b in recentBookings"
                :key="b.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
              >
                <td class="py-3 px-3">
                  <span class="font-bold text-slate-900 dark:text-white block">{{ b.car?.car_name }} {{ b.car?.car_model }}</span>
                  <span class="font-mono text-[10px] text-slate-400">{{ b.car?.car_number }}</span>
                </td>
                <td class="py-3 px-3">
                  <span class="font-semibold text-slate-900 dark:text-white block">{{ b.customer?.full_name || 'Customer' }}</span>
                  <span class="text-[10px] text-slate-400">{{ b.customer?.email }}</span>
                </td>
                <td class="py-3 px-3 text-slate-700 dark:text-slate-300">
                  {{ b.pickup_date || b.start_date || 'N/A' }} &rarr; {{ b.drop_date || b.end_date || 'N/A' }}
                </td>
                <td class="py-3 px-3 font-bold text-emerald-600 dark:text-emerald-400">
                  ${{ b.total_price || b.amount || 0 }}
                </td>
                <td class="py-3 px-3">
                  <span
                    :class="{
                      'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300': b.status === 'confirmed',
                      'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300': b.status === 'completed',
                      'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300': b.status === 'pending',
                      'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300': b.status === 'cancelled',
                    }"
                    class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                  >
                    {{ b.status }}
                  </span>
                </td>
                <td class="py-3 px-3 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button
                      v-if="b.status === 'pending'"
                      type="button"
                      class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] transition-all cursor-pointer"
                      @click="confirmBooking(b.id)"
                    >
                      Confirm
                    </button>
                    <button
                      v-if="b.status === 'pending' || b.status === 'confirmed'"
                      type="button"
                      class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 text-[10px] font-bold transition-all cursor-pointer"
                      @click="cancelBooking(b.id)"
                    >
                      Cancel
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div
          v-else
          class="p-8 text-center text-slate-400 text-xs"
        >
          No recent reservations on your fleet vehicles.
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
                :href="`/owner/cars/${car.id}`"
                class="flex-1 py-2.5 text-center rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors"
              >
                View
              </Link>
              <Link
                :href="`/owner/cars/${car.id}/edit`"
                class="flex-1 py-2.5 text-center rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-xs font-bold text-emerald-700 dark:text-emerald-300 transition-colors"
              >
                Edit
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
