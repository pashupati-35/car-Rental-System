<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const availableDrivers = ref<Array<any>>([])

const form = useForm({
  car_name: '',
  car_model: '',
  car_number_part1: 'BA',
  car_number_part2: '1',
  car_number_part3: 'PA',
  car_number_part4: '',
  number_of_seats: 5,
  car_price_per_km: 15,
  car_price_per_day: 65,
  available: 'yes',
  driver_id: '' as any,
  driver_name: '',
  driver_number: '',
  driving_experience: '3',
  car_photo: null as File | null,
  blue_book_photo: null as File | null,
})

const fetchDrivers = async () => {
  try {
    const res = await axios.get('/api/owner/drivers')
    if (res.data.status === 'success') {
      availableDrivers.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to load drivers for assignment:', err)
  }
}

onMounted(() => {
  fetchDrivers()
})

const onDriverSelected = () => {
  const selected = availableDrivers.value.find(d => d.id == form.driver_id)
  if (selected) {
    form.driver_name = selected.name
    form.driver_number = selected.phone
    form.driving_experience = selected.experience_years
  }
}

const submit = () => {
  form.post('/owner/cars', {
    forceFormData: true,
  })
}
</script>

<template>
  <AppLayout>
    <Head title="Owner - Register New Car" />

    <div class="max-w-4xl mx-auto space-y-6 py-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            Register New Vehicle
          </h1>
          <p class="text-xs text-gray-500 mt-0.5">
            Add a car to your fleet, set day & km pricing, and assign a dedicated driver
          </p>
        </div>
        <Link
          href="/owner/cars"
          class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          &larr; Back to Cars
        </Link>
      </div>

      <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form class="space-y-6" @submit.prevent="submit">
          <!-- Car Specifications -->
          <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-3">Vehicle Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Make / Brand *</label>
                <input
                  v-model="form.car_name"
                  type="text"
                  required
                  placeholder="e.g. Hyundai, Toyota"
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Model *</label>
                <input
                  v-model="form.car_model"
                  type="text"
                  required
                  placeholder="e.g. Creta, Fortuner"
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                />
              </div>
            </div>
          </div>

          <!-- Number plate and seats -->
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Zone / State</label>
              <input
                v-model="form.car_number_part1"
                type="text"
                required
                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-mono"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Lot / Series</label>
              <input
                v-model="form.car_number_part2"
                type="text"
                required
                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-mono"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Vehicle Symbol</label>
              <input
                v-model="form.car_number_part3"
                type="text"
                required
                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-mono"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Registration No *</label>
              <input
                v-model="form.car_number_part4"
                type="text"
                required
                placeholder="1234"
                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              />
            </div>
          </div>

          <!-- Pricing & Seats -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Price Per Day ($) *</label>
              <input
                v-model="form.car_price_per_day"
                type="number"
                step="0.01"
                required
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Price Per KM ($) *</label>
              <input
                v-model="form.car_price_per_km"
                type="number"
                step="0.01"
                required
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Seats Capacity *</label>
              <input
                v-model="form.number_of_seats"
                type="number"
                required
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              />
            </div>
          </div>

          <!-- Driver Assignment (from dedicated Drivers Table) -->
          <div class="p-5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                  Assign Driver from Driver Roster
                </h3>
                <p class="text-[11px] text-gray-500">
                  Select an existing driver or define chauffeur details
                </p>
              </div>
              <Link href="/owner/drivers" class="text-xs font-bold text-emerald-600 hover:underline">
                Manage Driver Roster &rarr;
              </Link>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Select Registered Driver</label>
              <select
                v-model="form.driver_id"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                @change="onDriverSelected"
              >
                <option value="">-- Assign a driver from your roster --</option>
                <option v-for="d in availableDrivers" :key="d.id" :value="d.id">
                  {{ d.name }} (Lic: {{ d.license_number }}, {{ d.experience_years }} yrs exp)
                </option>
              </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Driver Name</label>
                <input
                  v-model="form.driver_name"
                  type="text"
                  placeholder="Driver Full Name"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Driver Phone</label>
                <input
                  v-model="form.driver_number"
                  type="text"
                  placeholder="Driver Phone Number"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Experience (Years)</label>
                <input
                  v-model="form.driving_experience"
                  type="text"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs"
                />
              </div>
            </div>
          </div>

          <!-- Car Photo Upload -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Car Photo *</label>
              <input
                type="file"
                accept="image/*"
                class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                @input="form.car_photo = ($event.target as HTMLInputElement).files?.[0] || null"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Blue Book Document</label>
              <input
                type="file"
                accept="image/*,.pdf"
                class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                @input="form.blue_book_photo = ($event.target as HTMLInputElement).files?.[0] || null"
              />
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
            <button
              type="submit"
              :disabled="form.processing"
              class="py-3 px-8 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-xl shadow-emerald-500/25 disabled:opacity-50 transition-all"
            >
              <span v-if="form.processing">Saving Vehicle...</span>
              <span v-else>Register & Submit for Verification</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
