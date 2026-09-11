<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'

interface CarItem {
  id: number
  car_name: string
  car_model?: string
  car_number?: string
  car_price_per_day?: number
  car_photo?: string
  status?: string
}

interface CustomerItem {
  id?: number
  name?: string
  first_name?: string
  last_name?: string
  email?: string
  mobile?: string
  phone_number?: string
}

interface BookingItem {
  id: number
  booking_id?: string
  car_id: number
  customer_id?: number
  name?: string
  pickup_date?: string
  return_date?: string
  pick_up_date?: string
  last_date?: string
  start_date?: string
  end_date?: string
  status: string
  total_price?: number
  amount?: number
  car?: CarItem
  customer?: CustomerItem
}

const props = defineProps<{
  carId?: number | null
  cars?: CarItem[]
  bookings?: BookingItem[]
  stats?: {
    totalCars: number
    thisMonthBookings: number
    activeBookings: number
  }
}>()

const selectedCarId = ref<number | ''>(props.carId || '')
const currentDate = ref(new Date())
const selectedBooking = ref<BookingItem | null>(null)
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
    router.get(`/owner/calendar/${selectedCarId.value}`, {}, { preserveState: true, preserveScroll: true })
  } else {
    router.get('/owner/calendar', {}, { preserveState: true, preserveScroll: true })
  }
}

// Filtered bookings based on selected vehicle
const filteredBookings = computed<BookingItem[]>(() => {
  if (!props.bookings) return []
  if (!selectedCarId.value) return props.bookings
  
  return props.bookings.filter((b: BookingItem) => b.car_id === Number(selectedCarId.value))
})

// Build calendar matrix
interface CalendarDay {
  date: Date
  dateString: string
  dayNumber: number
  isCurrentMonth: boolean
  isToday: boolean
  bookings: BookingItem[]
}

