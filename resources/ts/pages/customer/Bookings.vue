<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{
  bookings: Array<any>
  customer?: any
}>()

const search = ref('')
const activeFilter = ref<'all' | 'confirm' | 'pending' | 'cancel'>('all')
const cancellingId = ref<number | null>(null)
const cancelSuccess = ref('')
const cancelError = ref('')

const filteredBookings = computed(() => {
  return (props.bookings || []).filter(b => {
    // Filter by status
    if (activeFilter.value === 'confirm' && b.status !== 'confirm' && b.status !== 'booked') return false
    if (activeFilter.value === 'pending' && b.status !== 'pending' && b.status !== 'reserved') return false
    if (activeFilter.value === 'cancel' && b.status !== 'cancel' && b.status !== 'canceled') return false

    // Filter by search query
    if (search.value.trim()) {
      const q = search.value.toLowerCase()
      const carName = (b.car?.car_name || b.car?.brand || '').toLowerCase()
      const carModel = (b.car?.car_model || b.car?.model || '').toLowerCase()
      const refId = String(b.id)
      const loc = (b.pickup_location || '').toLowerCase()
      
      return carName.includes(q) || carModel.includes(q) || refId.includes(q) || loc.includes(q)
    }

    return true
  })
})

const confirmedCount = computed(() => {
  return (props.bookings || []).filter(b => b.status === 'confirm' || b.status === 'booked').length
})

const pendingCount = computed(() => {
  return (props.bookings || []).filter(b => b.status === 'pending' || b.status === 'reserved').length
})

const totalSpent = computed(() => {
  return (props.bookings || [])
    .filter(b => b.status === 'confirm' || b.status === 'booked')
    .reduce((sum, b) => sum + (parseFloat(b.total_price) || 0), 0)
})

const handleCancel = (bookingId: number) => {
  if (!confirm('Are you sure you want to cancel this booking reservation?')) return
  cancellingId.value = bookingId
  cancelSuccess.value = ''
  cancelError.value = ''

  router.post(`/customer/bookings/${bookingId}/cancel`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      cancelSuccess.value = 'Booking #' + bookingId + ' has been successfully canceled.'
      cancellingId.value = null
    },
    onError: () => {
      cancelError.value = 'Failed to cancel the booking. Please try again.'
      cancellingId.value = null
    },
  })
}

const getCarImage = (car: any) => {
  if (car?.car_photo) {
    return car.car_photo.startsWith('http') ? car.car_photo : `/storage/${car.car_photo}`
  }
  
  return 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=600&q=80'
}
</script>

