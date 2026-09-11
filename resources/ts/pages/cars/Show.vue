<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'
import MessageBox from '@/components/MessageBox.vue'
import axios from 'axios'

const props = defineProps<{
  car: any
  disabledDates?: string[]
}>()

const page = usePage()
const authUser = computed(() => {
  const auth = (page.props.auth as any) || {}
  return auth.customer || auth.user || auth.admin || auth.owner || null
})
const isLoggedIn = computed(() => Boolean(authUser.value))

const carData = ref<any>(props.car)
const disabledDatesList = ref<string[]>(props.disabledDates || [])
const loading = ref(false)

// Booking form
const pickupLocation = ref('Kathmandu Central Hub')
const dropLocation = ref('Kathmandu Central Hub')
const pickupDate = ref('')
const returnDate = ref('')
const rentalPurpose = ref('Vacation / Personal Trip')

// Availability status
const checkingAvailability = ref(false)
const availabilityResult = ref<any>(null)
const availabilityError = ref('')

// Login prompt modal state
const showLoginPromptModal = ref(false)

// Payment modal state
const showPaymentModal = ref(false)
const processingPayment = ref(false)
const bookingSuccess = ref<any>(null)

const paymentForm = ref({
  cardholderName: '',
  cardNumber: '',
  expiryDate: '',
  cvv: '',
  paymentMethod: 'Credit Card',
})

