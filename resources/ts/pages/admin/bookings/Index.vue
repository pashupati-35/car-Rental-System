<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { BookingItem } from './types'
import BookingTable from './components/BookingTable.vue'
import BookingDetailModal from './components/BookingDetailModal.vue'

const props = defineProps<{
  bookedCars: any
  counts?: {
    all?: number
    pending?: number
    confirm?: number
    cancel?: number
  }
  filters?: {
    status?: string
    search?: string
    per_page?: number
  }
}>()

const activeTab = ref<'all' | 'confirm' | 'pending' | 'cancel'>(
  (props.filters?.status as any) || 'all',
)

const searchQuery = ref(props.filters?.search || '')
const selectedBooking = ref<BookingItem | null>(null)
const showDetailModal = ref(false)

const bookingsList = computed<BookingItem[]>(() => {
  if (Array.isArray(props.bookedCars)) return props.bookedCars
  
  return props.bookedCars?.data || []
})

let searchTimeout: any = null

const applyFilters = () => {
  router.get('/admin/booked-cars', {
    status: activeTab.value === 'all' ? undefined : activeTab.value,
    search: searchQuery.value || undefined,
    per_page: props.filters?.per_page || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const onSearchInput = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 350)
}

const setTab = (tab: 'all' | 'confirm' | 'pending' | 'cancel') => {
  activeTab.value = tab
  applyFilters()
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
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setTab('all')"
          >
            <span>All Bookings</span>
            <span
              v-if="props.counts?.all !== undefined"
              :class="activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200/70 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
            >
              {{ props.counts.all }}
            </span>
          </button>
          <button
            type="button"
            :class="activeTab === 'pending' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setTab('pending')"
          >
            <span>Pending Orders</span>
            <span
              v-if="props.counts?.pending !== undefined"
              :class="activeTab === 'pending' ? 'bg-white/25 text-white' : 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300'"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
            >
              {{ props.counts.pending }}
            </span>
          </button>
          <button
            type="button"
            :class="activeTab === 'confirm' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setTab('confirm')"
          >
            <span>Confirmed & Active</span>
            <span
              v-if="props.counts?.confirm !== undefined"
              :class="activeTab === 'confirm' ? 'bg-white/20 text-white' : 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300'"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
            >
              {{ props.counts.confirm }}
            </span>
          </button>
          <button
            type="button"
            :class="activeTab === 'cancel' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setTab('cancel')"
          >
            <span>Cancelled</span>
            <span
              v-if="props.counts?.cancel !== undefined"
              :class="activeTab === 'cancel' ? 'bg-white/20 text-white' : 'bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300'"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
            >
              {{ props.counts.cancel }}
            </span>
          </button>
        </div>

        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by order #, client, car..."
            class="w-full py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-400 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            style="padding-left: 2rem; padding-right: 0.75rem"
            @input="onSearchInput"
            @keyup.enter="applyFilters"
          >
        </div>
      </div>

      <!-- Main Display Table -->
      <BookingTable
        :bookings="bookingsList"
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
