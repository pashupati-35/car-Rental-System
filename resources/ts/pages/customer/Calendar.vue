<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'

interface CarItem {
  id: number
  car_name: string
  brand?: string
  car_model?: string
  car_number?: string
  car_price_per_day?: number
}

interface BookingItem {
  id: number
  car_id: number
  pick_up_date: string
  last_date: string
  status: string
  total_price?: number
  car?: CarItem
}

const props = defineProps<{
  cars: CarItem[]
  bookings: BookingItem[]
}>()

const selectedCarId = ref<number | ''>('')
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

const currentMonthName = computed(() => `${monthNames[month.value]} ${year.value}`)

const filteredBookings = computed(() => {
  if (!selectedCarId.value) return props.bookings || []

  return (props.bookings || []).filter((b: BookingItem) => b.car_id === selectedCarId.value)
})

const daysInMonth = computed(() => {
  const date = new Date(year.value, month.value + 1, 0)

  return date.getDate()
})

const firstDayOfWeek = computed(() => {
  const date = new Date(year.value, month.value, 1)

  return date.getDay()
})

const calendarDays = computed(() => {
  const days = []
  for (let i = 0; i < firstDayOfWeek.value; i++) {
    days.push({ day: null, dateStr: '', isCurrentMonth: false, bookings: [] })
  }

  for (let d = 1; d <= daysInMonth.value; d++) {
    const monthStr = String(month.value + 1).padStart(2, '0')
    const dayStr = String(d).padStart(2, '0')
    const dateStr = `${year.value}-${monthStr}-${dayStr}`

    const dayBookings = filteredBookings.value.filter((b: BookingItem) => {
      const start = b.pick_up_date.split('T')[0]
      const end = b.last_date.split('T')[0]

      return dateStr >= start && dateStr <= end
    })

    days.push({
      day: d,
      dateStr,
      isCurrentMonth: true,
      bookings: dayBookings,
    })
  }

  return days
})