// Fetch car via API on mount
const fetchCarDetails = async () => {
  loading.value = true
  try {
    const res = await axios.get(`/api/cars/${props.car.id}`)
    if (res.data.status === 'success') {
      carData.value = res.data.data.car
      disabledDatesList.value = res.data.data.disabled_dates || []
    }
  } catch (err) {
    console.error('Error fetching car via API:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCarDetails()
})

// Check calendar availability with overlap prevention algorithm
const checkCalendarAvailability = async () => {
  if (!pickupDate.value || !returnDate.value) return

  availabilityError.value = ''
  availabilityResult.value = null
  checkingAvailability.value = true

  try {
    const res = await axios.post('/api/bookings/check-availability', {
      car_id: carData.value.id,
      pick_up_date: pickupDate.value,
      last_date: returnDate.value,
    })

    if (res.data.status === 'success') {
      availabilityResult.value = res.data
      if (!res.data.available) {
        availabilityError.value = res.data.message
      }
    }
  } catch (err: any) {
    availabilityError.value = err.response?.data?.message || err.response?.data?.errors?.pick_up_date?.[0] || 'Selected dates are invalid or already booked.'
  } finally {
    checkingAvailability.value = false
  }
}

// Initiate Booking & open payment modal (requires authentication)
const proceedToPayment = () => {
  if (!isLoggedIn.value) {
    showLoginPromptModal.value = true
    return
  }

  if (!availabilityResult.value?.available) {
    availabilityError.value = 'Please select available dates before booking.'
    return
  }

  paymentForm.value.cardholderName = authUser.value.name || authUser.value.full_name || ''
  showPaymentModal.value = true
}

// Process booking and payment API
const submitBookingAndPayment = async () => {
  if (!paymentForm.value.cardNumber || !paymentForm.value.expiryDate || !paymentForm.value.cvv) {
    alert('Please enter all payment information.')
    return
  }

  processingPayment.value = true
  try {
    // 1. Create booking with overlap lock
    const bookingRes = await axios.post('/api/bookings/create', {
      car_id: carData.value.id,
      pickup_location: pickupLocation.value,
      drop_location: dropLocation.value,
      pick_up_date: pickupDate.value,
      last_date: returnDate.value,
      purpose: rentalPurpose.value,
    })

    if (bookingRes.data.status === 'success') {
      const createdBooking = bookingRes.data.data.booking

      // 2. Process payment
      const paymentRes = await axios.post('/api/bookings/payment', {
        booking_id: createdBooking.id,
        card_number: paymentForm.value.cardNumber,
        expiry_date: paymentForm.value.expiryDate,
        cvv: paymentForm.value.cvv,
        payment_method: paymentForm.value.paymentMethod,
      })

      if (paymentRes.data.status === 'success') {
        bookingSuccess.value = {
          booking: paymentRes.data.data.booking,
          payment: paymentRes.data.data.payment,
        }
      }
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Booking or Payment failed. Please check your details and try again.')
  } finally {
    processingPayment.value = false
  }
}
</script>

<template>
  <FrontendLayout>
    <Head :title="`${carData.car_name || ''} ${carData.car_model || ''} - Booking & Details`" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-gray-500">
        <Link
          href="/"
          class="hover:text-blue-600"
        >
          Home
        </Link>
        <span>/</span>
        <Link
          href="/cars"
          class="hover:text-blue-600"
        >
          Vehicles
        </Link>
        <span>/</span>
        <span class="text-gray-900 dark:text-white font-medium">{{ carData.car_name }} {{ carData.car_model }}</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Main Car, Owner & Driver Details (Left 2 cols) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Hero Car Image & Specs -->
          <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="relative h-80 sm:h-96 bg-slate-100 dark:bg-gray-800">
              <img
                :src="carData.car_photo ? '/' + carData.car_photo : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=1000&auto=format&fit=crop&q=80'"
                class="w-full h-full object-cover"
                :alt="carData.car_name"
              >
              <div class="absolute top-4 left-4 flex gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-500 text-white shadow-md">
                  {{ carData.available === 'yes' || carData.available === true ? 'Available' : 'Verified' }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-blue-600 text-white shadow-md">
                  Approved Fleet
                </span>
              </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h1 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white">
                    {{ carData.car_name }} {{ carData.car_model }}
                  </h1>
                  <p class="text-sm text-gray-500 font-mono mt-1">
                    Registration No: <span class="font-bold text-gray-700 dark:text-gray-300">{{ carData.car_number }}</span>
                  </p>
                </div>
                <div class="text-left sm:text-right p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/40">
                  <span class="text-3xl font-black text-blue-600 dark:text-blue-400">${{ carData.car_price_per_day }}</span>
                  <span class="text-xs text-gray-500 block">per day (24 hrs)</span>
                  <span class="text-[11px] font-semibold text-gray-400 mt-0.5 block">${{ carData.car_price_per_km }}/km extra</span>
                </div>
              </div>

              <!-- Key Specs Grid -->
              <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">
                  Vehicle Specifications
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                  <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                    <span class="text-xs text-gray-500 block">Seating Capacity</span>
                    <span class="text-base font-bold text-gray-900 dark:text-white">{{ carData.number_of_seats }} Passengers</span>
                  </div>
                  <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                    <span class="text-xs text-gray-500 block">Rate / Day</span>
                    <span class="text-base font-bold text-gray-900 dark:text-white">${{ carData.car_price_per_day }}</span>
                  </div>
                  <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                    <span class="text-xs text-gray-500 block">Rate / KM</span>
                    <span class="text-base font-bold text-gray-900 dark:text-white">${{ carData.car_price_per_km }}</span>
                  </div>
                  <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                    <span class="text-xs text-gray-500 block">Verification Status</span>
                    <span class="text-base font-bold text-emerald-600 capitalize">{{ carData.status }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Owner Details Card -->
          <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-lg">
                  {{ (carData.owner?.full_name || 'O')[0] }}
                </div>
                <div>
                  <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                    {{ carData.owner?.full_name || 'Verified Fleet Owner' }}
                  </h3>
                  <p class="text-xs text-gray-500">
                    Registered Car Fleet Partner
                  </p>
                </div>
              </div>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-400 dark:border-emerald-800">
                Verified Owner
              </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs">
              <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60">
                <span class="text-gray-400 block mb-0.5">Contact Number</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ carData.owner?.contact_number || 'Confidential (Provided upon booking)' }}</span>
              </div>
              <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60">
                <span class="text-gray-400 block mb-0.5">Email</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ carData.owner?.email || 'owner@fleet.com' }}</span>
              </div>
              <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60">
                <span class="text-gray-400 block mb-0.5">Location</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ carData.owner?.address || 'Kathmandu, Nepal' }}</span>
              </div>
            </div>
          </div>

          <!-- Driver Details Card (from drivers table) -->
          <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-lg overflow-hidden">
                  <img
                    v-if="carData.driver?.photo || carData.driver_photo"
                    :src="'/' + (carData.driver?.photo || carData.driver_photo)"
                    class="w-full h-full object-cover"
                  >
                  <span v-else>{{ (carData.driver?.name || carData.driver_name || 'D')[0] }}</span>
                </div>
                <div>
                  <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                    Driver: {{ carData.driver?.name || carData.driver_name || 'Dedicated Fleet Driver' }}
                  </h3>
                  <p class="text-xs text-gray-500">
                    Professional Licensed Chauffeur &bull; {{ carData.driver?.experience_years || carData.driving_experience || '3+' }} Years Experience
                  </p>
                </div>
              </div>
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950 dark:text-blue-400 dark:border-blue-800">
                Active & Certified
              </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs">
              <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60">
                <span class="text-gray-400 block mb-0.5">Driver Contact</span>
                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ carData.driver?.phone || carData.driver_number || 'Direct contact upon reservation' }}</span>
              </div>
              <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60">
                <span class="text-gray-400 block mb-0.5">License Number</span>
                <span class="font-semibold font-mono text-gray-900 dark:text-gray-100">{{ carData.driver?.license_number || 'DL-VERIFIED-9821' }}</span>
              </div>
              <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60">
                <span class="text-gray-400 block mb-0.5">Driver Status</span>
                <span class="font-semibold text-emerald-600 capitalize">{{ carData.driver?.status || 'Active' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Sticky Reservation & Calendar Box (Right col) -->
        <div class="space-y-6 sticky top-24">
          <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xl space-y-6">
            <div>
              <h3 class="font-bold text-xl text-gray-900 dark:text-white">
                Reserve Car & Schedule
              </h3>
              <p class="text-xs text-gray-500 mt-0.5">
                Overlap Protection: Real-time calendar conflict check
              </p>
            </div>

            <!-- Login Prompt Banner when Guest -->
            <div
              v-if="!isLoggedIn"
              class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-xs space-y-2.5"
            >
              <div class="flex items-center gap-2 font-bold text-amber-800 dark:text-amber-300">
                <i class="ri-information-fill text-base text-amber-600 dark:text-amber-400" />
                <span>Login Required to Book</span>
              </div>
              <p class="text-amber-700 dark:text-amber-400 leading-relaxed">
                Please log in to your account before reserving this vehicle and completing payment.
              </p>
              <div class="flex items-center gap-2 pt-1">
                <Link
                  :href="`/customer/login?redirect=/cars/${carData.id}`"
                  class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1 cursor-pointer"
                >
                  <i class="ri-login-box-line text-xs" />
                  <span>Login / Sign In</span>
                </Link>
                <Link
                  href="/customer/register"
                  class="px-3.5 py-1.5 rounded-xl bg-white dark:bg-gray-900 border border-amber-300 dark:border-amber-700/80 text-amber-900 dark:text-amber-200 font-bold text-xs hover:bg-amber-100/50 transition-all cursor-pointer"
                >
                  Register
                </Link>
              </div>
            </div>

            <!-- Unavailable dates indicator -->
            <div
              v-if="disabledDatesList.length > 0"
              class="p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-900/50 text-xs"
            >
              <div class="flex items-center justify-between mb-2">
                <span class="font-bold text-rose-800 dark:text-rose-300 flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse" />
                  Reserved Dates (Unavailable)
                </span>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-200/80 dark:bg-rose-900 text-rose-800 dark:text-rose-200">
                  {{ disabledDatesList.length }} Days
                </span>
              </div>
              <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto pe-1">
                <span
                  v-for="d in disabledDatesList"
                  :key="d"
                  class="px-2 py-1 rounded-lg bg-white dark:bg-rose-900/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-[11px] font-mono font-semibold shadow-xs"
                >
                  {{ d }}
                </span>
              </div>
            </div>

            <form
              class="space-y-4"
              @submit.prevent="proceedToPayment"
            >
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Pickup Location</label>
                <input
                  v-model="pickupLocation"
                  type="text"
                  required
                  class="w-full px-3.5 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Drop-off Location</label>
                <input
                  v-model="dropLocation"
                  type="text"
                  required
                  class="w-full px-3.5 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="min-w-0">
                  <AppDatePicker
                    v-model="pickupDate"
                    label="Pickup Date"
                    required
                    placeholder="Select pickup date"
                    min-date="today"
                    :disabled-dates="disabledDatesList"
                    @change="checkCalendarAvailability"
                  />
                </div>
                <div class="min-w-0">
                  <AppDatePicker
                    v-model="returnDate"
                    label="Return Date"
                    required
                    placeholder="Select return date"
                    :min-date="pickupDate || 'today'"
                    :disabled-dates="disabledDatesList"
                    @change="checkCalendarAvailability"
                  />
                </div>
              </div>

              <!-- Overlap Conflict Warning -->
              <MessageBox
                v-if="availabilityError"
                :message="availabilityError"
                type="error"
                @close="availabilityError = ''"
              />

              <!-- Calculation Result (if available) -->
              <div
                v-if="availabilityResult?.available"
                class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs space-y-2"
              >
                <div class="flex justify-between items-center text-emerald-800 dark:text-emerald-300 font-bold">
                  <span>✓ Dates Available!</span>
                  <span>{{ availabilityResult.days }} Day(s)</span>
                </div>
                <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                  <span>${{ carData.car_price_per_day }} × {{ availabilityResult.days }} days</span>
                  <span class="text-base font-black text-gray-900 dark:text-white">${{ availabilityResult.total_price }}</span>
                </div>
              </div>

              <!-- Booking Submit Button -->
              <button
                type="submit"
                :disabled="checkingAvailability || (availabilityResult && !availabilityResult.available)"
                class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-xl shadow-blue-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2 cursor-pointer"
              >
                <span v-if="checkingAvailability">Checking Calendar Overlaps...</span>
                <span
                  v-else-if="!isLoggedIn"
                  class="flex items-center gap-1.5"
                >
                  <i class="ri-login-box-line" />
                  <span>Login to Book & Pay (${{ availabilityResult?.total_price || carData.car_price_per_day }})</span>
                </span>
                <span v-else>Book Now & Pay (${{ availabilityResult?.total_price || carData.car_price_per_day }})</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Login Required Before Booking Modal -->
    <div
      v-if="showLoginPromptModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto"
    >
      <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl max-w-md w-full p-6 sm:p-8 space-y-6 relative animate-in fade-in zoom-in-95 duration-200">
        <button
          class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold cursor-pointer"
          @click="showLoginPromptModal = false"
        >
          &times;
        </button>

        <div class="text-center space-y-3">
          <div class="w-16 h-16 rounded-3xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-3xl mx-auto shadow-xs">
            <i class="ri-lock-line" />
          </div>

          <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
            Please Login Before Booking
          </h3>

          <p class="text-xs sm:text-sm text-gray-500 leading-relaxed max-w-xs mx-auto">
            You must be signed in to your customer account to reserve <strong>{{ carData.car_name }} {{ carData.car_model }}</strong> and complete payment.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 text-xs space-y-2">
          <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
            <span>Selected Vehicle:</span>
            <span class="font-bold text-gray-900 dark:text-white">{{ carData.car_name }} {{ carData.car_model }}</span>
          </div>
          <div
            v-if="availabilityResult?.total_price"
            class="flex items-center justify-between text-gray-600 dark:text-gray-300"
          >
            <span>Total Estimate:</span>
            <span class="font-black text-blue-600 dark:text-blue-400 text-sm">${{ availabilityResult.total_price }}</span>
          </div>
        </div>

        <div class="flex flex-col gap-2.5 pt-2">
          <Link
            :href="`/customer/login?redirect=/cars/${carData.id}`"
            class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm text-center shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer"
          >
            <i class="ri-login-box-line" />
            <span>Sign In to Continue Booking</span>
          </Link>

          <Link
            href="/customer/register"
            class="w-full py-3 px-4 rounded-2xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold text-xs text-center transition-all cursor-pointer"
          >
            Don't have an account? Register
          </Link>

          <button
            type="button"
            class="w-full py-2 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors cursor-pointer"
            @click="showLoginPromptModal = false"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- Integrated Payment & Checkout Modal (Only opens if authenticated) -->
    <div
      v-if="showPaymentModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto"
    >
      <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl max-w-md w-full p-6 sm:p-8 space-y-6 relative">
        <button
          class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold cursor-pointer"
          @click="showPaymentModal = false"
        >
          &times;
        </button>

        <!-- Payment Success State -->
        <div
          v-if="bookingSuccess"
          class="text-center space-y-4 py-4"
        >
          <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mx-auto">
            ✓
          </div>
          <h3 class="text-2xl font-black text-gray-900 dark:text-white">
            Booking & Payment Confirmed!
          </h3>
          <p class="text-xs text-gray-500 max-w-xs mx-auto">
            Your rental for <strong>{{ carData.car_name }} {{ carData.car_model }}</strong> has been officially locked and confirmed.
          </p>

          <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800 text-left text-xs space-y-1.5 font-mono">
            <div class="flex justify-between">
              <span class="text-gray-400">Booking ID:</span>
              <span class="font-bold text-gray-900 dark:text-white">#{{ bookingSuccess.booking.id }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Dates:</span>
              <span>{{ bookingSuccess.booking.pick_up_date }} &rarr; {{ bookingSuccess.booking.last_date }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Paid Amount:</span>
              <span class="font-bold text-emerald-600">${{ bookingSuccess.booking.total_price }}</span>
            </div>
          </div>

          <div class="flex flex-col gap-2 pt-2">
            <Link
              href="/customer/dashboard"
              class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs text-center cursor-pointer"
            >
              Go to Customer Dashboard
            </Link>
            <button
              type="button"
              class="w-full py-2 text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer"
              @click="showPaymentModal = false"
            >
              Close
            </button>
          </div>
        </div>

        <!-- Payment Form -->
        <div
          v-else
          class="space-y-4"
        >
          <div>
            <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-1">
              <span>🔒 Secure Checkout</span>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white">
              Complete Payment
            </h3>
            <p class="text-xs text-gray-500">
              Total to charge: <strong class="text-base text-blue-600">${{ availabilityResult?.total_price || carData.car_price_per_day }}</strong>
            </p>
          </div>

          <!-- Payment methods tabs -->
          <div class="grid grid-cols-2 gap-2">
            <button
              type="button"
              :class="paymentForm.paymentMethod === 'Credit Card' ? 'border-blue-600 bg-blue-50/50 text-blue-700' : 'border-gray-200 text-gray-600'"
              class="py-2.5 px-3 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer"
              @click="paymentForm.paymentMethod = 'Credit Card'"
            >
              💳 Card / Stripe
            </button>
            <button
              type="button"
              :class="paymentForm.paymentMethod === 'Digital' ? 'border-blue-600 bg-blue-50/50 text-blue-700' : 'border-gray-200 text-gray-600'"
              class="py-2.5 px-3 rounded-xl border text-xs font-bold transition-all text-center cursor-pointer"
              @click="paymentForm.paymentMethod = 'Digital'"
            >
              📱 Digital Wallet
            </button>
          </div>

          <form
            class="space-y-3"
            @submit.prevent="submitBookingAndPayment"
          >
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Cardholder Name</label>
              <input
                v-model="paymentForm.cardholderName"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="John Doe"
              >
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Card Number</label>
              <input
                v-model="paymentForm.cardNumber"
                type="text"
                required
                maxlength="19"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="4242 •••• •••• 4242"
              >
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Expiry Date</label>
                <input
                  v-model="paymentForm.expiryDate"
                  type="text"
                  required
                  placeholder="MM/YY"
                  maxlength="5"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">CVV</label>
                <input
                  v-model="paymentForm.cvv"
                  type="password"
                  required
                  placeholder="•••"
                  maxlength="4"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
              </div>
            </div>

            <button
              type="submit"
              :disabled="processingPayment"
              class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-xl shadow-blue-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2 mt-4 cursor-pointer"
            >
              <span v-if="processingPayment">Processing Secure Payment...</span>
              <span v-else>Pay ${{ availabilityResult?.total_price || carData.car_price_per_day }} & Lock Reservation</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>
