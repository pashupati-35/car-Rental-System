<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import MessageBox from '@/components/MessageBox.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'
import { resolveMediaUrl } from '@/utils/helpers'
import axios from 'axios'

const props = defineProps<{
  cars: Array<any>
  filters?: {
    search?: string
    seats?: string
    min_price?: string
    max_price?: string
  }
  stats?: {
    totalAvailable?: number
    avgRate?: number
  }
}>()

const searchQuery = ref(props.filters?.search || '')
const selectedSeats = ref(props.filters?.seats || '')
const selectedCategory = ref('all')
const maxPrice = ref(props.filters?.max_price || '')

const flashSuccess = ref('')
const flashError = ref('')
const modalError = ref('')
const modalSuccess = ref('')

// Modals
const isBookingModalOpen = ref(false)
const selectedCarForBooking = ref<any>(null)
const bookingSubmitting = ref(false)

const isDriverModalOpen = ref(false)
const selectedDriver = ref<any>(null)

const isOwnerModalOpen = ref(false)
const selectedOwner = ref<any>(null)

const bookingForm = ref({
  pick_up_date: '',
  last_date: '',
  pickup_location: '',
  drop_location: '',
  note: '',
})

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

const filteredCars = computed(() => {
  const list = Array.isArray(props.cars) ? props.cars : (props.cars as any)?.data || []

  return list.filter((car: any) => {
    // Search query
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase()
      const name = (car.car_name || car.brand || '').toLowerCase()
      const model = (car.car_model || car.model || '').toLowerCase()
      const plate = (car.car_number || '').toLowerCase()
      if (!name.includes(q) && !model.includes(q) && !plate.includes(q)) return false
    }

    // Seats filter
    if (selectedSeats.value) {
      if (Number(car.number_of_seats) < Number(selectedSeats.value)) return false
    }

    // Price filter
    if (maxPrice.value && Number(car.car_price_per_day) > Number(maxPrice.value)) {
      return false
    }

    // Category filter
    if (selectedCategory.value !== 'all') {
      const cat = selectedCategory.value.toLowerCase()
      const text = `${car.car_name || ''} ${car.car_model || ''} ${car.brand || ''} ${car.description || ''}`.toLowerCase()
      if (!text.includes(cat)) return false
    }

    return true
  })
})

const categories = [
  { id: 'all', label: 'All Fleet', icon: 'ri-apps-line' },
  { id: 'suv', label: 'SUVs & 4x4', icon: 'ri-car-line' },
  { id: 'sedan', label: 'Sedans', icon: 'ri-roadster-line' },
  { id: 'electric', label: 'EV & Electric', icon: 'ri-flashlight-line' },
  { id: 'luxury', label: 'Luxury VIP', icon: 'ri-vip-crown-line' },
]

const openDriverModal = (driver: any, fallbackName = 'Assigned Chauffeur') => {
  selectedDriver.value = driver || {
    name: fallbackName,
    phone: '+977-9800000000',
    email: 'chauffeur@carrental.com',
    experience_years: 5,
    license_number: 'LIC-NP-883921',
    status: 'active',
  }
  isDriverModalOpen.value = true
}

const openOwnerModal = (owner: any, fallbackName = 'Verified Fleet Partner') => {
  selectedOwner.value = owner || {
    full_name: fallbackName,
    contact_number: '+977-9811111111',
    email: 'fleet.partner@carrental.com',
    address: 'Kathmandu Ring Road Hub, Nepal',
    unique_identifier: 'OWN-PARTNER-01',
  }
  isOwnerModalOpen.value = true
}

