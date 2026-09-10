<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps<{
  stats?: {
    totalCars: number
    totalOwners: number
    totalCustomers: number
    totalDrivers: number
    totalBookings: number
    totalRevenue: number
  }
  totalCars?: number
  totalCustomers?: number
  totalBookings?: number
  totalRevenue?: number
}>()

const statsData = ref<any>({
  totalCars: props.stats?.totalCars ?? props.totalCars ?? 0,
  totalOwners: props.stats?.totalOwners ?? 0,
  totalCustomers: props.stats?.totalCustomers ?? props.totalCustomers ?? 0,
  totalDrivers: props.stats?.totalDrivers ?? 0,
  totalBookings: props.stats?.totalBookings ?? props.totalBookings ?? 0,
  totalRevenue: props.stats?.totalRevenue ?? props.totalRevenue ?? 0,
})

const loading = ref(false)

const fetchAdminStats = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/admin/stats')
    if (res.data.status === 'success') {
      statsData.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to load admin stats:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchAdminStats()
})
</script>

<template>
  <AppLayout>
    <Head title="Admin Master Control Dashboard" />

    <template #header>
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
        Master Admin System Control Dashboard
      </div>
    </template>

    <div class="space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Admin Hero -->
      <div class="p-8 rounded-3xl bg-gradient-to-r from-indigo-700 via-purple-700 to-slate-900 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 relative z-10">
          <span class="px-3 py-1 rounded-full bg-white/20 text-indigo-100 text-xs font-semibold uppercase tracking-wider">
            Super Administrator Hub
          </span>
          <h2 class="text-3xl font-black tracking-tight">
            System Intelligence & Operations
          </h2>
          <p class="text-xs text-indigo-100/80 max-w-xl leading-relaxed">
            Full authority over car verification, fleet owners, customer accounts, driver rosters, and booking calendar disputes.
          </p>
        </div>

        <div class="flex flex-wrap gap-3 relative z-10">
          <button
            class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md transition-all"
            @click="fetchAdminStats"
          >
            ↻ Refresh Metrics
          </button>
        </div>
      </div>

      <!-- Stats Grid (6 cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Fleet Vehicles</p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ statsData.totalCars }}</h3>
            <Link href="/admin/cars" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline mt-2 inline-block">
              Verify / Manage Cars &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
            🚗
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Fleet Owners</p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ statsData.totalOwners }}</h3>
            <Link href="/admin/owners" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline mt-2 inline-block">
              Manage Owners &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
            🏢
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">System Drivers</p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ statsData.totalDrivers }}</h3>
            <Link href="/admin/cars" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline mt-2 inline-block">
              Driver Oversight &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
            👨‍✈️
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Customers</p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ statsData.totalCustomers }}</h3>
            <Link href="/admin/customers" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline mt-2 inline-block">
              Customer Directory &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl">
            👥
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Bookings</p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ statsData.totalBookings }}</h3>
            <Link href="/admin/booked-cars" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline mt-2 inline-block">
              View Calendar & Bookings &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
            📅
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Volume</p>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">${{ statsData.totalRevenue }}</h3>
            <span class="text-xs text-gray-400 mt-2 block">All verified rentals</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
            💵
          </div>
        </div>
      </div>

      <!-- Quick Portals Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Link
          href="/admin/cars"
          class="p-6 rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-xl shadow-blue-500/20 hover:scale-[1.01] transition-all"
        >
          <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl mb-4">
            🚗
          </div>
          <h4 class="font-bold text-xl">Fleet Car Approvals</h4>
          <p class="text-xs text-blue-100 mt-1 leading-relaxed">
            Verify newly added cars by owners, inspect blue book documents, and activate vehicle listings.
          </p>
        </Link>

        <Link
          href="/admin/booked-cars"
          class="p-6 rounded-3xl bg-gradient-to-br from-purple-600 to-pink-700 text-white shadow-xl shadow-purple-500/20 hover:scale-[1.01] transition-all"
        >
          <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl mb-4">
            📅
          </div>
          <h4 class="font-bold text-xl">Rental Calendars & Bookings</h4>
          <p class="text-xs text-purple-100 mt-1 leading-relaxed">
            Monitor zero-conflict calendar schedules, verify payment records, and resolve booking cancellations.
          </p>
        </Link>

        <Link
          href="/admin/email-templates"
          class="p-6 rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-xl shadow-emerald-500/20 hover:scale-[1.01] transition-all"
        >
          <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl mb-4">
            ✉️
          </div>
          <h4 class="font-bold text-xl">Email Templates & MFA</h4>
          <p class="text-xs text-emerald-100 mt-1 leading-relaxed">
            Customize automated emails for OTP verification, booking invoices, and customer alerts.
          </p>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