const calendarMatrix = computed<CalendarDay[]>(() => {
  const days: CalendarDay[] = []
  const firstDayOfMonth = new Date(year.value, month.value, 1)
  const lastDayOfMonth = new Date(year.value, month.value + 1, 0)

  const startDayOfWeek = firstDayOfMonth.getDay() // 0 = Sun
  const totalDays = lastDayOfMonth.getDate()

  const today = new Date()
  const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`

  // Helper date normalize
  const normalizeDateStr = (d: Date) => {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
  }

  // Days from previous month
  const prevMonthLastDay = new Date(year.value, month.value, 0).getDate()
  for (let i = startDayOfWeek - 1; i >= 0; i--) {
    const d = new Date(year.value, month.value - 1, prevMonthLastDay - i)
    const dateString = normalizeDateStr(d)
    days.push({
      date: d,
      dateString,
      dayNumber: d.getDate(),
      isCurrentMonth: false,
      isToday: dateString === todayStr,
      bookings: getBookingsForDate(dateString),
    })
  }

  // Current month days
  for (let i = 1; i <= totalDays; i++) {
    const d = new Date(year.value, month.value, i)
    const dateString = normalizeDateStr(d)
    days.push({
      date: d,
      dateString,
      dayNumber: i,
      isCurrentMonth: true,
      isToday: dateString === todayStr,
      bookings: getBookingsForDate(dateString),
    })
  }

  // Days from next month to complete the grid (up to 35 or 42 cells)
  const remainingDays = 42 - days.length
  if (remainingDays < 7) {
    for (let i = 1; i <= remainingDays; i++) {
      const d = new Date(year.value, month.value + 1, i)
      const dateString = normalizeDateStr(d)
      days.push({
        date: d,
        dateString,
        dayNumber: i,
        isCurrentMonth: false,
        isToday: dateString === todayStr,
        bookings: getBookingsForDate(dateString),
      })
    }
  }

  return days
})

const getBookingsForDate = (dateStr: string): BookingItem[] => {
  return filteredBookings.value.filter((b: BookingItem) => {
    const start = b.pickup_date || b.pick_up_date || b.start_date || ''
    const end = b.return_date || b.last_date || b.end_date || ''
    if (!start || !end) return false
    return dateStr >= start.split('T')[0] && dateStr <= end.split('T')[0]
  })
}

const openDateDetails = (day: CalendarDay) => {
  if (day.bookings.length > 0) {
    selectedDateDetails.value = {
      date: day.dateString,
      bookings: day.bookings,
    }
    selectedBooking.value = day.bookings[0]
    isModalOpen.value = true
  }
}

const openBookingDetail = (booking: BookingItem) => {
  selectedBooking.value = booking
  selectedDateDetails.value = null
  isModalOpen.value = true
}

const getCustomerName = (booking: BookingItem) => {
  if (booking.customer) {
    return booking.customer.name || `${booking.customer.first_name || ''} ${booking.customer.last_name || ''}`.trim() || 'Customer'
  }
  return booking.name || 'Customer'
}
</script>

<template>
  <OwnerLayout>
    <Head title="Fleet Availability Calendar" />

    <div class="space-y-6 max-w-7xl mx-auto">
      <!-- Hero Banner -->
      <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-emerald-700 via-teal-700 to-slate-900 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 relative z-10 max-w-2xl">
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-white/20 text-emerald-100 text-[11px] font-black uppercase tracking-wider">
              Fleet Scheduling & Schedule Matrix
            </span>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/80 text-white text-[10px] font-bold">
              Owner Portal
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
            Fleet Availability & Reservation Calendar
          </h1>
          <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
            Inspect real-time schedules across your registered vehicles. Monitor bookings, chauffeur allocations, and prevent rental conflicts.
          </p>
        </div>

        <div class="flex flex-wrap gap-2.5 relative z-10">
          <Link
            href="/owner/cars/create"
            class="px-4 py-2.5 rounded-2xl bg-white text-emerald-950 font-bold text-xs shadow-md hover:bg-emerald-50 transition-all flex items-center gap-1.5"
          >
            <i class="ri-add-circle-line text-base text-emerald-700" />
            <span>List Vehicle</span>
          </Link>
          <Link
            href="/owner/bookings"
            class="px-4 py-2.5 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-1.5"
          >
            <i class="ri-calendar-check-line text-base" />
            <span>All Bookings</span>
          </Link>
        </div>
      </div>

      <!-- Quick KPI Counters -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">My Registered Fleet</span>
            <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ stats?.totalCars ?? cars?.length ?? 0 }}</h4>
          </div>
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
            <i class="ri-car-line" />
          </div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bookings This Month</span>
            <h4 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">{{ stats?.thisMonthBookings ?? 0 }}</h4>
          </div>
          <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xl">
            <i class="ri-calendar-event-line" />
          </div>
        </div>

        <div class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Reservations</span>
            <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ stats?.activeBookings ?? bookings?.length ?? 0 }}</h4>
          </div>
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
            <i class="ri-checkbox-circle-line" />
          </div>
        </div>
      </div>

      <!-- Calendar Controls & Filters Toolbar -->
      <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <!-- Month Navigation -->
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer"
            title="Previous Month"
            @click="prevMonth"
          >
            <i class="ri-arrow-left-s-line text-lg" />
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors cursor-pointer"
            @click="goToToday"
          >
            Today
          </button>
          <button
            type="button"
            class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer"
            title="Next Month"
            @click="nextMonth"
          >
            <i class="ri-arrow-right-s-line text-lg" />
          </button>
          <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white ms-2">
            {{ currentMonthLabel }}
          </h2>
        </div>

        <!-- Vehicle Selector & Status Legend -->
        <div class="flex flex-wrap items-center gap-3">
          <!-- Car Filter Dropdown -->
          <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400">Filter Vehicle:</span>
            <select
              v-model="selectedCarId"
              class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500"
              @change="onCarFilterChange"
            >
              <option value="">
                All My Fleet Cars ({{ cars?.length || 0 }})
              </option>
              <option
                v-for="car in cars"
                :key="car.id"
                :value="car.id"
              >
                {{ car.car_name }} {{ car.car_model }} ({{ car.car_number }})
              </option>
            </select>
          </div>

          <!-- Status Indicator Badges -->
          <div class="hidden sm:flex items-center gap-2 text-[11px] font-bold text-slate-500">
            <span class="flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-emerald-500" /> Confirmed
            </span>
            <span class="flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-amber-500" /> Pending
            </span>
            <span class="flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-blue-500" /> Completed
            </span>
          </div>
        </div>
      </div>

      <!-- Calendar Matrix Board -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <!-- Weekday Headers -->
        <div class="grid grid-cols-7 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 text-center text-[11px] font-black uppercase tracking-wider text-slate-400 py-3">
          <div>Sun</div>
          <div>Mon</div>
          <div>Tue</div>
          <div>Wed</div>
          <div>Thu</div>
          <div>Fri</div>
          <div>Sat</div>
        </div>

        <!-- 7-column Days Grid -->
        <div class="grid grid-cols-7 divide-x divide-y divide-slate-100 dark:divide-slate-800/80">
          <div
            v-for="(day, idx) in calendarMatrix"
            :key="idx"
            :class="[
              day.isCurrentMonth ? 'bg-white dark:bg-slate-900' : 'bg-slate-50/50 dark:bg-slate-950/40 text-slate-400',
              day.isToday ? 'ring-2 ring-emerald-500/40 inset-0' : '',
            ]"
            class="min-h-[110px] p-2 flex flex-col justify-between transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer"
            @click="openDateDetails(day)"
          >
            <!-- Day Number & Badge -->
            <div class="flex items-center justify-between">
              <span
                :class="[
                  day.isToday
                    ? 'w-6 h-6 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs shadow-xs'
                    : 'text-xs font-bold text-slate-700 dark:text-slate-300'
                ]"
              >
                {{ day.dayNumber }}
              </span>

              <span
                v-if="day.bookings.length > 0"
                class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
              >
                {{ day.bookings.length }} booking{{ day.bookings.length > 1 ? 's' : '' }}
              </span>
            </div>

            <!-- Booking Cards in Day Cell -->
            <div class="space-y-1 my-1 overflow-hidden">
              <div
                v-for="b in day.bookings.slice(0, 2)"
                :key="b.id"
                :class="{
                  'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-200 dark:border-emerald-800': b.status === 'confirmed',
                  'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-200 dark:border-amber-800': b.status === 'pending',
                  'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-200 dark:border-blue-800': b.status === 'completed',
                }"
                class="p-1 rounded-lg text-[10px] font-bold truncate border flex items-center justify-between gap-1 shadow-2xs"
                @click.stop="openBookingDetail(b)"
              >
                <span class="truncate">{{ b.car?.car_name || 'Car' }} &bull; {{ getCustomerName(b) }}</span>
                <span class="text-[9px] uppercase font-mono px-1 rounded bg-white/60 dark:bg-slate-900/60">{{ b.status }}</span>
              </div>

              <span
                v-if="day.bookings.length > 2"
                class="text-[9px] font-bold text-slate-400 block text-center"
              >
                +{{ day.bookings.length - 2 }} more
              </span>
            </div>

            <!-- Bottom Availability Indicator -->
            <div class="text-[10px] text-slate-400 font-medium">
              <span
                v-if="day.bookings.length === 0 && day.isCurrentMonth"
                class="text-emerald-600/70 dark:text-emerald-400/70 text-[10px]"
              >
                ● Available
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Booking Details Modal -->
    <div
      v-if="isModalOpen && selectedBooking"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/75 backdrop-blur-sm p-4 overflow-y-auto"
    >
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl max-w-md w-full p-6 sm:p-8 space-y-5 relative">
        <button
          type="button"
          class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold cursor-pointer"
          @click="isModalOpen = false"
        >
          &times;
        </button>

        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl font-black shadow-xs">
            <i class="ri-car-line" />
          </div>
          <div>
            <h3 class="text-lg font-black text-slate-900 dark:text-white">
              {{ selectedBooking.car?.car_name }} {{ selectedBooking.car?.car_model }}
            </h3>
            <p class="text-xs text-slate-400 font-mono">
              Plate: {{ selectedBooking.car?.car_number }} &bull; ID #BK-{{ selectedBooking.id }}
            </p>
          </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs space-y-2.5">
          <div class="flex items-center justify-between">
            <span class="text-slate-400">Customer Name:</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ getCustomerName(selectedBooking) }}</span>
          </div>

          <div
            v-if="selectedBooking.customer?.mobile || selectedBooking.customer?.phone_number"
            class="flex items-center justify-between"
          >
            <span class="text-slate-400">Contact Phone:</span>
            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
              {{ selectedBooking.customer?.mobile || selectedBooking.customer?.phone_number }}
            </span>
          </div>

          <div class="flex items-center justify-between">
            <span class="text-slate-400">Scheduled Dates:</span>
            <span class="font-semibold text-slate-800 dark:text-slate-200">
              {{ selectedBooking.pickup_date || selectedBooking.pick_up_date || selectedBooking.start_date }} &rarr;
              {{ selectedBooking.return_date || selectedBooking.last_date || selectedBooking.end_date }}
            </span>
          </div>

          <div class="flex items-center justify-between">
            <span class="text-slate-400">Reservation Status:</span>
            <span
              :class="{
                'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300': selectedBooking.status === 'confirmed',
                'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300': selectedBooking.status === 'pending',
                'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300': selectedBooking.status === 'completed',
              }"
              class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider"
            >
              {{ selectedBooking.status }}
            </span>
          </div>

          <div class="flex items-center justify-between pt-1 border-t border-slate-200 dark:border-slate-700">
            <span class="text-slate-400">Rental Total:</span>
            <span class="text-base font-black text-emerald-600 dark:text-emerald-400">
              ${{ selectedBooking.total_price || selectedBooking.amount || 0 }}
            </span>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
          <Link
            href="/owner/bookings"
            class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center shadow-md shadow-emerald-500/25 transition-all"
            @click="isModalOpen = false"
          >
            Manage All Fleet Bookings &rarr;
          </Link>
        </div>
      </div>
    </div>
  </OwnerLayout>
</template>
