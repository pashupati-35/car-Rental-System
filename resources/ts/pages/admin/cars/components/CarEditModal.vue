<script setup lang="ts">
import { ref, watch } from 'vue'
import type { CarItem } from '../types'

const props = defineProps<{
  show: boolean
  car: CarItem | null
  owners?: Array<any>
  drivers?: Array<any>
  submitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', formData: FormData): void
}>()

const form = ref<{
  car_name: string
  car_model: string
  car_number: string
  number_of_seats: number | string
  car_price_per_day: number | string
  car_price_per_km: number | string
  fuel_type: string
  transmission: string
  owner_id: number | string
  driver_id: number | string
  status: string
  available: string
  description: string
}>({
  car_name: '',
  car_model: '',
  car_number: '',
  number_of_seats: 4,
  car_price_per_day: '',
  car_price_per_km: '',
  fuel_type: 'Petrol',
  transmission: 'Automatic',
  owner_id: '',
  driver_id: '',
  status: 'pending',
  available: 'no',
  description: '',
})

const carPhotoFile = ref<File | null>(null)
const carPhotoPreview = ref<string | null>(null)
const blueBookPhotoFile = ref<File | null>(null)
const blueBookPhotoPreview = ref<string | null>(null)

watch(
  () => props.car,
  newCar => {
    if (newCar) {
      form.value = {
        car_name: newCar.car_name || newCar.brand || '',
        car_model: newCar.car_model || newCar.model || '',
        car_number: newCar.car_number || newCar.plate_number || '',
        number_of_seats: newCar.number_of_seats || newCar.seating_capacity || 4,
        car_price_per_day: newCar.car_price_per_day || newCar.price_per_day || newCar.rental_price || '',
        car_price_per_km: newCar.car_price_per_km || '',
        fuel_type: newCar.fuel_type || 'Petrol',
        transmission: newCar.transmission || 'Automatic',
        owner_id: newCar.owner_id || newCar.owner?.id || '',
        driver_id: newCar.driver_id || newCar.driver?.id || '',
        status: newCar.status || 'pending',
        available: newCar.available || 'no',
        description: newCar.description || '',
      }
      carPhotoPreview.value = newCar.image || newCar.car_photo || null
      blueBookPhotoPreview.value = newCar.blue_book_photo || null
      carPhotoFile.value = null
      blueBookPhotoFile.value = null
    }
  },
  { immediate: true },
)

const handleCarPhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]

    carPhotoFile.value = file
    carPhotoPreview.value = URL.createObjectURL(file)
  }
}

const handleBlueBookPhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]

    blueBookPhotoFile.value = file
    blueBookPhotoPreview.value = URL.createObjectURL(file)
  }
}

