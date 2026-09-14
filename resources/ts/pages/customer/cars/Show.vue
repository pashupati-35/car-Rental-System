<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import MessageBox from '@/components/MessageBox.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'
import { resolveMediaUrl } from '@/utils/helpers'
import axios from 'axios'

const props = defineProps<{
  car: any
  bookedRanges?: Array<any>
}>()

const carData = ref<any>(props.car)
const submitting = ref(false)
const flashSuccess = ref('')
const flashError = ref('')

const isDriverModalOpen = ref(false)
const selectedDriver = ref<any>(null)

const isOwnerModalOpen = ref(false)
const selectedOwner = ref<any>(null)

const today = new Date().toISOString().split('T')[0]
const defaultReturn = new Date(Date.now() + 86400000 * 3).toISOString().split('T')[0]

const bookingForm = ref({
  pick_up_date: today,
  last_date: defaultReturn,
  pickup_location: 'Kathmandu Central Hub',
  drop_location: 'Pokhara Lakeside',
  note: '',
})

// Calendar view month state
const calendarMonth = ref(new Date())

const getCarImage = (car: any) => {
  const url = resolveMediaUrl(car.car_photo || car.image, car.car_photo_path || car.image_path, 'car')

  return url || 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&auto=format&fit=crop&q=80'
}

const getDriverImage = (driver: any) => {
  if (!driver) return 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80'
  const url = resolveMediaUrl(driver.photo || driver.image, driver.photo_path || driver.image_path, 'driver')

  return url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80'
}

const getOwnerImage = (owner: any) => {
  if (!owner) return ''
  const url = resolveMediaUrl(owner.image, owner.image_path, 'owner')

  return url || ''
}

const openDriverModal = (driver: any) => {
  selectedDriver.value = driver || {
    name: carData.value?.driver_name || 'Assigned Chauffeur',
    phone: '+977-9800000000',
    email: 'chauffeur@carrental.com',
    experience_years: 5,
    license_number: 'LIC-NP-883921',
    status: 'active',
  }
  isDriverModalOpen.value = true
}

const openOwnerModal = (owner: any) => {
  selectedOwner.value = owner || {
    full_name: 'Verified Fleet Partner',
    contact_number: '+977-9811111111',
    email: 'fleet.partner@carrental.com',
    address: 'Kathmandu Ring Road Hub, Nepal',
    unique_identifier: 'OWN-PARTNER-01',
  }
  isOwnerModalOpen.value = true
}

// Date booking ranges
const ranges = computed(() => {
  return props.bookedRanges || []
})

// Formatted booked dates for AppDatePicker component
const formattedBookedDates = computed(() => {
  const dates: string[] = []

  ranges.value.forEach((r: any) => {
    const startStr = (r.start || '').split('T')[0]
    const endStr = (r.end || '').split('T')[0]
    if (startStr && endStr) {
      const curr = new Date(startStr)
      const last = new Date(endStr)
      while (curr <= last) {
        dates.push(curr.toISOString().split('T')[0])
        curr.setDate(curr.getDate() + 1)
      }
    }
  })

  return dates
})

const isDateBooked = (dateStr: string) => {
  return ranges.value.some((r: any) => dateStr >= r.start && dateStr <= r.end)
}

const hasDateOverlap = computed(() => {
  if (!bookingForm.value.pick_up_date || !bookingForm.value.last_date) return false
  const pStart = bookingForm.value.pick_up_date
  const pEnd = bookingForm.value.last_date
  if (pStart > pEnd) return true

  return ranges.value.some((r: any) => {
    return (pStart <= r.end && pEnd >= r.start)
  })
})

// Calendar Computed & Controls
const calYear = computed(() => calendarMonth.value.getFullYear())
const calMonthIndex = computed(() => calendarMonth.value.getMonth())

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

const calMonthName = computed(() => `${monthNames[calMonthIndex.value]} ${calYear.value}`)

