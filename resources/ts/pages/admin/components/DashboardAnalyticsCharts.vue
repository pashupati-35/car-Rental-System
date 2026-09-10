<script setup lang="ts">
import { computed, ref } from 'vue'

const props = defineProps<{
  stats: {
    totalCars: number
    pendingCarsCount: number
    verifiedCarsCount: number
    rejectedCarsCount: number
    totalOwners: number
    totalCustomers: number
    totalDrivers: number
    totalBookings: number
    confirmedBookings: number
    pendingBookings: number
    totalRevenue: number
  }
}>()

const activeView = ref<'all' | 'entities' | 'bookings'>('all')

// Entity comparisons for Bar Chart
const entityBars = computed(() => {
  const cars = props.stats.totalCars || 0
  const owners = props.stats.totalOwners || 0
  const customers = props.stats.totalCustomers || 0
  const drivers = props.stats.totalDrivers || 0
  const bookings = props.stats.totalBookings || 0

  const maxVal = Math.max(cars, owners, customers, drivers, bookings, 1)

  return [
    {
      label: 'Booked Cars',
      sublabel: 'Reservations',
      count: bookings,
      percentage: Math.round((bookings / maxVal) * 100),
      gradient: 'from-blue-600 to-indigo-600',
      bgLight: 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
      icon: 'ri-calendar-check-line',
    },
    {
      label: 'Fleet Owners',
      sublabel: 'Vehicle Partners',
      count: owners,
      percentage: Math.round((owners / maxVal) * 100),
      gradient: 'from-emerald-500 to-teal-600',
      bgLight: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
      icon: 'ri-user-star-line',
    },
    {
      label: 'Customers',
      sublabel: 'Registered Clients',
      count: customers,
      percentage: Math.round((customers / maxVal) * 100),
      gradient: 'from-purple-500 to-fuchsia-600',
      bgLight: 'bg-purple-500/10 text-purple-600 dark:text-purple-400',
      icon: 'ri-team-line',
    },
    {
      label: 'System Drivers',
      sublabel: 'Active Chauffeurs',
      count: drivers,
      percentage: Math.round((drivers / maxVal) * 100),
      gradient: 'from-cyan-500 to-blue-600',
      bgLight: 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400',
      icon: 'ri-steering-2-line',
    },
    {
      label: 'Fleet Vehicles',
      sublabel: 'Total Inventory',
      count: cars,
      percentage: Math.round((cars / maxVal) * 100),
      gradient: 'from-indigo-500 to-violet-600',
      bgLight: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
      icon: 'ri-car-line',
    },
  ]
})

// Booking distribution pie/donut chart computation
const bookingSlices = computed(() => {
  const total = props.stats.totalBookings || 0
  const confirmed = props.stats.confirmedBookings || 0
  const pending = props.stats.pendingBookings || 0
  const other = Math.max(0, total - confirmed - pending)

  const calcTotal = total > 0 ? total : 1
  const circumference = 2 * Math.PI * 40 // radius = 40 => circumference ≈ 251.32

  const confirmedPct = (confirmed / calcTotal)
  const pendingPct = (pending / calcTotal)
  const otherPct = (other / calcTotal)

  const confirmedStroke = confirmedPct * circumference
  const pendingStroke = pendingPct * circumference
  const otherStroke = otherPct * circumference

  // Offsets
  const confirmedOffset = 0
  const pendingOffset = -confirmedStroke
  const otherOffset = -(confirmedStroke + pendingStroke)

  return {
    total,
    circumference,
    confirmed: { count: confirmed, pct: Math.round(confirmedPct * 100), stroke: confirmedStroke, offset: confirmedOffset },
    pending: { count: pending, pct: Math.round(pendingPct * 100), stroke: pendingStroke, offset: pendingOffset },
    other: { count: other, pct: Math.round(otherPct * 100), stroke: otherStroke, offset: otherOffset },
  }
})