// Booked dates for currently selected car formatted for AppDatePicker
const formattedBookedDates = computed(() => {
  if (!selectedCarForBooking.value) return []
  const bookings = selectedCarForBooking.value.booking || []
  const dates: string[] = []

  bookings.forEach((b: any) => {
    const startStr = (b.pick_up_date || '').split('T')[0]
    const endStr = (b.last_date || '').split('T')[0]
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

// Booked ranges for currently selected car
const carBookedRanges = computed(() => {
  if (!selectedCarForBooking.value) return []
  const bookings = selectedCarForBooking.value.booking || []

  return bookings.map((b: any) => ({
    start: (b.pick_up_date || '').split('T')[0],
    end: (b.last_date || '').split('T')[0],
    status: b.status,
  }))
})

// Check if selected bookingForm dates overlap with existing bookings
const hasDateOverlap = computed(() => {
  if (!bookingForm.value.pick_up_date || !bookingForm.value.last_date) return false
  const pStart = bookingForm.value.pick_up_date
  const pEnd = bookingForm.value.last_date
  if (pStart > pEnd) return true

  return carBookedRanges.value.some((r: any) => {
    return (pStart <= r.end && pEnd >= r.start)
  })
})

const openBookingModal = (car: any) => {
  selectedCarForBooking.value = car
  modalError.value = ''
  modalSuccess.value = ''

  // Find next free dates
  const now = new Date()
  const pickup = new Date(now.getTime() + 86400000)
  const drop = new Date(now.getTime() + 86400000 * 3)

  const pStr = pickup.toISOString().split('T')[0]
  const dStr = drop.toISOString().split('T')[0]

  bookingForm.value = {
    pick_up_date: pStr,
    last_date: dStr,
    pickup_location: 'Kathmandu Central Terminal',
    drop_location: 'Pokhara Lakeside',
    note: '',
  }
  isBookingModalOpen.value = true
}

const calculatedDays = computed(() => {
  if (!bookingForm.value.pick_up_date || !bookingForm.value.last_date) return 1
  const start = new Date(bookingForm.value.pick_up_date).getTime()
  const end = new Date(bookingForm.value.last_date).getTime()
  const diff = Math.ceil((end - start) / (1000 * 3600 * 24))

  return diff > 0 ? diff : 1
})

const calculatedTotal = computed(() => {
  const rate = Number(selectedCarForBooking.value?.car_price_per_day) || 65

  return rate * calculatedDays.value
})

const submitBooking = async () => {
  if (!selectedCarForBooking.value || hasDateOverlap.value) return
  bookingSubmitting.value = true
  modalError.value = ''
  modalSuccess.value = ''
  flashSuccess.value = ''
  flashError.value = ''

  try {
    const payload = {
      car_id: selectedCarForBooking.value.id,
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
      modalSuccess.value = `Reservation confirmed for ${selectedCarForBooking.value.car_name || selectedCarForBooking.value.brand}!`
      flashSuccess.value = `Reservation confirmed for ${selectedCarForBooking.value.car_name || selectedCarForBooking.value.brand}!`
      setTimeout(() => {
        isBookingModalOpen.value = false
        router.visit('/customer/bookings')
      }, 1000)
    }
  } catch (err: any) {
    const msg = err.response?.data?.message || err.response?.data?.error || 'Failed to submit reservation. Please check your dates and locations.'

    modalError.value = msg
    flashError.value = msg
  } finally {
    bookingSubmitting.value = false
  }
}
</script>

<template>
  <CustomerLayout>
    <Head title="Browse Fleet - Traveler Portal" />

    <div class="space-y-8 max-w-7xl mx-auto">
      <!-- Flash Alert messages -->
      <MessageBox
        v-model="flashSuccess"
        type="success"
      />
      <MessageBox
        v-model="flashError"
        type="error"
      />

      <!-- Hero Header -->
      <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 relative z-10">
          <span class="px-3 py-1 rounded-full bg-white/20 text-blue-100 text-xs font-semibold uppercase tracking-wider">
            Verified Fleet Directory
          </span>
          <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
            Browse Premium Vehicles & Instant Reservation
          </h2>
          <p class="text-xs sm:text-sm text-blue-100/90 max-w-xl leading-relaxed">
            Choose from {{ stats?.totalAvailable || filteredCars.length }} verified luxury vehicles with guaranteed certified chauffeurs, zero double-booking conflicts, and instant confirmation.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
          <Link
            href="/customer/calendar"
            class="px-5 py-3.5 rounded-2xl bg-white text-blue-800 font-bold text-xs shadow-lg hover:bg-blue-50 transition-all flex items-center gap-2"
          >
            <i class="ri-calendar-line text-base text-blue-600" />
            <span>Check Calendar</span>
          </Link>
          <Link
            href="/customer/bookings"
            class="px-5 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-2"
          >
            <i class="ri-book-read-line text-base" />
            <span>My Bookings</span>
          </Link>
        </div>
      </div>

      <!-- Search & Filters Toolbar -->
      <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Keyword Search -->
          <div class="sm:col-span-2 relative">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
              Search Vehicle or Model
            </label>
            <div class="relative">
              <i class="ri-search-line absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search Hyundai Creta, BYD, Toyota, Scorpio..."
                class="w-full ps-10 pe-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
              >
            </div>
          </div>

          <!-- Capacity Filter -->
          <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
              Minimum Seats
            </label>
            <select
              v-model="selectedSeats"
              class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer"
            >
              <option value="">
                All Passenger Capacities
              </option>
              <option value="4">
                4+ Passenger Seats
              </option>
              <option value="5">
                5+ Passenger Seats
              </option>
              <option value="7">
                7+ Large Capacity SUV/Van
              </option>
            </select>
          </div>

          <!-- Max Daily Rate -->
          <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
              Max Daily Budget ($)
            </label>
            <div class="relative">
              <span class="absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">$</span>
              <input
                v-model="maxPrice"
                type="number"
                placeholder="e.g. 150"
                class="w-full ps-8 pe-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
              >
            </div>
          </div>
        </div>

        <!-- Category Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar border-t border-slate-100 dark:border-slate-800 pt-4">
          <button
            v-for="cat in categories"
            :key="cat.id"
            type="button"
            :class="selectedCategory === cat.id ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 font-semibold'"
            class="px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shrink-0 transition-all cursor-pointer"
            @click="selectedCategory = cat.id"
          >
            <i :class="cat.icon" />
            <span>{{ cat.label }}</span>
          </button>
        </div>
      </div>

      <!-- Cars Grid -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold text-slate-500">
            Showing <strong class="text-slate-900 dark:text-white">{{ filteredCars.length }}</strong> verified fleet vehicles
          </p>
        </div>

        <div
          v-if="filteredCars.length > 0"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <div
            v-for="car in filteredCars"
            :key="car.id"
            class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between space-y-4 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-900 transition-all group"
          >
            <div>
              <!-- Car Image with Badges -->
              <div class="relative overflow-hidden rounded-2xl mb-4 bg-slate-100 dark:bg-slate-800 h-48">
                <img
                  :src="getCarImage(car)"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  :alt="car.car_name"
                >
                <span class="absolute top-3 start-3 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-slate-900/80 backdrop-blur-md text-white shadow-sm">
                  {{ car.number_of_seats || 5 }} Seats
                </span>
                <span class="absolute top-3 end-3 px-3 py-1 rounded-full text-xs font-black bg-blue-600 text-white shadow-md shadow-blue-600/30">
                  ${{ car.car_price_per_day || car.price_per_day || 65 }}/day
                </span>
              </div>

              <!-- Car Title & Spec Info -->
              <div class="space-y-2">
                <div class="flex items-start justify-between">
                  <div>
                    <h4 class="font-bold text-lg text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors">
                      {{ car.car_name || car.brand }} {{ car.car_model || car.model }}
                    </h4>
                    <p class="text-xs font-mono text-slate-400">
                      {{ car.car_number }}
                    </p>
                  </div>
                </div>

                <!-- Driver & Owner Interactive Info Box -->
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs space-y-2">
                  <!-- Driver Row (Clickable) -->
                  <div class="flex justify-between items-center">
                    <span class="text-slate-400">Assigned Chauffeur:</span>
                    <button
                      type="button"
                      class="font-bold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1 cursor-pointer"
                      title="Click to view full driver credentials"
                      @click="openDriverModal(car.driver, car.driver_name)"
                    >
                      <i class="ri-user-star-line text-sm text-blue-500" />
                      <span>{{ car.driver?.name || car.driver_name || 'Certified Driver' }}</span>
                    </button>
                  </div>

                  <!-- Fleet Partner Row (Clickable) -->
                  <div class="flex justify-between items-center">
                    <span class="text-slate-400">Fleet Partner:</span>
                    <button
                      type="button"
                      class="font-semibold text-slate-800 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 hover:underline inline-flex items-center gap-1 cursor-pointer"
                      title="Click to view fleet partner profile"
                      @click="openOwnerModal(car.owner, 'Verified Fleet Partner')"
                    >
                      <i class="ri-building-line text-sm text-indigo-500" />
                      <span>{{ car.owner?.full_name || 'Verified Fleet Partner' }}</span>
                    </button>
                  </div>

                  <div class="flex justify-between items-center pt-1.5 border-t border-slate-200/50 dark:border-slate-700/50 text-[11px] text-emerald-600 font-bold">
                    <span class="flex items-center gap-1">
                      <i class="ri-checkbox-circle-fill text-emerald-500" /> Free Cancellation
                    </span>
                    <span class="flex items-center gap-1">
                      <i class="ri-shield-check-fill text-blue-500" /> GPS & Insurance
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 pt-1">
              <Link
                :href="`/customer/cars/${car.id}`"
                class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors text-center"
              >
                View Details
              </Link>
              <button
                type="button"
                class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-md shadow-blue-600/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                @click="openBookingModal(car)"
              >
                <span>Reserve Now</span>
                <i class="ri-arrow-right-line" />
              </button>
            </div>
          </div>
        </div>

        <div
          v-else
          class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs"
        >
          <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-950 text-blue-600 flex items-center justify-center text-3xl mx-auto mb-3">
            <i class="ri-search-eye-line" />
          </div>
          <h4 class="font-black text-lg text-slate-900 dark:text-white">
            No matching vehicles found
          </h4>
          <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
            Try adjusting your search query, passenger capacity, or price range filters.
          </p>
          <button
            type="button"
            class="mt-4 px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md"
            @click="searchQuery = ''; selectedSeats = ''; maxPrice = ''; selectedCategory = 'all'"
          >
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Driver Details Popup Modal -->
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

        <!-- Driver Avatar & Bio -->
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

        <!-- Driver Details Grid -->
        <div class="grid grid-cols-2 gap-3 text-xs">
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Experience</span>
            <span class="font-bold text-slate-900 dark:text-white text-sm">{{ selectedDriver?.experience_years || 5 }}+ Years Professional</span>
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

        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/40 text-[11px] text-blue-800 dark:text-blue-300">
          <i class="ri-information-line font-bold" /> This chauffeur has passed defensive driving, navigation training, and background criminal checks.
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

    <!-- Owner / Partner Details Popup Modal -->
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

        <!-- Owner Bio & Badge -->
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

        <!-- Partner Details Grid -->
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

    <!-- Instant Reservation Modal with AppDatePicker -->
    <div
      v-if="isBookingModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm animate-in fade-in duration-150"
    >
      <div class="relative w-full max-w-xl rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 space-y-5 overflow-y-auto max-h-[90vh]">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
          <div>
            <span class="text-[10px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 block">
              Instant Reservation
            </span>
            <h3 class="font-black text-xl text-slate-900 dark:text-white">
              {{ selectedCarForBooking?.car_name || selectedCarForBooking?.brand }} {{ selectedCarForBooking?.car_model || selectedCarForBooking?.model }}
            </h3>
          </div>
          <button
            type="button"
            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            @click="isBookingModalOpen = false"
          >
            <i class="ri-close-line text-2xl" />
          </button>
        </div>

        <!-- Vehicle Summary Card -->
        <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/40 text-xs">
          <img
            :src="getCarImage(selectedCarForBooking)"
            class="w-20 h-14 rounded-xl object-cover shadow-xs"
            alt="Car thumbnail"
          >
          <div class="flex-1 min-w-0 space-y-1">
            <div class="flex justify-between items-center">
              <span class="font-bold text-slate-900 dark:text-white truncate">Chauffeur: {{ selectedCarForBooking?.driver?.name || selectedCarForBooking?.driver_name || 'Assigned Chauffeur' }}</span>
              <span class="text-sm font-black text-blue-600 dark:text-blue-400">${{ selectedCarForBooking?.car_price_per_day }}/day</span>
            </div>
            <span class="text-[11px] text-slate-500 block">Plate: {{ selectedCarForBooking?.car_number }} &bull; {{ selectedCarForBooking?.number_of_seats }} Seats</span>
          </div>
        </div>

        <!-- Modal Error / Success Alerts -->
        <div
          v-if="modalError"
          class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-bold flex items-center gap-2"
        >
          <i class="ri-error-warning-fill text-lg text-rose-600 shrink-0" />
          <span>{{ modalError }}</span>
        </div>

        <div
          v-if="modalSuccess"
          class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2"
        >
          <i class="ri-checkbox-circle-fill text-lg text-emerald-600 shrink-0" />
          <span>{{ modalSuccess }}</span>
        </div>

        <!-- Overlap Alert Warning -->
        <div
          v-if="hasDateOverlap"
          class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-bold flex items-center gap-2"
        >
          <i class="ri-error-warning-fill text-lg text-rose-600 shrink-0" />
          <span>Booking Conflict Detected: This vehicle is already booked/reserved on the selected dates. Please pick available dates in the datepicker.</span>
        </div>
        <div
          v-else-if="bookingForm.pick_up_date && bookingForm.last_date && !modalSuccess"
          class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2"
        >
          <i class="ri-checkbox-circle-fill text-lg text-emerald-600 shrink-0" />
          <span>Dates Available: Zero booking conflicts. Ready for instant reservation!</span>
        </div>

        <!-- Booking Form Fields with AppDatePicker -->
        <form
          class="space-y-4"
          @submit.prevent="submitBooking"
        >
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                label="Return / Drop-off Date *"
                placeholder="Select return date..."
                :min-date="bookingForm.pick_up_date || 'today'"
                :booked-dates="formattedBookedDates"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                Pickup Location *
              </label>
              <input
                v-model="bookingForm.pickup_location"
                type="text"
                placeholder="e.g. Kathmandu City"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white"
              >
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                Drop Location *
              </label>
              <input
                v-model="bookingForm.drop_location"
                type="text"
                placeholder="e.g. Pokhara"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white"
              >
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
              Special Instructions / Notes (Optional)
            </label>
            <textarea
              v-model="bookingForm.note"
              rows="2"
              placeholder="e.g. Airport terminal pickup with luggage assistance"
              class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white"
            />
          </div>

          <!-- Total Calculation Summary -->
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
            <div>
              <span class="text-slate-400 block font-medium">Estimated Total ({{ calculatedDays }} Days)</span>
              <span class="text-xs text-emerald-600 font-bold">Includes Driver & Fuel</span>
            </div>
            <span class="text-2xl font-black text-blue-600 dark:text-blue-400">${{ calculatedTotal }}</span>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end gap-3 pt-2">
            <button
              type="button"
              class="px-4 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition-colors"
              @click="isBookingModalOpen = false"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="bookingSubmitting || hasDateOverlap"
              :class="hasDateOverlap ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/30 cursor-pointer'"
              class="px-6 py-2.5 rounded-xl text-xs font-black transition-all flex items-center gap-2"
            >
              <i
                v-if="bookingSubmitting"
                class="ri-loader-4-line animate-spin text-sm"
              />
              <span>{{ bookingSubmitting ? 'Confirming...' : 'Confirm & Reserve' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </CustomerLayout>
</template>