const miniCalendarDays = computed(() => {
  const firstDay = new Date(calYear.value, calMonthIndex.value, 1).getDay()
  const totalDays = new Date(calYear.value, calMonthIndex.value + 1, 0).getDate()
  const days = []

  for (let i = 0; i < firstDay; i++) {
    days.push({ day: null, dateStr: '', status: 'empty' })
  }

  for (let d = 1; d <= totalDays; d++) {
    const mStr = String(calMonthIndex.value + 1).padStart(2, '0')
    const dStr = String(d).padStart(2, '0')
    const dateStr = `${calYear.value}-${mStr}-${dStr}`
    const booked = isDateBooked(dateStr)

    const inSelected = Boolean(
      bookingForm.value.pick_up_date &&
      bookingForm.value.last_date &&
      dateStr >= bookingForm.value.pick_up_date &&
      dateStr <= bookingForm.value.last_date,
    )

    days.push({
      day: d,
      dateStr,
      status: booked ? 'booked' : (inSelected ? 'selected' : 'available'),
    })
  }

  return days
})

const prevCalMonth = () => {
  calendarMonth.value = new Date(calYear.value, calMonthIndex.value - 1, 1)
}

const nextCalMonth = () => {
  calendarMonth.value = new Date(calYear.value, calMonthIndex.value + 1, 1)
}

const selectCalendarDate = (dateStr: string) => {
  if (!dateStr || isDateBooked(dateStr)) return

  if (!bookingForm.value.pick_up_date || (bookingForm.value.pick_up_date && bookingForm.value.last_date)) {
    bookingForm.value.pick_up_date = dateStr
    bookingForm.value.last_date = ''
  } else if (bookingForm.value.pick_up_date && !bookingForm.value.last_date) {
    if (dateStr < bookingForm.value.pick_up_date) {
      bookingForm.value.pick_up_date = dateStr
    } else {
      bookingForm.value.last_date = dateStr
    }
  }
}

const calculatedDays = computed(() => {
  if (!bookingForm.value.pick_up_date || !bookingForm.value.last_date) return 1
  const start = new Date(bookingForm.value.pick_up_date).getTime()
  const end = new Date(bookingForm.value.last_date).getTime()
  const diff = Math.ceil((end - start) / (1000 * 3600 * 24))

  return diff > 0 ? diff : 1
})

const calculatedTotal = computed(() => {
  const rate = Number(carData.value?.car_price_per_day) || 65

  return rate * calculatedDays.value
})