// User Ecosystem pie/donut chart computation
const userSlices = computed(() => {
  const owners = props.stats.totalOwners || 0
  const customers = props.stats.totalCustomers || 0
  const drivers = props.stats.totalDrivers || 0
  const total = owners + customers + drivers
  const calcTotal = total > 0 ? total : 1

  const circumference = 2 * Math.PI * 40 // 251.32

  const ownersPct = owners / calcTotal
  const customersPct = customers / calcTotal
  const driversPct = drivers / calcTotal

  const ownersStroke = ownersPct * circumference
  const customersStroke = customersPct * circumference
  const driversStroke = driversPct * circumference

  const ownersOffset = 0
  const customersOffset = -ownersStroke
  const driversOffset = -(ownersStroke + customersStroke)

  return {
    total,
    circumference,
    owners: { count: owners, pct: Math.round(ownersPct * 100), stroke: ownersStroke, offset: ownersOffset },
    customers: { count: customers, pct: Math.round(customersPct * 100), stroke: customersStroke, offset: customersOffset },
    drivers: { count: drivers, pct: Math.round(driversPct * 100), stroke: driversStroke, offset: driversOffset },
  }
})

// KPI Metrics
const avgBookingValue = computed(() => {
  const rev = props.stats.totalRevenue || 0
  const b = props.stats.totalBookings || 0
  if (!b) return '$0'
  return `$${Math.round(rev / b)}`
})

const confirmationRate = computed(() => {
  const b = props.stats.totalBookings || 0
  const conf = props.stats.confirmedBookings || 0
  if (!b) return '0%'
  return `${Math.round((conf / b) * 100)}%`
})

const driverAllocationRate = computed(() => {
  const cars = props.stats.totalCars || 0
  const drivers = props.stats.totalDrivers || 0
  if (!cars) return '100%'
  return `${Math.min(100, Math.round((drivers / cars) * 100))}%`
})
</script>

