<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import { resolveMediaUrl } from '@/utils/helpers'
import axios from 'axios'

const props = defineProps<{
  customer?: any
  activeBookings?: Array<any>
  featuredCars?: Array<any>
  totalRentedCars?: number
  totalSpent?: number
}>()

const bookingsList = ref<Array<any>>(props.activeBookings || [])
const featuredFleet = ref<Array<any>>(props.featuredCars || [])
const loading = ref(false)

const getCarImage = (car: any) => {
  const url = resolveMediaUrl(car.car_photo || car.image, car.car_photo_path || car.image_path, 'car')

  return url || 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&auto=format&fit=crop&q=80'
}

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
  <CustomerLayout>
    <Head title="Traveler Portal Dashboard" />

    <div class="space-y-8 max-w-7xl mx-auto">
      <!-- Traveler Hero Banner -->
      <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 relative z-10">
          <span class="px-3 py-1 rounded-full bg-white/20 text-blue-100 text-xs font-semibold uppercase tracking-wider">
            Premium Traveler Hub
          </span>
          <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
            Ready for your next journey, {{ customer?.name || 'Traveler' }}?
          </h2>
          <p class="text-xs sm:text-sm text-blue-100/90 max-w-xl leading-relaxed">
            Browse verified vehicles with transparent daily rates, certified chauffeurs, live GPS-ready itineraries, and instant PDF invoices.
          </p>
        </div>

        <div class="flex flex-wrap gap-3 relative z-10">
          <Link
            href="/customer/cars"
            class="px-5 py-3.5 rounded-2xl bg-white text-blue-700 font-bold text-xs shadow-xl hover:bg-blue-50 transition-all flex items-center gap-2 shrink-0"
          >
            <i class="ri-car-line text-base text-blue-600" />
            <span>Browse Fleet</span>
          </Link>
          <Link
            href="/customer/calendar"
            class="px-5 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-2 shrink-0"
          >
            <i class="ri-calendar-line text-base" />
            <span>Check Calendar</span>
          </Link>
          <Link
            href="/ai-chat"
            class="px-5 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-2 shrink-0"
          >
            <i class="ri-sparkling-fill text-amber-300" />
            <span>AI Trip Assistant</span>
          </Link>
        </div>
      </div>

      <!-- Quick Metrics -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Confirmed Rentals
            </p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">
              {{ bookingsList.filter((b: any) => b.status === 'confirm' || b.status === 'booked').length }}
            </h3>
            <Link
              href="/customer/bookings"
              class="text-xs text-blue-600 dark:text-blue-400 font-semibold hover:underline mt-2 inline-block"
            >
              View Invoices &rarr;
            </Link>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-car-line" />
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Pending Approval
            </p>
            <h3 class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">
              {{ bookingsList.filter((b: any) => b.status === 'pending').length }}
            </h3>
            <span class="text-xs text-slate-400 mt-2 block">Awaiting partner confirmation</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-time-line" />
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
              Total Rental Spend
            </p>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
              ${{ bookingsList.filter((b: any) => b.status === 'confirm' || b.status === 'booked').reduce((acc: number, b: any) => acc + (parseFloat(b.total_price) || 0), 0) }}
            </h3>
            <span class="text-xs text-slate-400 mt-2 block">Lifetime confirmed spend</span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shadow-xs">
            <i class="ri-wallet-3-line" />
          </div>
        </div>
      </div>

      <!-- Featured Vehicles for Next Journey -->
      <div
        v-if="featuredFleet.length > 0"
        class="space-y-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-black text-xl text-slate-900 dark:text-white">
              Featured Fleet & Quick Booking
            </h3>
            <p class="text-xs text-slate-500">
              Top verified vehicles ready for immediate reservation
            </p>
          </div>
          <Link
            href="/customer/cars"
            class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300 hover:bg-blue-100 font-bold text-xs transition-colors"
          >
            All Fleet ({{ featuredFleet.length }}+) &rarr;
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="car in featuredFleet.slice(0, 3)"
            :key="car.id"
            class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-900 transition-all group"
          >
            <div>
              <div class="relative overflow-hidden rounded-2xl mb-4 bg-slate-100 dark:bg-slate-800 h-44">
                <img
                  :src="getCarImage(car)"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  :alt="car.car_name"
                >
                <span class="absolute top-3 end-3 px-3 py-1 rounded-full text-xs font-black bg-blue-600 text-white shadow-md">
                  ${{ car.car_price_per_day || 65 }}/day
                </span>
              </div>

              <div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors">
                  {{ car.car_name || car.brand }} {{ car.car_model || car.model }}
                </h4>
                <p class="text-xs text-slate-400 font-mono">
                  {{ car.car_number }} &bull; {{ car.number_of_seats }} Seats
                </p>
              </div>

              <div class="mt-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs space-y-1">
                <div class="flex justify-between items-center">
                  <span class="text-slate-400">Chauffeur:</span>
                  <span class="font-bold text-slate-800 dark:text-slate-200">{{ car.driver?.name || car.driver_name || 'Assigned Driver' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-slate-400">Partner:</span>
                  <span class="font-semibold text-blue-600 dark:text-blue-400">{{ car.owner?.full_name || 'Fleet Partner' }}</span>
                </div>
              </div>
            </div>

            <div class="flex gap-2 pt-1">
              <Link
                :href="`/customer/cars/${car.id}`"
                class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs text-center shadow-md shadow-blue-600/20 transition-all flex items-center justify-center gap-1.5"
              >
                <span>Reserve Vehicle</span>
                <i class="ri-arrow-right-line" />
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Bookings List -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-black text-xl text-slate-900 dark:text-white">
              My Rental Bookings & Invoices
            </h3>
            <p class="text-xs text-slate-500">
              Active reservations with assigned chauffeurs, route points, and PDF invoices
            </p>
          </div>
          <Link
            href="/customer/cars"
            class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300 hover:bg-blue-100 font-bold text-xs transition-colors"
          >
            + Book Another Car
          </Link>
        </div>

        <div
          v-if="loading"
          class="py-12 text-center"
        >
          <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-2" />
          <p class="text-xs text-slate-500">
            Loading reservations...
          </p>
        </div>

        <div
          v-else-if="bookingsList.length > 0"
          class="grid grid-cols-1 md:grid-cols-2 gap-6"
        >
          <div
            v-for="booking in bookingsList"
            :key="booking.id"
            class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4 hover:shadow-md transition-shadow"
          >
            <div class="flex items-start justify-between gap-4">
              <div>
                <span class="text-[10px] font-mono font-bold text-slate-400 block mb-0.5">Booking #{{ booking.id }}</span>
                <h4 class="font-bold text-lg text-slate-900 dark:text-white">
                  {{ booking.car?.car_name || booking.car?.brand }} {{ booking.car?.car_model || booking.car?.model }}
                </h4>
                <span class="text-xs font-mono text-slate-500">{{ booking.car?.car_number }}</span>
              </div>
              <span
                :class="booking.status === 'confirm' || booking.status === 'booked' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
              >
                {{ booking.status === 'confirm' || booking.status === 'booked' ? 'Paid & Confirmed' : booking.status }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
              <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Pickup Date:</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ String(booking.pick_up_date || '').split('T')[0] }}</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Return Date:</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ String(booking.last_date || '').split('T')[0] }}</span>
              </div>
              <div class="col-span-2">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Route:</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ booking.pickup_location }} &rarr; {{ booking.drop_location }}</span>
              </div>
              <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                <span class="text-slate-500 font-medium">Total Charged:</span>
                <span class="font-black text-emerald-600 text-sm">${{ booking.total_price }}</span>
              </div>
            </div>

            <!-- Chauffeur & Fleet Partner Info -->
            <div class="p-3 rounded-xl bg-blue-50/60 dark:bg-blue-950/30 text-xs flex justify-between items-center text-slate-700 dark:text-slate-300 border border-blue-100/60 dark:border-blue-900/40">
              <div>
                <span class="font-bold text-slate-900 dark:text-white">Chauffeur: {{ booking.car?.driver?.name || booking.car?.driver_name || 'Assigned Chauffeur' }}</span>
                <span
                  v-if="booking.car?.driver?.phone"
                  class="block text-[11px] text-slate-400 font-mono"
                >Tel: {{ booking.car?.driver?.phone }}</span>
              </div>
              <span class="text-[11px] text-blue-600 dark:text-blue-400 font-bold">Owner: {{ booking.car?.owner?.full_name || 'Fleet Partner' }}</span>
            </div>

            <div class="flex items-center justify-between pt-1">
              <Link
                v-if="booking.status === 'confirm' || booking.status === 'booked'"
                :href="`/customer/booking/${booking.id}/pdf`"
                class="text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 flex items-center gap-1"
              >
                <i class="ri-file-download-line text-sm" />
                <span>Invoice PDF</span>
              </Link>
              <span v-else />

              <Link
                href="/customer/bookings"
                class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1"
              >
                <span>View Full Details &rarr;</span>
              </Link>
            </div>
          </div>
        </div>

        <div
          v-else
          class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs"
        >
          <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-950 text-blue-600 flex items-center justify-center text-3xl mx-auto mb-3">
            <i class="ri-car-line" />
          </div>
          <h4 class="font-black text-lg text-slate-900 dark:text-white">
            No active reservations
          </h4>
          <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
            You don't have any vehicle reservations currently. Explore our premium showroom and book your dream car.
          </p>
          <Link
            href="/customer/cars"
            class="mt-4 inline-block px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-colors"
          >
            Browse Available Cars
          </Link>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>