const submitReservation = async () => {
  if (hasDateOverlap.value) return
  submitting.value = true
  flashSuccess.value = ''
  flashError.value = ''

  try {
    const payload = {
      car_id: carData.value.id,
      rent_start_date: bookingForm.value.pick_up_date,
      rent_end_date: bookingForm.value.last_date,
      pick_up_date: bookingForm.value.pick_up_date,
      last_date: bookingForm.value.last_date,
      pickup_location: bookingForm.value.pickup_location,
      drop_location: bookingForm.value.drop_location,
      total_price: calculatedTotal.value,
      note: bookingForm.value.note,
      purpose: bookingForm.value.note,
    }

    const res = await axios.post('/booking/store', payload)
    if (res.data?.status === 'success' || res.status === 200) {
      flashSuccess.value = `Reservation confirmed for ${carData.value.car_name || carData.value.brand}!`
      setTimeout(() => {
        router.visit('/customer/bookings')
      }, 1000)
    }
  } catch (err: any) {
    flashError.value = err.response?.data?.message || 'Failed to submit reservation. Please check your dates and locations.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <CustomerLayout>
    <Head :title="`${carData.car_name || carData.brand} - Vehicle Reservation`" />

    <div class="space-y-8 max-w-7xl mx-auto">
      <!-- Flash Alert -->
      <MessageBox
        v-model="flashSuccess"
        type="success"
      />
      <MessageBox
        v-model="flashError"
        type="error"
      />

      <!-- Breadcrumbs -->
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
        <Link
          href="/customer/dashboard"
          class="hover:text-blue-600 transition-colors"
        >
          Dashboard
        </Link>
        <span>/</span>
        <Link
          href="/customer/cars"
          class="hover:text-blue-600 transition-colors"
        >
          Browse Fleet
        </Link>
        <span>/</span>
        <span class="text-slate-900 dark:text-white font-bold">{{ carData.car_name || carData.brand }} {{ carData.car_model || carData.model }}</span>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Vehicle Showcase & Specs (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Main Showcase Image -->
          <div class="relative rounded-3xl overflow-hidden bg-slate-900 shadow-xl border border-slate-200 dark:border-slate-800 h-80 sm:h-96">
            <img
              :src="getCarImage(carData)"
              class="w-full h-full object-cover"
              :alt="carData.car_name"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex flex-col justify-end p-6 sm:p-8 text-white">
              <span class="px-3 py-1 rounded-full bg-blue-600 text-white font-bold text-xs uppercase tracking-wider w-max mb-2 shadow-md">
                Verified Vehicle
              </span>
              <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                {{ carData.car_name || carData.brand }} {{ carData.car_model || carData.model }}
              </h1>
              <p class="text-xs sm:text-sm text-slate-300 font-mono mt-0.5">
                Plate: {{ carData.car_number }} &bull; {{ carData.number_of_seats }} Passenger Capacity
              </p>
            </div>
          </div>

          <!-- Key Vehicle Specs Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center shadow-xs">
              <i class="ri-user-star-line text-2xl text-blue-600 block mb-1" />
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Passenger Seats</span>
              <span class="text-base font-black text-slate-900 dark:text-white">{{ carData.number_of_seats }} Seats</span>
            </div>

            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center shadow-xs">
              <i class="ri-steering-2-line text-2xl text-emerald-600 block mb-1" />
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Chauffeur</span>
              <button
                type="button"
                class="text-sm font-black text-blue-600 dark:text-blue-400 hover:underline truncate block w-full text-center cursor-pointer"
                title="Click to view driver credentials"
                @click="openDriverModal(carData.driver)"
              >
                {{ carData.driver?.name || carData.driver_name || 'Assigned' }}
              </button>
            </div>

            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center shadow-xs">
              <i class="ri-money-dollar-circle-line text-2xl text-amber-600 block mb-1" />
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Daily Rate</span>
              <span class="text-base font-black text-slate-900 dark:text-white">${{ carData.car_price_per_day }}/day</span>
            </div>

            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-center shadow-xs">
              <i class="ri-shield-check-line text-2xl text-indigo-600 block mb-1" />
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Status</span>
              <span class="text-base font-black text-emerald-600 uppercase">Verified</span>
            </div>
          </div>

          <!-- Description & Details Card -->
          <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
            <h3 class="font-black text-lg text-slate-900 dark:text-white">
              About This Vehicle & Services
            </h3>
            <div
              v-if="carData.description"
              class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed"
              v-html="carData.description"
            />
            <p
              v-else
              class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed"
            >
              Premium luxury vehicle inspected and certified for intercity travel, executive transfers, and holiday tours. Includes comprehensive passenger insurance, full sanitization, dedicated 24/7 route support, and certified courteous chauffeur.
            </p>

            <!-- Fleet Partner Info Card (Clickable) -->
            <div class="p-4 rounded-2xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40 flex items-center justify-between text-xs">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">
                  {{ (carData.owner?.full_name || 'F')[0].toUpperCase() }}
                </div>
                <div>
                  <button
                    type="button"
                    class="font-bold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:underline block text-left cursor-pointer text-sm"
                    title="Click to view partner profile"
                    @click="openOwnerModal(carData.owner)"
                  >
                    {{ carData.owner?.full_name || 'Verified Fleet Partner' }}
                  </button>
                  <span class="text-[11px] text-slate-400">Official AutoRent Network Operator</span>
                </div>
              </div>
              <button
                type="button"
                class="px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold text-[10px] uppercase tracking-wider hover:bg-emerald-200 cursor-pointer"
                @click="openOwnerModal(carData.owner)"
              >
                Partner Active &bull; View &rarr;
              </button>
            </div>

            <!-- Chauffeur Credentials Card (Clickable) -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
              <div class="flex items-center gap-3">
                <img
                  :src="getDriverImage(carData.driver)"
                  class="w-10 h-10 rounded-xl object-cover shadow-xs"
                  alt="Chauffeur photo"
                >
                <div>
                  <button
                    type="button"
                    class="font-bold text-blue-600 dark:text-blue-400 hover:underline block text-left cursor-pointer text-sm"
                    title="Click to view chauffeur credentials"
                    @click="openDriverModal(carData.driver)"
                  >
                    {{ carData.driver?.name || carData.driver_name || 'Assigned Chauffeur' }}
                  </button>
                  <span class="text-[11px] text-slate-400">Certified Professional &bull; {{ carData.driver?.experience_years || 5 }}+ Years Experience</span>
                </div>
              </div>
              <button
                type="button"
                class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-bold text-[10px] uppercase tracking-wider hover:bg-blue-200 cursor-pointer"
                @click="openDriverModal(carData.driver)"
              >
                Chauffeur Profile &rarr;
              </button>
            </div>
          </div>

          <!-- Vehicle Interactive Availability Schedule -->
          <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-black text-lg text-slate-900 dark:text-white">
                  Vehicle Availability Schedule
                </h3>
                <p class="text-xs text-slate-500">
                  Select available dates directly from the calendar below
                </p>
              </div>
              <div class="flex items-center gap-1.5">
                <button
                  type="button"
                  class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs cursor-pointer shadow-xs"
                  @click="prevCalMonth"
                >
                  <i class="ri-arrow-left-s-line text-base" />
                </button>
                <span class="text-xs font-black px-2 min-w-[120px] text-center">{{ calMonthName }}</span>
                <button
                  type="button"
                  class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs cursor-pointer shadow-xs"
                  @click="nextCalMonth"
                >
                  <i class="ri-arrow-right-s-line text-base" />
                </button>
              </div>
            </div>

            <!-- Calendar Legend -->
            <div class="flex items-center gap-4 text-[11px] font-bold text-slate-500 pb-1">
              <span class="flex items-center gap-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" /> Available for Booking
              </span>
              <span class="flex items-center gap-1">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500" /> Reserved / Booked
              </span>
              <span class="flex items-center gap-1">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600" /> Selected Rental Range
              </span>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
              <div class="font-bold text-slate-400 py-1">
                Sun
              </div>
              <div class="font-bold text-slate-400 py-1">
                Mon
              </div>
              <div class="font-bold text-slate-400 py-1">
                Tue
              </div>
              <div class="font-bold text-slate-400 py-1">
                Wed
              </div>
              <div class="font-bold text-slate-400 py-1">
                Thu
              </div>
              <div class="font-bold text-slate-400 py-1">
                Fri
              </div>
              <div class="font-bold text-slate-400 py-1">
                Sat
              </div>

              <button
                v-for="(c, idx) in miniCalendarDays"
                :key="idx"
                type="button"
                :disabled="!c.day || c.status === 'booked'"
                :class="[
                  !c.day ? 'opacity-0 pointer-events-none' : '',
                  c.status === 'booked' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 line-through cursor-not-allowed border border-amber-300 dark:border-amber-800 font-bold' : '',
                  c.status === 'selected' ? 'bg-blue-600 text-white font-black shadow-md shadow-blue-500/30' : '',
                  c.status === 'available' ? 'bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:border-blue-400 border border-slate-200 dark:border-slate-700 cursor-pointer font-bold' : '',
                ]"
                class="py-2.5 rounded-xl transition-all text-xs"
                :title="c.status === 'booked' ? 'Already booked' : 'Click to select date'"
                @click="selectCalendarDate(c.dateStr)"
              >
                {{ c.day }}
              </button>
            </div>
          </div>
        </div>

        <!-- Right: Reservation Form Card (1 Col Sticky) -->
        <div class="space-y-6">
          <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xl space-y-5 sticky top-24">
            <div>
              <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 block">
                Instant Reservation
              </span>
              <h3 class="font-black text-xl text-slate-900 dark:text-white mt-0.5">
                Book This Vehicle
              </h3>
              <div class="flex items-baseline gap-1 mt-2">
                <span class="text-3xl font-black text-blue-600 dark:text-blue-400">${{ carData.car_price_per_day }}</span>
                <span class="text-xs text-slate-400 font-medium">/ 24 hour rental period</span>
              </div>
            </div>

            <!-- Overlap Shield Status Banner -->
            <div
              v-if="hasDateOverlap"
              class="p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-bold flex items-center gap-2"
            >
              <i class="ri-error-warning-fill text-lg text-rose-600 shrink-0" />
              <span>Dates Conflict: Vehicle is already reserved for this date range.</span>
            </div>
            <div
              v-else-if="bookingForm.pick_up_date && bookingForm.last_date"
              class="p-2.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-[11px] font-bold flex items-center gap-1.5"
            >
              <i class="ri-checkbox-circle-fill text-base text-emerald-600 shrink-0" />
              <span>Dates verified open & available!</span>
            </div>

            <form
              class="space-y-4 pt-2"
              @submit.prevent="submitReservation"
            >
              <div>
                <AppDatePicker
                  v-model="bookingForm.pick_up_date"
                  label="Pickup Date *"
                  placeholder="Select pickup date..."
                  min-date="today"
                  :booked-dates="formattedBookedDates"
                />
              </div>

              <div>
                <AppDatePicker
                  v-model="bookingForm.last_date"
                  label="Return Date *"
                  placeholder="Select return date..."
                  :min-date="bookingForm.pick_up_date || 'today'"
                  :booked-dates="formattedBookedDates"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Pickup Location *
                </label>
                <input
                  v-model="bookingForm.pickup_location"
                  type="text"
                  placeholder="e.g. Kathmandu Hub or Airport"
                  required
                  class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white"
                >
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Drop-off Location *
                </label>
                <input
                  v-model="bookingForm.drop_location"
                  type="text"
                  placeholder="e.g. Pokhara Lakeside"
                  required
                  class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white"
                >
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Trip Notes (Optional)
                </label>
                <textarea
                  v-model="bookingForm.note"
                  rows="2"
                  placeholder="e.g. Luggage count, preferred pickup time"
                  class="w-full px-3.5 py-2 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white"
                />
              </div>

              <!-- Price Breakdown Box -->
              <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 space-y-2 text-xs">
                <div class="flex justify-between text-slate-500">
                  <span>${{ carData.car_price_per_day }} &times; {{ calculatedDays }} Days</span>
                  <span>${{ calculatedTotal }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                  <span>Chauffeur & GPS</span>
                  <span class="text-emerald-600 font-bold">Included Free</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-200 dark:border-slate-700 font-black text-sm">
                  <span class="text-slate-900 dark:text-white">Estimated Total:</span>
                  <span class="text-2xl font-black text-blue-600 dark:text-blue-400">${{ calculatedTotal }}</span>
                </div>
              </div>

              <!-- Submit CTA -->
              <button
                type="submit"
                :disabled="submitting || hasDateOverlap"
                :class="hasDateOverlap ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-xl shadow-blue-600/30 cursor-pointer'"
                class="w-full py-3.5 px-4 rounded-2xl font-black text-xs transition-all flex items-center justify-center gap-2"
              >
                <i
                  v-if="submitting"
                  class="ri-loader-4-line animate-spin text-sm"
                />
                <span>{{ submitting ? 'Processing Reservation...' : 'Confirm & Reserve Vehicle' }}</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Driver Details Modal -->
    <div
      v-if="isDriverModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm animate-in fade-in duration-150"
    >
      <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 flex items-center justify-center font-bold">
              <i class="ri-user-star-line text-lg" />
            </span>
            <div>
              <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block">
                Chauffeur Profile
              </span>
              <h3 class="font-black text-base text-slate-900 dark:text-white">
                {{ selectedDriver?.name }}
              </h3>
            </div>
          </div>
          <button
            type="button"
            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
            @click="isDriverModalOpen = false"
          >
            <i class="ri-close-line text-2xl" />
          </button>
        </div>

        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
          <img
            :src="getDriverImage(selectedDriver)"
            class="w-16 h-16 rounded-2xl object-cover shadow-sm ring-2 ring-blue-500/20"
            alt="Driver Photo"
          >
          <div class="space-y-1">
            <h4 class="font-extrabold text-slate-900 dark:text-white text-base">
              {{ selectedDriver?.name }}
            </h4>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
              <i class="ri-shield-check-fill text-xs" /> Certified & Verified
            </span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs">
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Experience</span>
            <span class="font-bold text-slate-900 dark:text-white text-sm">{{ selectedDriver?.experience_years || 5 }}+ Years</span>
          </div>
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">License Number</span>
            <span class="font-bold text-slate-900 dark:text-white font-mono text-xs">{{ selectedDriver?.license_number || 'VERIFIED-NP' }}</span>
          </div>
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 col-span-2">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Direct Contact Phone</span>
            <a
              :href="`tel:${selectedDriver?.phone || ''}`"
              class="font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1.5 mt-0.5"
            >
              <i class="ri-phone-fill text-xs" />
              <span>{{ selectedDriver?.phone || '+977-9800000000' }}</span>
            </a>
          </div>
        </div>

        <div class="pt-2 flex justify-end">
          <button
            type="button"
            class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold cursor-pointer"
            @click="isDriverModalOpen = false"
          >
            Close Details
          </button>
        </div>
      </div>
    </div>

    <!-- Owner / Partner Details Modal -->
    <div
      v-if="isOwnerModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm animate-in fade-in duration-150"
    >
      <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold">
              <i class="ri-building-line text-lg" />
            </span>
            <div>
              <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 block">
                Fleet Partner Organization
              </span>
              <h3 class="font-black text-base text-slate-900 dark:text-white">
                {{ selectedOwner?.full_name }}
              </h3>
            </div>
          </div>
          <button
            type="button"
            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
            @click="isOwnerModalOpen = false"
          >
            <i class="ri-close-line text-2xl" />
          </button>
        </div>

        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
          <div
            v-if="getOwnerImage(selectedOwner)"
            class="w-14 h-14 rounded-2xl overflow-hidden shadow-sm"
          >
            <img
              :src="getOwnerImage(selectedOwner)"
              class="w-full h-full object-cover"
              alt="Owner logo"
            >
          </div>
          <div
            v-else
            class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-blue-600 text-white font-black text-xl flex items-center justify-center shadow-sm"
          >
            {{ (selectedOwner?.full_name || 'F')[0].toUpperCase() }}
          </div>
          <div class="space-y-1 min-w-0">
            <h4 class="font-extrabold text-slate-900 dark:text-white text-base truncate">
              {{ selectedOwner?.full_name }}
            </h4>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300">
              <i class="ri-verified-badge-fill text-xs" /> Verified Partner
            </span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs">
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Partner Code</span>
            <span class="font-bold text-slate-900 dark:text-white font-mono text-xs truncate block">{{ selectedOwner?.unique_identifier || 'OWN-NET-01' }}</span>
          </div>
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Contact Phone</span>
            <a
              :href="`tel:${selectedOwner?.contact_number || selectedOwner?.phone || ''}`"
              class="font-bold text-blue-600 dark:text-blue-400 hover:underline block truncate"
            >
              {{ selectedOwner?.contact_number || selectedOwner?.phone || '+977-9811111111' }}
            </a>
          </div>
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 col-span-2">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Headquarters & Fleet Hub</span>
            <span class="font-semibold text-slate-800 dark:text-slate-200 block mt-0.5">{{ selectedOwner?.address || 'Kathmandu Ring Road Hub, Nepal' }}</span>
          </div>
        </div>

        <div class="pt-2 flex justify-end">
          <button
            type="button"
            class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold cursor-pointer"
            @click="isOwnerModalOpen = false"
          >
            Close Profile
          </button>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