<template>
  <div class="space-y-6">
    <!-- Top Summary Ribbon -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg font-bold shrink-0">
          <i class="ri-pie-chart-2-line" />
        </div>
        <div>
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Confirmation Rate</span>
          <span class="text-lg font-black text-slate-900 dark:text-white">{{ confirmationRate }}</span>
        </div>
      </div>

      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg font-bold shrink-0">
          <i class="ri-money-dollar-circle-line" />
        </div>
        <div>
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Avg Revenue / Booking</span>
          <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ avgBookingValue }}</span>
        </div>
      </div>

      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg font-bold shrink-0">
          <i class="ri-user-shared-line" />
        </div>
        <div>
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Driver Capacity</span>
          <span class="text-lg font-black text-purple-600 dark:text-purple-400">{{ driverAllocationRate }}</span>
        </div>
      </div>

      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-2xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-lg font-bold shrink-0">
          <i class="ri-shield-check-line" />
        </div>
        <div>
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Verified Vehicles</span>
          <span class="text-lg font-black text-slate-900 dark:text-white">{{ stats.verifiedCarsCount }} / {{ stats.totalCars }}</span>
        </div>
      </div>
    </div>

    <!-- Charts Grid Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Left: Entity Volume Bar Chart (7 Cols) -->
      <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 flex flex-col justify-between space-y-5">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-600" />
              <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
                Entity Volume & Distribution Matrix
              </h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Comparative metrics across Booked Cars, Owners, Customers, Drivers & Vehicles
            </p>
          </div>

          <span class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs font-mono">
            Bar Chart
          </span>
        </div>

        <!-- Horizontal Interactive Bars -->
        <div class="space-y-4 pt-2">
          <div
            v-for="(bar, index) in entityBars"
            :key="index"
            class="space-y-1.5 group"
          >
            <div class="flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs" :class="bar.bgLight">
                  <i :class="bar.icon" />
                </div>
                <span class="font-bold text-slate-900 dark:text-white">{{ bar.label }}</span>
                <span class="text-slate-400 text-[11px]">({{ bar.sublabel }})</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-black text-slate-900 dark:text-white font-mono text-sm">{{ bar.count }}</span>
                <span class="text-slate-400 text-[10px] font-mono font-bold">{{ bar.percentage }}% scale</span>
              </div>
            </div>

            <!-- Progress Bar with Glow -->
            <div class="w-full h-3.5 rounded-full bg-slate-100 dark:bg-slate-800/80 overflow-hidden p-0.5 border border-slate-200/60 dark:border-slate-700/60 shadow-inner">
              <div
                class="h-full rounded-full bg-gradient-to-r transition-all duration-700 ease-out shadow-xs"
                :class="bar.gradient"
                :style="{ width: `${Math.max(bar.percentage, 8)}%` }"
              />
            </div>
          </div>
        </div>

        <!-- Bottom Legend -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-[11px] text-slate-400">
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-500" /> Bookings
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500" /> Owners
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-purple-500" /> Customers
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-cyan-500" /> Drivers
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-indigo-500" /> Vehicles
          </span>
        </div>
      </div>

      <!-- Right: Donut & Pie Charts Suite (5 Cols) -->
      <div class="lg:col-span-5 grid grid-cols-1 gap-6">
        <!-- Donut Chart 1: Rental Booking Resolutions -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">
                Booking Status Breakdown
              </h4>
              <p class="text-[11px] text-slate-400">
                Resolution distribution for {{ stats.totalBookings }} total bookings
              </p>
            </div>
            <span class="px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 font-bold text-[10px]">
              Pie / Donut
            </span>
          </div>

          <div class="flex items-center justify-around gap-4 pt-1">
            <!-- SVG Donut Chart -->
            <div class="relative w-28 h-28 flex items-center justify-center shrink-0">
              <svg class="w-28 h-28 -rotate-90 transform" viewBox="0 0 100 100">
                <!-- Background Ring -->
                <circle
                  cx="50"
                  cy="50"
                  r="40"
                  stroke="currentColor"
                  stroke-width="12"
                  fill="transparent"
                  class="text-slate-100 dark:text-slate-800"
                />
                <!-- Confirmed Slice (Emerald) -->
                <circle
                  v-if="bookingSlices.confirmed.count > 0"
                  cx="50"
                  cy="50"
                  r="40"
                  stroke="#10B981"
                  stroke-width="12"
                  fill="transparent"
                  :stroke-dasharray="`${bookingSlices.confirmed.stroke} ${bookingSlices.circumference}`"
                  :stroke-dashoffset="bookingSlices.confirmed.offset"
                  stroke-linecap="round"
                  class="transition-all duration-700 ease-out"
                />
                <!-- Pending Slice (Amber) -->
                <circle
                  v-if="bookingSlices.pending.count > 0"
                  cx="50"
                  cy="50"
                  r="40"
                  stroke="#F59E0B"
                  stroke-width="12"
                  fill="transparent"
                  :stroke-dasharray="`${bookingSlices.pending.stroke} ${bookingSlices.circumference}`"
                  :stroke-dashoffset="bookingSlices.pending.offset"
                  stroke-linecap="round"
                  class="transition-all duration-700 ease-out"
                />
                <!-- Other/Cancelled Slice (Indigo/Rose) -->
                <circle
                  v-if="bookingSlices.other.count > 0"
                  cx="50"
                  cy="50"
                  r="40"
                  stroke="#6366F1"
                  stroke-width="12"
                  fill="transparent"
                  :stroke-dasharray="`${bookingSlices.other.stroke} ${bookingSlices.circumference}`"
                  :stroke-dashoffset="bookingSlices.other.offset"
                  stroke-linecap="round"
                  class="transition-all duration-700 ease-out"
                />
              </svg>
              <!-- Center Metric Text -->
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <span class="text-base font-black text-slate-900 dark:text-white font-mono leading-none">
                  {{ stats.totalBookings }}
                </span>
                <span class="text-[9px] uppercase font-extrabold text-slate-400 mt-0.5">Total</span>
              </div>
            </div>

            <!-- Legend List -->
            <div class="space-y-2 text-xs flex-1">
              <div class="flex items-center justify-between p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="flex items-center gap-1.5 font-semibold text-slate-700 dark:text-slate-300">
                  <span class="w-2 h-2 rounded-full bg-emerald-500" /> Confirmed
                </span>
                <span class="font-bold font-mono text-emerald-600">{{ bookingSlices.confirmed.count }} ({{ bookingSlices.confirmed.pct }}%)</span>
              </div>
              <div class="flex items-center justify-between p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="flex items-center gap-1.5 font-semibold text-slate-700 dark:text-slate-300">
                  <span class="w-2 h-2 rounded-full bg-amber-500" /> Pending
                </span>
                <span class="font-bold font-mono text-amber-500">{{ bookingSlices.pending.count }} ({{ bookingSlices.pending.pct }}%)</span>
              </div>
              <div class="flex items-center justify-between p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="flex items-center gap-1.5 font-semibold text-slate-700 dark:text-slate-300">
                  <span class="w-2 h-2 rounded-full bg-indigo-500" /> Cancel/Other
                </span>
                <span class="font-bold font-mono text-indigo-600">{{ bookingSlices.other.count }} ({{ bookingSlices.other.pct }}%)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Donut Chart 2: User Ecosystem Composition -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">
                Platform Stakeholder Ecosystem
              </h4>
              <p class="text-[11px] text-slate-400">
                Proportion of Owners, Customers & Drivers
              </p>
            </div>
            <span class="px-2 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 font-bold text-[10px]">
              User Share
            </span>
          </div>

          <div class="flex items-center justify-around gap-4 pt-1">
            <!-- SVG Donut Chart -->
            <div class="relative w-28 h-28 flex items-center justify-center shrink-0">
              <svg class="w-28 h-28 -rotate-90 transform" viewBox="0 0 100 100">
                <!-- Background Ring -->
                <circle
                  cx="50"
                  cy="50"
                  r="40"
                  stroke="currentColor"
                  stroke-width="12"
                  fill="transparent"
                  class="text-slate-100 dark:text-slate-800"
                />
                <!-- Owners Slice (Emerald) -->
                <circle
                  v-if="userSlices.owners.count > 0"
                  cx="50"
                  cy="50"
                  r="40"
                  stroke="#10B981"
                  stroke-width="12"
                  fill="transparent"
                  :stroke-dasharray="`${userSlices.owners.stroke} ${userSlices.circumference}`"
                  :stroke-dashoffset="userSlices.owners.offset"
                  stroke-linecap="round"
                  class="transition-all duration-700 ease-out"
                />
                <!-- Customers Slice (Purple) -->
                <circle
                  v-if="userSlices.customers.count > 0"
                  cx="50"
                  cy="50"
                  r="40"
                  stroke="#A855F7"
                  stroke-width="12"
                  fill="transparent"
                  :stroke-dasharray="`${userSlices.customers.stroke} ${userSlices.circumference}`"
                  :stroke-dashoffset="userSlices.customers.offset"
                  stroke-linecap="round"
                  class="transition-all duration-700 ease-out"
                />
                <!-- Drivers Slice (Cyan) -->
                <circle
                  v-if="userSlices.drivers.count > 0"
                  cx="50"
                  cy="50"
                  r="40"
                  stroke="#06B6D4"
                  stroke-width="12"
                  fill="transparent"
                  :stroke-dasharray="`${userSlices.drivers.stroke} ${userSlices.circumference}`"
                  :stroke-dashoffset="userSlices.drivers.offset"
                  stroke-linecap="round"
                  class="transition-all duration-700 ease-out"
                />
              </svg>
              <!-- Center Metric Text -->
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <span class="text-base font-black text-slate-900 dark:text-white font-mono leading-none">
                  {{ userSlices.total }}
                </span>
                <span class="text-[9px] uppercase font-extrabold text-slate-400 mt-0.5">Users</span>
              </div>
            </div>

            <!-- Legend List -->
            <div class="space-y-2 text-xs flex-1">
              <div class="flex items-center justify-between p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="flex items-center gap-1.5 font-semibold text-slate-700 dark:text-slate-300">
                  <span class="w-2 h-2 rounded-full bg-emerald-500" /> Owners
                </span>
                <span class="font-bold font-mono text-emerald-600">{{ userSlices.owners.count }} ({{ userSlices.owners.pct }}%)</span>
              </div>
              <div class="flex items-center justify-between p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="flex items-center gap-1.5 font-semibold text-slate-700 dark:text-slate-300">
                  <span class="w-2 h-2 rounded-full bg-purple-500" /> Customers
                </span>
                <span class="font-bold font-mono text-purple-600">{{ userSlices.customers.count }} ({{ userSlices.customers.pct }}%)</span>
              </div>
              <div class="flex items-center justify-between p-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="flex items-center gap-1.5 font-semibold text-slate-700 dark:text-slate-300">
                  <span class="w-2 h-2 rounded-full bg-cyan-500" /> Drivers
                </span>
                <span class="font-bold font-mono text-cyan-600">{{ userSlices.drivers.count }} ({{ userSlices.drivers.pct }}%)</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
