<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps<{
  customer?: any
  activeBookings?: Array<any>
  totalRentedCars?: number
  totalSpent?: number
}>()

const bookingsList = ref<Array<any>>(props.activeBookings || [])
const loading = ref(false)

const fetchMyBookings = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/customer/my-bookings')
    if (res.data.status === 'success') {
      bookingsList.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to load my bookings:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (!bookingsList.value.length) {
    fetchMyBookings()
  }
})
</script>

<template>
  <AppLayout>
    <Head title="Customer Travel & Rental Portal" />

    <template #header>
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
        Customer Rental Dashboard
      </div>
    </template>

    <div class="space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <!-- Banner -->
      <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-900 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 relative z-10">
          <span class="px-3 py-1 rounded-full bg-white/20 text-blue-100 text-xs font-semibold uppercase tracking-wider">
            Premium Car Rental
          </span>
          <h2 class="text-3xl font-black tracking-tight">
            Ready for your next journey, {{ customer?.name || 'Traveler' }}?
          </h2>
          <p class="text-xs text-blue-100/90 max-w-lg leading-relaxed">
            Browse verified vehicles with transparent pricing, zero-conflict calendar scheduling, certified drivers, and instant payment receipts.
          </p>
        </div>
        <Link
          href="/cars"
          class="px-6 py-3.5 rounded-2xl bg-white text-blue-700 font-bold text-xs shadow-xl hover:bg-blue-50 transition-all shrink-0 relative z-10"
        >
          Explore Fleet Cars &rarr;
        </Link>
      </div>

      <!-- Quick Metrics -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Confirmed Rentals</p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">
              {{ bookingsList.filter(b => b.status === 'confirm').length }}
            </h3>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
            🚗
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending Approval</p>
            <h3 class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">
              {{ bookingsList.filter(b => b.status === 'pending').length }}
            </h3>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
            ⏳
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Rental Spend</p>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
              ${{ bookingsList.filter(b => b.status === 'confirm').reduce((acc, b) => acc + (parseFloat(b.total_price) || 0), 0) }}
            </h3>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
            💳
          </div>
        </div>
      </div>

      <!-- Bookings List -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-xl text-gray-900 dark:text-white">
              My Rental Bookings & Invoices
            </h3>
            <p class="text-xs text-gray-500">
              Your active and past car bookings with driver details and payment receipts
            </p>
          </div>
          <Link
            href="/cars"
            class="text-xs font-semibold text-blue-600 hover:underline"
          >
            + Book another car
          </Link>
        </div>

        <div v-if="loading" class="py-12 text-center">
          <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
          <p class="text-xs text-gray-500">Loading bookings...</p>
        </div>

        <div v-else-if="bookingsList.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div
            v-for="booking in bookingsList"
            :key="booking.id"
            class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4"
          >
            <div class="flex items-start justify-between">
              <div>
                <span class="text-[10px] font-mono font-bold text-gray-400 block mb-0.5">Booking #{{ booking.id }}</span>
                <h4 class="font-bold text-lg text-gray-900 dark:text-white">
                  {{ booking.car?.car_name }} {{ booking.car?.car_model }}
                </h4>
                <span class="text-xs font-mono text-gray-500">{{ booking.car?.car_number }}</span>
              </div>
              <span
                :class="booking.status === 'confirm' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
              >
                {{ booking.status === 'confirm' ? 'Paid & Confirmed' : booking.status }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/60">
              <div>
                <span class="text-gray-400 block">Pickup Date:</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ booking.pick_up_date }}</span>
              </div>
              <div>
                <span class="text-gray-400 block">Return Date:</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ booking.last_date }}</span>
              </div>
              <div>
                <span class="text-gray-400 block">Pickup Location:</span>
                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ booking.pickup_location }}</span>
              </div>
              <div>
                <span class="text-gray-400 block">Total Charged:</span>
                <span class="font-black text-emerald-600">${{ booking.total_price }}</span>
              </div>
            </div>

            <!-- Driver & Owner Details snippet -->
            <div class="p-3 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 text-xs flex justify-between items-center text-gray-600 dark:text-gray-300">
              <div>
                <span>Chauffeur: <strong>{{ booking.car?.driver?.name || booking.car?.driver_name || 'Assigned' }}</strong></span>
                <span v-if="booking.car?.driver?.phone" class="block text-[11px] text-gray-400">Tel: {{ booking.car?.driver?.phone }}</span>
              </div>
              <span class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold">Owner: {{ booking.car?.owner?.full_name || 'Fleet Partner' }}</span>
            </div>
          </div>
        </div>

        <div v-else class="p-12 text-center bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800">
          <p class="text-xs text-gray-500">You haven't reserved any vehicles yet.</p>
          <Link href="/cars" class="mt-3 inline-block px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md">
            Browse Available Cars
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
