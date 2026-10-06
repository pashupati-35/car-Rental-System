<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import axios from 'axios'

const props = defineProps<{
  booking: any
}>()

const isSubmitting = ref(false)
const errorMessage = ref('')
const isSuccess = ref(false)

const paymentForm = useForm({
  booking_id: props.booking?.id,
  customer_name: props.booking?.customer?.name || props.booking?.customer?.full_name || '',
  phone_number: props.booking?.customer?.contact_number || '',
  cvv: '',
})

onMounted(() => {
  // Generate random 3-digit CVV
  paymentForm.cvv = String(Math.floor(100 + Math.random() * 900))
})

const handlePayment = async () => {
  errorMessage.value = ''
  isSubmitting.value = true

  try {
    const res = await axios.post('/payment/process', {
      booking_id: paymentForm.booking_id,
      customer_name: paymentForm.customer_name,
      phone_number: paymentForm.phone_number,
      cvv: paymentForm.cvv,
    })

    if (res.data?.success) {
      window.location.href = `/payment/confirmation/${props.booking.id}`
    } else {
      errorMessage.value = res.data?.message || 'Payment processing failed.'
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || err.message || 'Payment processing failed.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <FrontendLayout>
    <Head title="Booking Payment" />

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10 sm:py-16">
      <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200/80 dark:border-gray-800 shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="p-6 sm:p-8 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-blue-50/50 via-indigo-50/30 to-purple-50/20 dark:from-gray-900 dark:to-gray-850">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-500/25">
              <i class="ri-secure-payment-fill" />
            </div>
            <div>
              <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">
                Payment Details for Booking
              </h1>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Reference ID: #{{ booking?.id }} &bull; Vehicle: {{ booking?.car?.car_name }} {{ booking?.car?.car_model }}
              </p>
            </div>
          </div>
        </div>

        <div class="p-6 sm:p-8">
          <div
            v-if="errorMessage"
            class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-900 text-red-600 dark:text-red-300 text-xs font-medium flex items-center gap-2"
          >
            <i class="ri-error-warning-fill text-base" />
            <span>{{ errorMessage }}</span>
          </div>

          <form
            class="space-y-5"
            @submit.prevent="handlePayment"
          >
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                  Customer Name *
                </label>
                <input
                  v-model="paymentForm.customer_name"
                  type="text"
                  required
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
              </div>

              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                  Contact Phone *
                </label>
                <input
                  v-model="paymentForm.phone_number"
                  type="text"
                  required
                  maxlength="14"
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono"
                  placeholder="+977 98XXXXXXXX"
                >
              </div>
            </div>

            <!-- Booking Summary Card -->
            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/60 grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
              <div>
                <span class="text-gray-400 block text-[11px]">Selected Vehicle</span>
                <span class="font-bold text-gray-900 dark:text-white mt-0.5 block truncate">
                  {{ booking?.car?.car_name }} {{ booking?.car?.car_model }}
                </span>
              </div>
              <div>
                <span class="text-gray-400 block text-[11px]">Plate Number</span>
                <span class="font-mono font-bold text-gray-900 dark:text-white mt-0.5 block">
                  {{ booking?.car?.car_number }}
                </span>
              </div>
              <div class="col-span-2 sm:col-span-1">
                <span class="text-gray-400 block text-[11px]">Total Payable</span>
                <span class="text-lg font-black text-blue-600 dark:text-blue-400 block">
                  ${{ booking?.total_price }}
                </span>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                Simulated CVV (Demo Mode)
              </label>
              <input
                v-model="paymentForm.cvv"
                type="text"
                readonly
                class="w-full sm:w-48 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-sm font-mono tracking-widest text-center"
              >
              <span class="text-[11px] text-gray-400 mt-1 block">Pre-generated security code for direct test checkout.</span>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center gap-3">
              <button
                v-if="booking?.status !== 'booked'"
                type="submit"
                :disabled="isSubmitting"
                class="w-full sm:w-auto px-8 py-3 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
              >
                <i
                  v-if="isSubmitting"
                  class="ri-loader-4-line animate-spin"
                />
                <i
                  v-else
                  class="ri-check-line text-sm"
                />
                <span>{{ isSubmitting ? 'Processing Payment...' : 'Confirm & Complete Payment' }}</span>
              </button>

              <Link
                :href="`/cars/${booking?.car_id}`"
                class="w-full sm:w-auto px-6 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-semibold text-xs text-center hover:bg-gray-50 dark:hover:bg-gray-800 transition-all"
              >
                Inspect Vehicle Details
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>
