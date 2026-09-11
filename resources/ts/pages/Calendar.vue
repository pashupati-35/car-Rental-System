<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

interface CarItem {
  id: number
  car_name: string
  brand?: string
  car_model?: string
  car_number?: string
  car_price_per_day?: number
  image?: string
}

interface CustomerItem {
  id?: number
  name?: string
  email?: string
  contact_number?: string
}

interface BookingItem {
  id: number
  booking_id?: string
  car_id: number
  customer_id?: number
  name?: string
  pick_up_date: string
  last_date: string
  status: string
  total_price?: number
  car?: CarItem
  customer?: CustomerItem
}

const props = defineProps<{
  carId?: number | null
  cars?: CarItem[]
  bookings?: BookingItem[]
}>()

const selectedCarId = ref<number | ''>(props.carId || '')
const currentDate = ref(new Date())
const selectedDateDetails = ref<{ date: string; bookings: BookingItem[] } | null>(null)
const isModalOpen = ref(false)

const year = computed(() => currentDate.value.getFullYear())
const month = computed(() => currentDate.value.getMonth())

const monthNames = [
  'January',
  'February',
  'March',
  'April',
  'May',
  'June',
  'July',
  'August',
  'September',
  'October',
  'November',
  'December',
]

const currentMonthLabel = computed(() => `${monthNames[month.value]} ${year.value}`)