const prevMonth = () => {
  currentDate.value = new Date(year.value, month.value - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(year.value, month.value + 1, 1)
}

const openDateDetails = (cell: any) => {
  if (!cell.day) return
  selectedDateDetails.value = {
    date: cell.dateStr,
    bookings: cell.bookings,
  }
  isModalOpen.value = true
}
</script>

<template>
  <CustomerLayout>
    <Head title="Fleet Availability Calendar - Traveler Portal" />

    <div class="space-y-8 max-w-7xl mx-auto">
      <!-- Header Banner -->
      <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 relative z-10">
          <span class="px-3 py-1 rounded-full bg-white/20 text-blue-100 text-xs font-semibold uppercase tracking-wider">
            Live Availability Schedule
          </span>
          <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
            Check Vehicle Availability & Schedule
          </h2>
          <p class="text-xs sm:text-sm text-blue-100/90 max-w-xl leading-relaxed">
            Real-time calendar view of all reserved dates across our verified vehicle catalog. Pick open dates to ensure zero booking conflicts.
          </p>
        </div>

        <div class="flex flex-wrap gap-3 relative z-10">
          <Link
            href="/customer/cars"
            class="px-5 py-3.5 rounded-2xl bg-white text-blue-800 font-bold text-xs shadow-lg hover:bg-blue-50 transition-all flex items-center gap-2"
          >
            <i class="ri-car-line text-base text-blue-600" />
            <span>Browse All Fleet</span>
          </Link>
        </div>
      </div>

      <!-- Calendar Controls Toolbar -->
      <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Vehicle Filter Dropdown -->
        <div class="flex items-center gap-3">
          <label class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0">
            Filter Vehicle:
          </label>
          <select
            v-model="selectedCarId"
            class="px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500"
          >
            <option value="">
              All Fleet Vehicles ({{ cars?.length || 0 }})
            </option>
            <option
              v-for="car in cars"
              :key="car.id"
              :value="car.id"
            >
              {{ car.car_name || car.brand }} {{ car.car_model || car.model }} ({{ car.car_number }})
            </option>
          </select>
        </div>

        <!-- Month Switcher Controls -->
        <div class="flex items-center gap-3">
          <button
            type="button"
            class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer"
            aria-label="Previous Month"
            @click="prevMonth"
          >
            <i class="ri-arrow-left-s-line text-lg" />
          </button>
          <span class="text-base font-black text-slate-900 dark:text-white min-w-[140px] text-center">
            {{ currentMonthName }}
          </span>
          <button
            type="button"
            class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer"
            aria-label="Next Month"
            @click="nextMonth"
          >
            <i class="ri-arrow-right-s-line text-lg" />
          </button>
        </div>
      </div>

      <!-- Main Calendar Grid -->
      <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <!-- Weekday Headers -->
        <div class="grid grid-cols-7 gap-2 text-center text-[11px] font-black uppercase tracking-wider text-slate-400">
          <div>Sun</div>
          <div>Mon</div>
          <div>Tue</div>
          <div>Wed</div>
          <div>Thu</div>
          <div>Fri</div>
          <div>Sat</div>
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 gap-2">
          <div
            v-for="(cell, idx) in calendarDays"
            :key="idx"
            :class="[
              cell.day ? 'cursor-pointer hover:border-blue-400 dark:hover:border-blue-700' : 'opacity-30 pointer-events-none',
              cell.bookings.length > 0 ? 'bg-amber-50/70 dark:bg-amber-950/20 border-amber-200 dark:border-amber-900/40' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/70 dark:border-slate-800',
            ]"
            class="min-h-[90px] sm:min-h-[110px] p-2.5 rounded-2xl border flex flex-col justify-between transition-all"
            @click="openDateDetails(cell)"
          >
            <div class="flex items-center justify-between">
              <span
                v-if="cell.day"
                class="text-xs font-black text-slate-900 dark:text-white"
              >
                {{ cell.day }}
              </span>
              <span
                v-if="cell.bookings.length > 0"
                class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"
              />
            </div>

            <div
              v-if="cell.bookings.length > 0"
              class="space-y-1 mt-1"
            >
              <div
                v-for="b in cell.bookings.slice(0, 2)"
                :key="b.id"
                class="px-1.5 py-0.5 rounded-lg text-[9px] font-bold truncate bg-amber-200 dark:bg-amber-900/60 text-amber-900 dark:text-amber-200"
              >
                {{ b.car?.car_name || 'Booked' }}
              </div>
              <span
                v-if="cell.bookings.length > 2"
                class="text-[9px] text-slate-400 block"
              >
                +{{ cell.bookings.length - 2 }} more
              </span>
            </div>
            <div
              v-else-if="cell.day"
              class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400"
            >
              Available
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Date Details Modal -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm animate-in fade-in duration-150"
    >
      <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
          <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block">
              Schedule on Date
            </span>
            <h4 class="font-black text-lg text-slate-900 dark:text-white">
              {{ selectedDateDetails?.date }}
            </h4>
          </div>
          <button
            type="button"
            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
            @click="isModalOpen = false"
          >
            <i class="ri-close-line text-2xl" />
          </button>
        </div>

        <div
          v-if="selectedDateDetails && selectedDateDetails.bookings.length > 0"
          class="space-y-2.5 max-h-60 overflow-y-auto"
        >
          <div
            v-for="b in selectedDateDetails.bookings"
            :key="b.id"
            class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 text-xs space-y-1"
          >
            <div class="flex justify-between font-bold text-slate-900 dark:text-white">
              <span>{{ b.car?.car_name || b.car?.brand || 'Car' }} {{ b.car?.car_model || '' }}</span>
              <span class="uppercase text-amber-700 dark:text-amber-400">{{ b.status }}</span>
            </div>
            <span class="text-[11px] text-slate-500 block">{{ b.pick_up_date.split('T')[0] }} to {{ b.last_date.split('T')[0] }}</span>
          </div>
        </div>

        <div
          v-else
          class="p-6 text-center text-xs text-slate-500 bg-slate-50 dark:bg-slate-800/60 rounded-2xl"
        >
          <i class="ri-checkbox-circle-fill text-2xl text-emerald-500 block mb-1" />
          <span>All fleet vehicles are completely free and available for reservation on this date!</span>
        </div>

        <div class="pt-2 flex items-center justify-between">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300"
            @click="isModalOpen = false"
          >
            Close
          </button>
          <Link
            href="/customer/cars"
            class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-md hover:bg-blue-700"
          >
            Browse & Book Car &rarr;
          </Link>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>
