<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps<{
  car: any
}>()

const page = usePage()
const auth = page.props.auth as any

const form = useForm({
  car_id: props.car.id,
  start_date: '',
  end_date: '',
  purpose: 'Personal Trip',
})

const submitBooking = () => {
  form.post('/customer/bookings')
}
</script>

<template>
  <FrontendLayout>
    <Head :title="`${car.brand} ${car.model}`" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Car Details Left/Center -->
        <div class="lg:col-span-2 space-y-6">
          <img
            :src="car.image ? '/' + car.image : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&auto=format&fit=crop&q=80'"
            class="w-full h-80 sm:h-96 object-cover rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800"
            :alt="car.model"
          />

          <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
            <div class="flex items-start justify-between">
              <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ car.brand }} {{ car.model }}</h1>
                <p class="text-sm text-gray-500 font-mono">{{ car.car_number || car.plate_number }} &bull; {{ car.year || '2024' }}</p>
              </div>
              <div class="text-right">
                <span class="text-2xl font-black text-blue-600">${{ car.price_per_day || car.rate || 0 }}</span>
                <span class="text-xs text-gray-500 block">/ day</span>
              </div>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
              <h3 class="font-bold text-base text-gray-900 dark:text-white mb-2">Specifications</h3>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                  <span class="text-gray-500 block">Transmission</span>
                  <span class="font-semibold text-gray-900 dark:text-white">{{ car.transmission || 'Automatic' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                  <span class="text-gray-500 block">Fuel Type</span>
                  <span class="font-semibold text-gray-900 dark:text-white">{{ car.fuel_type || 'Petrol' }}</span>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                  <span class="text-gray-500 block">Seats</span>
                  <span class="font-semibold text-gray-900 dark:text-white">{{ car.seats || 5 }} Persons</span>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                  <span class="text-gray-500 block">Status</span>
                  <span class="font-semibold text-emerald-600 capitalize">{{ car.status || 'Available' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Sidebar -->
        <div>
          <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-lg sticky top-24 space-y-5">
            <h3 class="font-bold text-xl text-gray-900 dark:text-white">Reserve this Vehicle</h3>

            <div v-if="!auth?.customer && !auth?.admin && !auth?.owner" class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-xs">
              Please <Link href="/customer/login" class="font-bold underline">sign in</Link> to reserve this vehicle.
            </div>

            <form @submit.prevent="submitBooking" class="space-y-4">
              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Pickup Date</label>
                <input
                  v-model="form.start_date"
                  type="date"
                  required
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Return Date</label>
                <input
                  v-model="form.end_date"
                  type="date"
                  required
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Rental Purpose</label>
                <input
                  v-model="form.purpose"
                  type="text"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
              </div>

              <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-500/25 disabled:opacity-50 transition-all"
              >
                Confirm Reservation
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>
