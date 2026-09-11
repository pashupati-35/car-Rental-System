<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'

const props = defineProps<{
  bookings: {
    data: Array<any>
    current_page: number
    last_page: number
    total: number
    links: Array<{ url: string | null; label: string; active: boolean }>
  }
  statusCounts?: {
    all: number
    pending: number
    confirmed: number
    completed: number
    cancelled: number
  }
  filters?: {
    status?: string
    search?: string
  }
}>()

const search = ref(props.filters?.search || '')
const currentStatus = ref(props.filters?.status || 'all')

const applyFilters = () => {
  router.get(
    '/owner/bookings',
    {
      search: search.value || undefined,
      status: currentStatus.value !== 'all' ? currentStatus.value : undefined,
    },
    {
      preserveState: true,
      replace: true,
    },
  )
}

let debounceTimeout: any = null
watch(search, () => {
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    applyFilters()
  }, 350)
})

const setStatus = (st: string) => {
  currentStatus.value = st
  applyFilters()
}

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
</script>

<template>
  <OwnerLayout>
    <Head title="Fleet Bookings & Reservations" />

    <div class="space-y-6 max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
            Vehicle Bookings & Reservations
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Manage customer reservation requests for your fleet vehicles and track rental payouts.
          </p>
        </div>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <!-- Status Tabs with Counts -->
        <div class="flex flex-wrap items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl">
          <button
            type="button"
            :class="currentStatus === 'all' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('all')"
          >
            <span>All Bookings</span>
            <span
              :class="currentStatus === 'all' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.all ?? bookings.total }}
            </span>
          </button>
          <button
            type="button"
            :class="currentStatus === 'pending' ? 'bg-white dark:bg-slate-900 text-amber-700 dark:text-amber-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('pending')"
          >
            <span>Pending</span>
            <span
              :class="currentStatus === 'pending' ? 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.pending ?? 0 }}
            </span>
          </button>
          <button
            type="button"
            :class="currentStatus === 'confirmed' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('confirmed')"
          >
            <span>Confirmed</span>
            <span
              :class="currentStatus === 'confirmed' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.confirmed ?? 0 }}
            </span>
          </button>
          <button
            type="button"
            :class="currentStatus === 'completed' ? 'bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('completed')"
          >
            <span>Completed</span>
            <span
              :class="currentStatus === 'completed' ? 'bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.completed ?? 0 }}
            </span>
          </button>
          <button
            type="button"
            :class="currentStatus === 'cancelled' ? 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('cancelled')"
          >
            <span>Cancelled</span>
            <span
              :class="currentStatus === 'cancelled' ? 'bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.cancelled ?? 0 }}
            </span>
          </button>
        </div>

        <!-- Search Input -->
        <div class="relative min-w-[240px] md:w-72">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm" />
          <input
            v-model="search"
            type="text"
            placeholder="Search car, customer, pickup..."
            class="w-full ps-9 pe-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
          >
        </div>
      </div>

      <!-- Bookings Table / List -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div
          v-if="bookings?.data && bookings.data.length > 0"
          class="overflow-x-auto"
        >
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 text-slate-400 uppercase tracking-wider font-extrabold text-[10px]">
                <th class="py-3.5 px-5">
                  Reservation
                </th>
                <th class="py-3.5 px-5">
                  Vehicle
                </th>
                <th class="py-3.5 px-5">
                  Customer
                </th>
                <th class="py-3.5 px-5">
                  Trip Dates & Route
                </th>
                <th class="py-3.5 px-5">
                  Total Amount
                </th>
                <th class="py-3.5 px-5">
                  Status
                </th>
                <th class="py-3.5 px-5 text-right">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
              <tr
                v-for="b in bookings.data"
                :key="b.id"
                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors"
              >
                <!-- Booking ID & Time -->
                <td class="py-4 px-5">
                  <span class="font-mono font-bold text-slate-900 dark:text-white">#BK-{{ b.id }}</span>
                  <span class="block text-[10px] text-slate-400 mt-0.5">{{ new Date(b.created_at).toLocaleDateString() }}</span>
                </td>

                <!-- Vehicle Info -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <img
                      :src="b.car?.car_photo ? '/' + b.car.car_photo : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=100&auto=format&fit=crop&q=80'"
                      class="w-10 h-10 object-cover rounded-xl shrink-0"
                    >
                    <div>
                      <span class="font-bold text-slate-900 dark:text-white block">{{ b.car?.car_name }} {{ b.car?.car_model }}</span>
                      <span class="font-mono text-[11px] text-slate-400">{{ b.car?.car_number }}</span>
                    </div>
                  </div>
                </td>

                <!-- Customer Details -->
                <td class="py-4 px-5">
                  <span class="font-semibold text-slate-900 dark:text-white block">{{ b.customer?.full_name || b.customer?.name || 'Customer' }}</span>
                  <span class="text-[11px] text-slate-400 block">{{ b.customer?.email }}</span>
                  <span
                    v-if="b.customer?.contact_number || b.customer?.mobile"
                    class="font-mono text-[10px] text-emerald-600 dark:text-emerald-400 block"
                  >
                    📞 {{ b.customer?.contact_number || b.customer?.mobile }}
                  </span>
                </td>

                <!-- Trip Dates -->
                <td class="py-4 px-5 space-y-1">
                  <div class="flex items-center gap-1.5 text-slate-800 dark:text-slate-200">
                    <i class="ri-calendar-event-line text-emerald-600 text-xs" />
                    <span>{{ b.start_date || b.pickup_date || 'N/A' }} &rarr; {{ b.end_date || b.drop_date || 'N/A' }}</span>
                  </div>
                  <div
                    v-if="b.pickup_location || b.drop_location"
                    class="text-[11px] text-slate-400 flex items-center gap-1"
                  >
                    <i class="ri-map-pin-line text-xs" />
                    <span>{{ b.pickup_location || 'Pickup' }} &bull; {{ b.drop_location || 'Drop' }}</span>
                  </div>
                </td>

                <!-- Pricing -->
                <td class="py-4 px-5">
                  <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 block">
                    ${{ b.total_price || b.amount || 0 }}
                  </span>
                  <span
                    v-if="b.payment?.status"
                    class="text-[10px] uppercase font-bold text-slate-400"
                  >
                    Paid ({{ b.payment.status }})
                  </span>
                </td>

                <!-- Status Badge -->
                <td class="py-4 px-5">
                  <span
                    :class="{
                      'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800': b.status === 'confirmed',
                      'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800': b.status === 'completed',
                      'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800': b.status === 'pending',
                      'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800': b.status === 'cancelled',
                    }"
                    class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border"
                  >
                    {{ b.status }}
                  </span>
                </td>

                <!-- Action Buttons -->
                <td class="py-4 px-5 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      v-if="b.status === 'pending'"
                      type="button"
                      class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs transition-all cursor-pointer"
                      @click="confirmBooking(b.id)"
                    >
                      Confirm
                    </button>
                    <button
                      v-if="b.status === 'pending' || b.status === 'confirmed'"
                      type="button"
                      class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 text-[11px] font-bold transition-all cursor-pointer"
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

        <!-- Empty State -->
        <div
          v-else
          class="p-12 text-center space-y-3"
        >
          <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-2xl mx-auto">
            <i class="ri-calendar-check-line" />
          </div>
          <h4 class="font-bold text-base text-slate-900 dark:text-white">
            No Bookings Found
          </h4>
          <p class="text-xs text-slate-500 max-w-sm mx-auto">
            {{ search || currentStatus !== 'all' ? 'No reservations match your current filters.' : 'There are currently no customer reservations for your fleet vehicles.' }}
          </p>
        </div>

        <!-- Pagination -->
        <div
          v-if="bookings?.links && bookings.links.length > 3"
          class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-center gap-1.5"
        >
          <Link
            v-for="(link, i) in bookings.links"
            :key="i"
            :href="link.url || '#'"
            :class="[
              link.active
                ? 'bg-emerald-600 text-white font-bold'
                : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800',
              !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'cursor-pointer'
            ]"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold border border-slate-200/80 dark:border-slate-800 transition-all"
          >
            <span v-html="link.label" />
          </Link>
        </div>
      </div>
    </div>
  </OwnerLayout>
</template>
