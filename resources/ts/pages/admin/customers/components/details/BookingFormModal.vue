<script setup lang="ts">
import type { CustomerBookingItem } from '../../types'
import AppDatePicker from '@/components/AppDatePicker.vue'
import MessageBox from '@/components/MessageBox.vue'

const props = defineProps<{
  show: boolean
  isEditing: boolean
  availableCars: Array<any>
  submitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save'): void
}>()

const bookingForm = defineModel<Partial<CustomerBookingItem>>('bookingForm', { required: true })

const onCarSelect = () => {
  if (!props.isEditing && bookingForm.value.car_id) {
    const car = props.availableCars.find((c: any) => c.id === bookingForm.value.car_id)
    if (car && car.price_per_day) {
      bookingForm.value.total_price = Number(car.price_per_day)
    }
  }
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-calendar-check-line text-indigo-600" />
          <span>{{ isEditing ? 'Edit Customer Rental Booking' : 'Book Vehicle for Customer' }}</span>
        </h3>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <MessageBox
        :message="errorMessage"
        type="error"
      />

      <form
        class="space-y-4 text-xs"
        @submit.prevent="emit('save')"
      >
        <!-- Car Selection -->
        <div>
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Select Vehicle *</label>
          <select
            v-model="bookingForm.car_id"
            required
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-medium text-xs focus:ring-2 focus:ring-indigo-500"
            @change="onCarSelect"
          >
            <option
              value=""
              disabled
            >
              Choose an active vehicle...
            </option>
            <option
              v-for="car in availableCars"
              :key="car.id"
              :value="car.id"
            >
              {{ car.car_name || car.brand }} {{ car.car_model || car.model }} &bull; ${{ car.price_per_day || car.rental_price }}/day (Plate: {{ car.car_number || car.plate_number || 'N/A' }})
            </option>
          </select>
        </div>

        <!-- Dates using AppDatePicker -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <AppDatePicker
              v-model="bookingForm.pick_up_date"
              label="Pickup Date *"
              placeholder="Select pickup date..."
              min-date="today"
            />
          </div>
          <div>
            <AppDatePicker
              v-model="bookingForm.last_date"
              label="Return / Drop-off Date *"
              placeholder="Select drop-off date..."
              :min-date="bookingForm.pick_up_date || 'today'"
            />
          </div>
        </div>

        <!-- Locations -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Pickup Location *</label>
            <input
              v-model="bookingForm.pickup_location"
              type="text"
              required
              placeholder="e.g. LAX Terminal 2 or Downtown Hub"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
            >
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Drop-off Location *</label>
            <input
              v-model="bookingForm.drop_location"
              type="text"
              required
              placeholder="e.g. Same Location or Airport Drop"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
            >
          </div>
        </div>

        <!-- Price & Status -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Agreed Total Price ($) *</label>
            <input
              v-model="bookingForm.total_price"
              type="number"
              step="0.01"
              required
              placeholder="150.00"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono font-bold text-xs focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Booking Status</label>
            <select
              v-model="bookingForm.status"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold text-xs focus:ring-2 focus:ring-indigo-500"
            >
              <option value="confirm">
                Confirmed / Active
              </option>
              <option value="pending">
                Pending Review
              </option>
              <option value="completed">
                Completed Trip
              </option>
              <option value="cancel">
                Cancelled
              </option>
            </select>
          </div>
        </div>

        <div>
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Rental Purpose / Notes</label>
          <input
            v-model="bookingForm.purpose"
            type="text"
            placeholder="e.g. Business Travel, Vacation, Wedding..."
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
          >
        </div>

        <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer"
          >
            {{ submitting ? 'Saving...' : (isEditing ? 'Save Booking Changes' : 'Confirm & Create Booking') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