<template>
  <AppLayout>
    <Head title="My Rental Bookings - Customer" />

    <div class="max-w-7xl mx-auto space-y-8">
      <!-- Breadcrumb Tabs -->
      <div class="flex items-center gap-2 text-xs">
        <Link
          href="/customer/dashboard"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50"
        >
          <i class="ri-dashboard-line" />
          <span>Dashboard</span>
        </Link>
        <button
          type="button"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-blue-600 border-blue-200 bg-blue-50/70 font-semibold"
        >
          <i class="ri-book-read-line" />
          <span>My Bookings</span>
        </button>
      </div>

      <!-- Header & Action -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            My Rental Bookings
          </h2>
          <p class="text-xs text-gray-500 mt-0.5">
            Manage your active vehicle reservations, driver assignments, and payment invoices
          </p>
        </div>

        <Link
          href="/cars"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition-all self-start sm:self-auto"
        >
          <i class="ri-add-line text-sm" />
          <span>Book Another Vehicle</span>
        </Link>
      </div>

      <!-- Metrics Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
              Confirmed Rentals
            </p>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">
              {{ confirmedCount }}
            </h3>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl">
            <i class="ri-checkbox-circle-line" />
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
              Pending Confirmation
            </p>
            <h3 class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">
              {{ pendingCount }}
            </h3>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl">
            <i class="ri-time-line" />
          </div>
        </div>

        <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
              Total Rental Spend
            </p>
            <h3 class="text-3xl font-black text-blue-600 dark:text-blue-400 mt-1">
              ${{ totalSpent }}
            </h3>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl">
            <i class="ri-bank-card-line" />
          </div>
        </div>
      </div>

      <!-- Alerts -->
      <div
        v-if="cancelSuccess"
        class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2"
      >
        <i class="ri-checkbox-circle-fill text-emerald-600 text-base" />
        <span>{{ cancelSuccess }}</span>
      </div>
      <div
        v-if="cancelError"
        class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2"
      >
        <i class="ri-error-warning-fill text-rose-600 text-base" />
        <span>{{ cancelError }}</span>
      </div>

      <!-- Filters & Search Bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Status Filter Tabs -->
        <div class="flex rounded-xl bg-gray-100 dark:bg-gray-800 p-1 text-xs font-semibold w-full sm:w-auto">
          <button
            type="button"
            :class="activeFilter === 'all' ? 'bg-white dark:bg-gray-900 text-blue-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
            class="flex-1 sm:flex-initial px-4 py-1.5 rounded-lg transition-all"
            @click="activeFilter = 'all'"
          >
            All ({{ (bookings || []).length }})
          </button>
          <button
            type="button"
            :class="activeFilter === 'confirm' ? 'bg-white dark:bg-gray-900 text-emerald-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
            class="flex-1 sm:flex-initial px-4 py-1.5 rounded-lg transition-all"
            @click="activeFilter = 'confirm'"
          >
            Confirmed ({{ confirmedCount }})
          </button>
          <button
            type="button"
            :class="activeFilter === 'pending' ? 'bg-white dark:bg-gray-900 text-amber-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
            class="flex-1 sm:flex-initial px-4 py-1.5 rounded-lg transition-all"
            @click="activeFilter = 'pending'"
          >
            Pending ({{ pendingCount }})
          </button>
          <button
            type="button"
            :class="activeFilter === 'cancel' ? 'bg-white dark:bg-gray-900 text-rose-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
            class="flex-1 sm:flex-initial px-4 py-1.5 rounded-lg transition-all"
            @click="activeFilter = 'cancel'"
          >
            Canceled
          </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full sm:w-64">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm" />
          <input
            v-model="search"
            type="text"
            placeholder="Search bookings..."
            class="w-full py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs focus:ring-2 focus:ring-blue-500"
            style="padding-left: 2.25rem; padding-right: 0.875rem"
          >
        </div>
      </div>

      <!-- Bookings Grid -->
      <div
        v-if="filteredBookings.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 gap-6"
      >
        <div
          v-for="booking in filteredBookings"
          :key="booking.id"
          class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm space-y-5 hover:shadow-md transition-shadow"
        >
          <!-- Top Row: Car Title & Status -->
          <div class="flex items-start gap-4">
            <img
              :src="getCarImage(booking.car)"
              :alt="booking.car?.car_name"
              class="w-20 h-16 object-cover rounded-2xl border border-gray-100 dark:border-gray-800 shrink-0"
            >
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2">
                <span class="text-[10px] font-mono font-bold text-gray-400">#BK-{{ booking.id }}</span>
                <span
                  :class="{
                    'bg-emerald-50 text-emerald-700 border-emerald-200': booking.status === 'confirm' || booking.status === 'booked',
                    'bg-amber-50 text-amber-700 border-amber-200': booking.status === 'pending' || booking.status === 'reserved',
                    'bg-rose-50 text-rose-700 border-rose-200': booking.status === 'cancel' || booking.status === 'canceled',
                  }"
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border"
                >
                  {{ (booking.status === 'confirm' || booking.status === 'booked') ? 'Paid & Confirmed' : (booking.status === 'cancel' || booking.status === 'canceled') ? 'Canceled' : 'Pending' }}
                </span>
              </div>
              <h4 class="font-bold text-base text-gray-900 dark:text-white truncate mt-0.5">
                {{ booking.car?.car_name || booking.car?.brand }} {{ booking.car?.car_model || booking.car?.model }}
              </h4>
              <p class="text-xs font-mono text-gray-500">
                Plate: {{ booking.car?.car_number || 'N/A' }}
              </p>
            </div>
          </div>

          <!-- Schedule & Details Grid -->
          <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-800/60 text-xs">
            <div>
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Pickup Date</span>
              <span class="font-bold text-gray-900 dark:text-white">
                {{ String(booking.pick_up_date || '').split('T')[0] }}
              </span>
            </div>
            <div>
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Return Date</span>
              <span class="font-bold text-gray-900 dark:text-white">
                {{ String(booking.last_date || '').split('T')[0] }}
              </span>
            </div>
            <div class="col-span-2">
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Pickup / Return Location</span>
              <span class="font-medium text-gray-800 dark:text-gray-200">
                {{ booking.pickup_location }} &rarr; {{ booking.drop_location }}
              </span>
            </div>
            <div class="col-span-2 flex items-center justify-between pt-1 border-t border-gray-200/50 dark:border-gray-700/50">
              <span class="text-gray-500 font-medium">Total Amount:</span>
              <span class="font-black text-emerald-600 text-sm">${{ booking.total_price }}</span>
            </div>
          </div>

          <!-- Chauffeur & Owner Info -->
          <div class="p-3 rounded-2xl bg-blue-50/50 dark:bg-blue-950/20 text-xs flex items-center justify-between text-gray-600 dark:text-gray-300">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center text-sm">
                <i class="ri-user-star-line" />
              </div>
              <div>
                <span class="font-semibold block text-gray-900 dark:text-white">
                  {{ booking.car?.driver?.name || booking.car?.driver_name || 'Designated Chauffeur' }}
                </span>
                <span class="text-[10px] text-gray-500">
                  Tel: {{ booking.car?.driver?.phone || 'Confidential' }}
                </span>
              </div>
            </div>
            <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 px-2 py-1 rounded-lg border border-blue-100 dark:border-blue-900">
              Owner: {{ booking.car?.owner?.full_name || 'Fleet Partner' }}
            </span>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-2 pt-1">
            <button
              v-if="booking.status !== 'cancel' && booking.status !== 'canceled'"
              type="button"
              :disabled="cancellingId === booking.id"
              class="px-3.5 py-1.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold transition-colors disabled:opacity-50 flex items-center gap-1.5"
              @click="handleCancel(booking.id)"
            >
              <i class="ri-close-circle-line" />
              <span>{{ cancellingId === booking.id ? 'Canceling...' : 'Cancel Reservation' }}</span>
            </button>
            <Link
              :href="`/cars/${booking.car_id}`"
              class="px-3.5 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-semibold transition-colors flex items-center gap-1.5"
            >
              <i class="ri-car-line" />
              <span>View Vehicle</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="p-16 text-center bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4"
      >
        <div class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 mx-auto flex items-center justify-center text-3xl">
          <i class="ri-calendar-event-line" />
        </div>
        <div class="space-y-1">
          <h3 class="text-base font-bold text-gray-900 dark:text-white">
            No rental reservations found
          </h3>
          <p class="text-xs text-gray-500 max-w-sm mx-auto">
            {{ search ? 'No bookings match your search filters.' : 'You haven\'t booked any cars yet. Explore our verified fleet of vehicles with certified drivers and transparent pricing.' }}
          </p>
        </div>
        <Link
          href="/cars"
          class="inline-block px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all"
        >
          Explore Available Cars &rarr;
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