const handleSubmit = () => {
  const data = new FormData()

  data.append('car_name', form.value.car_name)
  data.append('car_model', form.value.car_model)
  data.append('car_number', form.value.car_number)
  data.append('number_of_seats', String(form.value.number_of_seats))
  data.append('car_price_per_day', String(form.value.car_price_per_day || 0))
  data.append('car_price_per_km', String(form.value.car_price_per_km || 0))
  data.append('fuel_type', form.value.fuel_type)
  data.append('transmission', form.value.transmission)

  if (form.value.owner_id) {
    data.append('owner_id', String(form.value.owner_id))
  }

  if (form.value.driver_id) {
    data.append('driver_id', String(form.value.driver_id))
  }

  data.append('status', form.value.status)
  data.append('available', form.value.available)
  data.append('description', form.value.description)

  if (carPhotoFile.value) {
    data.append('car_photo', carPhotoFile.value)
  }

  if (blueBookPhotoFile.value) {
    data.append('blue_book_photo', blueBookPhotoFile.value)
  }

  emit('save', data)
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-5 my-8 max-h-[90vh] overflow-y-auto">
      <!-- Modal Header -->
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
            <i class="ri-edit-2-line text-lg" />
          </div>
          <div>
            <h3 class="font-bold text-base text-slate-900 dark:text-white">
              Edit Vehicle Specifications & Status
            </h3>
            <p class="text-[11px] text-slate-500">
              Update fleet vehicle data, verification status, rates, and driver assignments.
            </p>
          </div>
        </div>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 text-2xl cursor-pointer p-1"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <!-- Error message banner -->
      <div
        v-if="errorMessage"
        class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-800 dark:text-rose-200 text-xs font-semibold border border-rose-200 dark:border-rose-800 flex items-center gap-2"
      >
        <i class="ri-error-warning-line text-rose-500 text-base shrink-0" />
        <span>{{ errorMessage }}</span>
      </div>

      <form
        class="space-y-4 text-xs"
        @submit.prevent="handleSubmit"
      >
        <!-- Vehicle Name & Model -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Car Brand / Name *</label>
            <input
              v-model="form.car_name"
              type="text"
              required
              placeholder="e.g. Toyota Land Cruiser"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>

          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Model / Trim *</label>
            <input
              v-model="form.car_model"
              type="text"
              required
              placeholder="e.g. Prado VX 2.8L"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>
        </div>

        <!-- License Plate & Seats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">License Plate / Car Number *</label>
            <input
              v-model="form.car_number"
              type="text"
              required
              placeholder="e.g. BA-17-PA-8782"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>

          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Seating Capacity *</label>
            <input
              v-model="form.number_of_seats"
              type="number"
              min="1"
              max="60"
              required
              placeholder="e.g. 5"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>
        </div>

        <!-- Rates: Daily & KM -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Daily Rental Price ($) *</label>
            <input
              v-model="form.car_price_per_day"
              type="number"
              step="0.01"
              min="0"
              required
              placeholder="e.g. 150.00"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>

          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Rate per KM ($) (Optional)</label>
            <input
              v-model="form.car_price_per_km"
              type="number"
              step="0.01"
              min="0"
              placeholder="e.g. 1.25"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>
        </div>

        <!-- Fuel & Transmission -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Fuel Type</label>
            <select
              v-model="form.fuel_type"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
              <option value="Petrol">
                Petrol
              </option>
              <option value="Diesel">
                Diesel
              </option>
              <option value="Electric">
                Electric
              </option>
              <option value="Hybrid">
                Hybrid
              </option>
              <option value="CNG">
                CNG
              </option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Transmission</label>
            <select
              v-model="form.transmission"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
              <option value="Automatic">
                Automatic
              </option>
              <option value="Manual">
                Manual
              </option>
            </select>
          </div>
        </div>

        <!-- Owner & Driver Assignment -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Fleet Owner</label>
            <select
              v-model="form.owner_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
              <option value="">
                Platform / Direct Fleet
              </option>
              <option
                v-for="owner in owners"
                :key="owner.id"
                :value="owner.id"
              >
                {{ owner.full_name || owner.name }} ({{ owner.email || owner.contact_number }})
              </option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Assigned Driver</label>
            <select
              v-model="form.driver_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
              <option value="">
                None / Self-Drive
              </option>
              <option
                v-for="driver in drivers"
                :key="driver.id"
                :value="driver.id"
              >
                {{ driver.name }} ({{ driver.phone }})
              </option>
            </select>
          </div>
        </div>

        <!-- Status & Availability -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-3.5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50">
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Admin Approval Status *</label>
            <select
              v-model="form.status"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold"
            >
              <option value="pending">
                Pending Review
              </option>
              <option value="verified">
                Verified & Approved
              </option>
              <option value="rejected">
                Rejected
              </option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Booking Availability</label>
            <select
              v-model="form.available"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold"
            >
              <option value="yes">
                Available for Rent
              </option>
              <option value="no">
                Unavailable / Off Market
              </option>
            </select>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Vehicle Description / Highlights</label>
          <textarea
            v-model="form.description"
            rows="2"
            placeholder="Vehicle condition, safety features, GPS, Bluetooth, etc..."
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
          />
        </div>

        <!-- Photos Upload: Car Image & Blue Book -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Car Main Photo</label>
            <div class="flex items-center gap-3">
              <div
                v-if="carPhotoPreview"
                class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0"
              >
                <img
                  :src="carPhotoPreview"
                  class="w-full h-full object-cover"
                >
              </div>
              <input
                type="file"
                accept="image/*"
                class="text-xs text-slate-500 file:me-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                @change="handleCarPhotoChange"
              >
            </div>
          </div>

          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Blue Book Photo</label>
            <div class="flex items-center gap-3">
              <div
                v-if="blueBookPhotoPreview"
                class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0"
              >
                <img
                  :src="blueBookPhotoPreview"
                  class="w-full h-full object-cover"
                >
              </div>
              <input
                type="file"
                accept="image/*"
                class="text-xs text-slate-500 file:me-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                @change="handleBlueBookPhotoChange"
              >
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer transition-colors"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 cursor-pointer flex items-center gap-2 transition-all disabled:opacity-60"
          >
            <span
              v-if="submitting"
              class="animate-spin"
            >⏳</span>
            <span>{{ submitting ? 'Updating Vehicle...' : 'Save Vehicle Changes' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