const prevMonth = () => {
  currentDate.value = new Date(year.value, month.value - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(year.value, month.value + 1, 1)
}

const goToToday = () => {
  currentDate.value = new Date()
}

const onCarFilterChange = () => {
  if (selectedCarId.value) {
    router.get(`/car-calendar/${selectedCarId.value}`, {}, { preserveState: true, preserveScroll: true })
  } else {
    router.get('/car-calendar', {}, { preserveState: true, preserveScroll: true })
  }
}

// Filtered bookings based on selected vehicle
const filteredBookings = computed<BookingItem[]>(() => {
  if (!props.bookings) return []
  if (!selectedCarId.value) return props.bookings
  
  return props.bookings.filter((b: BookingItem) => b.car_id === Number(selectedCarId.value))
})

// Build the calendar matrix
interface CalendarDay {
  date: Date
  dateString: string
  dayNumber: number
  isCurrentMonth: boolean
  isToday: boolean
  bookings: BookingItem[]
}

const calendarDays = computed(() => {
  const days: CalendarDay[] = []
  const firstDayOfMonth = new Date(year.value, month.value, 1)
  const lastDayOfMonth = new Date(year.value, month.value + 1, 0)
  
  const startingDayOfWeek = firstDayOfMonth.getDay() // 0 = Sun, 1 = Mon ...
  const totalDaysInMonth = lastDayOfMonth.getDate()

  const todayStr = new Date().toISOString().split('T')[0]

  // Previous month overflow days
  const prevMonthLastDay = new Date(year.value, month.value, 0).getDate()
  for (let i = startingDayOfWeek - 1; i >= 0; i--) {
    const dayNum = prevMonthLastDay - i
    const d = new Date(year.value, month.value - 1, dayNum)
    const dateStr = d.toISOString().split('T')[0]

    days.push({
      date: d,
      dateString: dateStr,
      dayNumber: dayNum,
      isCurrentMonth: false,
      isToday: dateStr === todayStr,
      bookings: getBookingsForDate(dateStr),
    })
  }

  // Current month days
  for (let dayNum = 1; dayNum <= totalDaysInMonth; dayNum++) {
    const d = new Date(year.value, month.value, dayNum)
    const y = d.getFullYear()
    const m = String(d.getMonth() + 1).padStart(2, '0')
    const dayPadded = String(dayNum).padStart(2, '0')
    const dateStr = `${y}-${m}-${dayPadded}`

    days.push({
      date: d,
      dateString: dateStr,
      dayNumber: dayNum,
      isCurrentMonth: true,
      isToday: dateStr === todayStr,
      bookings: getBookingsForDate(dateStr),
    })
  }

  // Next month overflow days to fill 35 or 42 grid slots
  const remainingSlots = (7 - (days.length % 7)) % 7
  for (let i = 1; i <= remainingSlots; i++) {
    const d = new Date(year.value, month.value + 1, i)
    const dateStr = d.toISOString().split('T')[0]

    days.push({
      date: d,
      dateString: dateStr,
      dayNumber: i,
      isCurrentMonth: false,
      isToday: dateStr === todayStr,
      bookings: getBookingsForDate(dateStr),
    })
  }

  return days
})

const getBookingsForDate = (dateStr: string): BookingItem[] => {
  return filteredBookings.value.filter((b: BookingItem) => {
    const start = b.pick_up_date ? b.pick_up_date.split(' ')[0] : ''
    const end = b.last_date ? b.last_date.split(' ')[0] : ''
    
    return dateStr >= start && dateStr <= end
  })
}

const openDayModal = (day: CalendarDay) => {
  selectedDateDetails.value = {
    date: day.dateString,
    bookings: day.bookings,
  }
  isModalOpen.value = true
}

const getStatusBadgeClass = (status: string) => {
  const s = status.toLowerCase()
  if (s === 'confirm' || s === 'confirmed') {
    return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-300/50'
  }
  if (s === 'reserved' || s === 'booked') {
    return 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/80 dark:text-indigo-300 border-indigo-300/50'
  }
  if (s === 'pending') {
    return 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-300/50'
  }
  
  return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-300/50'
}

const thisMonthBookingsCount = computed(() => {
  const m = String(month.value + 1).padStart(2, '0')
  const ym = `${year.value}-${m}`
  
  return filteredBookings.value.filter((b: BookingItem) => (b.pick_up_date && b.pick_up_date.startsWith(ym)) || (b.last_date && b.last_date.startsWith(ym))).length
})
</script>

<template>
  <FrontendLayout>
    <Head title="Vehicle Calendar & Availability Timetable" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      <!-- Hero Banner -->
      <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl space-y-3">
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold uppercase tracking-wider text-blue-100 inline-block">
              Zero-Conflict Fleet Schedule
            </span>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 text-xs font-bold border border-emerald-400/30 flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
              Live Sync
            </span>
          </div>
          <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
            Fleet Availability & Reservation Calendar
          </h1>
          <p class="text-sm text-blue-100/90 leading-relaxed">
            Inspect live reservations across all verified vehicles. Prevent booking overlaps with realtime date availability.
          </p>
        </div>

        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-indigo-400/20 rounded-full blur-3xl pointer-events-none" />
      </div>

      <!-- Quick Metrics Ribbon -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl font-bold">
            <i class="ri-car-line" />
          </div>
          <div>
            <span class="text-xs text-slate-500 block font-medium">Verified Vehicles</span>
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ props.cars?.length || 0 }}</span>
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl font-bold">
            <i class="ri-calendar-check-line" />
          </div>
          <div>
            <span class="text-xs text-slate-500 block font-medium">Bookings This Month</span>
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ thisMonthBookingsCount }}</span>
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl font-bold">
            <i class="ri-shield-check-line" />
          </div>
          <div>
            <span class="text-xs text-slate-500 block font-medium">Overlaps Protection</span>
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full inline-block mt-0.5">
              100% Guaranteed
            </span>
          </div>
        </div>
      </div>

      <!-- Calendar Controls & Filter Toolbar -->
      <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row gap-4 items-center justify-between">
        <!-- Month Navigator -->
        <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-start">
          <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl">
            <button
              type="button"
              class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 shadow-xs transition-all cursor-pointer"
              title="Previous Month"
              @click="prevMonth"
            >
              <i class="ri-arrow-left-s-line text-lg" />
            </button>

            <button
              type="button"
              class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 transition-all cursor-pointer"
              @click="goToToday"
            >
              Today
            </button>

            <button
              type="button"
              class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 shadow-xs transition-all cursor-pointer"
              title="Next Month"
              @click="nextMonth"
            >
              <i class="ri-arrow-right-s-line text-lg" />
            </button>
          </div>

          <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white min-w-[160px]">
            {{ currentMonthLabel }}
          </h2>
        </div>

        <!-- Vehicle Filter & Status Legend -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-between md:justify-end">
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-500">Filter Car:</span>
            <select
              v-model="selectedCarId"
              class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="onCarFilterChange"
            >
              <option value="">
                All Fleet Cars ({{ props.cars?.length || 0 }})
              </option>
              <option
                v-for="car in props.cars"
                :key="car.id"
                :value="car.id"
              >
                {{ car.car_name || car.brand }} {{ car.car_model }} ({{ car.car_number }})
              </option>
            </select>
          </div>

          <!-- Color Legend -->
          <div
            class="hidden xl:flex items-center gap-3 text-[11px] font-semibold text-slate-500 border-l border-slate-200 dark:border-slate-800"
            style="padding-left: 0.75rem"
          >
            <span class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" /> Confirmed
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-indigo-500" /> Booked/Reserved
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-amber-500" /> Pending
            </span>
          </div>
        </div>
      </div>

      <!-- Calendar Grid Matrix -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Weekday Headers -->
        <div class="grid grid-cols-7 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-center py-3 text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
          <div class="text-rose-500">
            Sun
          </div>
          <div>Mon</div>
          <div>Tue</div>
          <div>Wed</div>
          <div>Thu</div>
          <div>Fri</div>
          <div class="text-indigo-500">
            Sat
          </div>
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 divide-x divide-y divide-slate-100 dark:divide-slate-800">
          <div
            v-for="day in calendarDays"
            :key="day.dateString"
            class="min-h-[110px] sm:min-h-[130px] p-2 sm:p-2.5 flex flex-col justify-between transition-colors hover:bg-slate-50/70 dark:hover:bg-slate-800/40 cursor-pointer"
            :class="{
              'bg-slate-50/40 dark:bg-slate-950/40 text-slate-400 dark:text-slate-600': !day.isCurrentMonth,
              'bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200': day.isCurrentMonth,
            }"
            @click="openDayModal(day)"
          >
            <!-- Day Header (Date & Today Badge) -->
            <div class="flex items-center justify-between">
              <span
                class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                :class="{
                  'bg-blue-600 text-white font-black shadow-md shadow-blue-500/30': day.isToday,
                  'text-slate-900 dark:text-white font-extrabold': day.isCurrentMonth && !day.isToday,
                  'text-slate-400 dark:text-slate-600': !day.isCurrentMonth,
                }"
              >
                {{ day.dayNumber }}
              </span>

              <span
                v-if="day.bookings.length > 0"
                class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300"
              >
                {{ day.bookings.length }} {{ day.bookings.length === 1 ? 'Booking' : 'Bookings' }}
              </span>
            </div>

            <!-- Booking Badges in Cell -->
            <div class="space-y-1 my-1 overflow-hidden">
              <div
                v-for="b in day.bookings.slice(0, 2)"
                :key="b.id"
                class="px-1.5 py-1 rounded-lg text-[10px] font-bold truncate border flex items-center justify-between gap-1"
                :class="getStatusBadgeClass(b.status)"
                :title="`${b.car?.car_name || 'Vehicle'} - ${b.customer?.name || b.name || 'Customer'}`"
              >
                <span class="truncate">{{ b.car?.car_name || b.car?.brand || 'Car' }}</span>
                <span class="text-[8px] uppercase font-mono opacity-80 shrink-0">{{ b.status }}</span>
              </div>

              <div
                v-if="day.bookings.length > 2"
                class="text-[9px] font-bold text-slate-500 text-center"
              >
                +{{ day.bookings.length - 2 }} more
              </div>
            </div>

            <!-- Bottom status dot if available -->
            <div
              v-if="day.bookings.length === 0 && day.isCurrentMonth"
              class="text-[10px] text-slate-400 font-medium flex items-center gap-1 opacity-0 hover:opacity-100 sm:opacity-60 transition-opacity"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400" />
              <span>Available</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Schedule Modal / Drawer for Selected Date -->
      <div
        v-if="isModalOpen && selectedDateDetails"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-150"
      >
        <div
          class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-5 overflow-hidden"
          @click.stop
        >
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 text-[10px] font-extrabold uppercase">
                  Schedule Inspector
                </span>
                <span class="text-xs text-slate-500 font-mono">{{ selectedDateDetails.date }}</span>
              </div>
              <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Bookings for {{ selectedDateDetails.date }}
              </h3>
            </div>
            <button
              type="button"
              class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
              @click="isModalOpen = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <!-- Bookings list on this date -->
          <div
            class="space-y-3 max-h-96 overflow-y-auto"
            style="padding-right: 0.25rem"
          >
            <div
              v-if="selectedDateDetails.bookings.length === 0"
              class="p-8 text-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl space-y-2"
            >
              <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-400 flex items-center justify-center text-xl mx-auto">
                <i class="ri-check-line" />
              </div>
              <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">
                All Vehicles Available
              </h4>
              <p class="text-xs text-slate-500">
                There are no scheduled reservations on this date. You can book any vehicle from our fleet catalog.
              </p>
              <Link
                href="/cars"
                class="inline-block mt-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition-all"
              >
                Browse Fleet Cars &rarr;
              </Link>
            </div>

            <div
              v-for="b in selectedDateDetails.bookings"
              :key="b.id"
              class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-3"
            >
              <div class="flex items-start justify-between gap-3">
                <div>
                  <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">
                    {{ b.car?.car_name || b.car?.brand }} {{ b.car?.car_model }}
                  </h4>
                  <p class="text-[11px] font-mono text-slate-500">
                    Plate: {{ b.car?.car_number || 'N/A' }}
                  </p>
                </div>

                <span
                  :class="getStatusBadgeClass(b.status)"
                  class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border shrink-0"
                >
                  {{ b.status }}
                </span>
              </div>

              <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                <div>
                  <span class="text-[10px] text-slate-400 block font-semibold">Customer</span>
                  <span class="font-bold text-slate-800 dark:text-slate-200 truncate block">
                    {{ b.customer?.name || b.name || 'Customer' }}
                  </span>
                </div>
                <div>
                  <span class="text-[10px] text-slate-400 block font-semibold">Rental Price</span>
                  <span class="font-bold text-emerald-600 dark:text-emerald-400">
                    ${{ b.total_price || 0 }}
                  </span>
                </div>
                <div class="col-span-2">
                  <span class="text-[10px] text-slate-400 block font-semibold">Reservation Period</span>
                  <span class="font-mono text-[11px] text-slate-700 dark:text-slate-300">
                    {{ b.pick_up_date }} &rarr; {{ b.last_date }}
                  </span>
                </div>
              </div>

              <div class="pt-1 flex items-center justify-end">
                <Link
                  :href="`/cars/${b.car_id}`"
                  class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1"
                >
                  <span>Inspect Vehicle Details</span>
                  <i class="ri-arrow-right-line" />
                </Link>
              </div>
            </div>
          </div>

          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-end">
            <button
              type="button"
              class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer"
              @click="isModalOpen = false"
            >
              Close Inspector
            </button>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>
