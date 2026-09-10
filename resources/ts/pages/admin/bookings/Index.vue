<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { BookingItem } from './types'
import BookingTable from './components/BookingTable.vue'
import BookingDetailModal from './components/BookingDetailModal.vue'

const props = defineProps<{
  bookedCars: any
  filters?: {
    status?: string
    search?: string
    per_page?: number
  }
}>()

const activeTab = ref<'all' | 'confirm' | 'pending' | 'cancel'>(
  (props.filters?.status as any) || 'all'
)
const searchQuery = ref(props.filters?.search || '')
const selectedBooking = ref<BookingItem | null>(null)
const showDetailModal = ref(false)

const bookingsList = computed<BookingItem[]>(() => {
  if (Array.isArray(props.bookedCars)) return props.bookedCars
  return props.bookedCars?.data || []
})

const filteredBookings = computed<BookingItem[]>(() => {
  let list = bookingsList.value

  if (activeTab.value !== 'all' && !props.filters?.status) {
    list = list.filter((b: BookingItem) => {
      if (activeTab.value === 'confirm') return b.status === 'confirm' || b.status === 'confirmed' || b.status === 'completed'
      if (activeTab.value === 'pending') return b.status === 'pending'
      if (activeTab.value === 'cancel') return b.status === 'cancel' || b.status === 'cancelled'
      return true
    })
  }

  if (searchQuery.value.trim() && !props.filters?.search) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter((b: BookingItem) => {
      const id = String(b.id)
      const cust = b.customer?.name || b.customer?.full_name || ''
      const car = (b.car?.car_name || b.car?.brand || '') + ' ' + (b.car?.car_model || b.car?.model || '')
      return id.includes(q) || cust.toLowerCase().includes(q) || car.toLowerCase().includes(q)
    })
  }

  return list
})

const setTab = (tab: 'all' | 'confirm' | 'pending' | 'cancel') => {
  activeTab.value = tab
  router.get('/admin/booked-cars', {
    status: tab === 'all' ? '' : tab,
    search: searchQuery.value,
  }, { preserveState: true, preserveScroll: true })
}

const handleSearch = () => {
  router.get('/admin/booked-cars', {
    status: activeTab.value === 'all' ? '' : activeTab.value,
    search: searchQuery.value,
  }, { preserveState: true, preserveScroll: true })
}

const confirmBooking = (id: number) => {
  router.post(`/admin/bookings/${id}/confirm`, {}, {
    preserveScroll: true,
  })
}

const cancelBooking = (id: number) => {
  router.post(`/admin/bookings/${id}/cancel`, {}, {
    preserveScroll: true,
  })
}

const deleteBooking = (id: number) => {
  if (!confirm('Are you sure you want to permanently delete this rental booking order?')) return
  router.delete(`/admin/bookings/${id}`, {
    preserveScroll: true,
  })
}

const openDetails = (booking: BookingItem) => {
  selectedBooking.value = booking
  showDetailModal.value = true
}
</script>

<template>
  <AdminLayout>
    <Head title="Rental Bookings & Orders - Admin Portal" />

    <div class="space-y-6">
      <!-- Title Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
              Operations & Orders
            </span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
              <i class="ri-calendar-check-line text-indigo-600" />
              Car Rental Orders & Bookings
            </h2>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            Monitor client rental schedules, approve pending reservation inquiries, and track fleet turnover.
          </p>
        </div>
      </div>

      <!-- Filter Tabs & Search Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
          <button
            type="button"
            :class="activeTab === 'all' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all"
            @click="setTab('all')"
          >
            All Bookings
          </button>
          <button
            type="button"
            :class="activeTab === 'pending' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all"
            @click="setTab('pending')"
          >
            Pending Orders
          </button>
          <button
            type="button"
            :class="activeTab === 'confirm' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all"
            @click="setTab('confirm')"
          >
            Confirmed & Active
          </button>
          <button
            type="button"
            :class="activeTab === 'cancel' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all"
            @click="setTab('cancel')"
          >
            Cancelled
          </button>
        </div>

        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by order #, client, car..."
            class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            @keyup.enter="handleSearch"
          />
        </div>
      </div>

      <!-- Main Display Table -->
      <BookingTable
        :bookings="filteredBookings"
        :pagination="props.bookedCars"
        @view="openDetails"
        @confirm="confirmBooking"
        @cancel="cancelBooking"
        @delete="deleteBooking"
      />

      <!-- Inspect Booking Modal -->
      <BookingDetailModal
        :show="showDetailModal"
        :booking="selectedBooking"
        @close="showDetailModal = false"
        @confirm="confirmBooking"
        @cancel="cancelBooking"
        @delete="deleteBooking"
      />
    </div>
  </AdminLayout>
</template>
