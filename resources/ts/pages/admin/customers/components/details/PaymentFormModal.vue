<script setup lang="ts">
import type { CustomerBookingItem } from '../../types'
import MessageBox from '@/components/MessageBox.vue'

interface PaymentFormData {
  booking_id: number | string
  car_id: number | string
  amount: number | string
  card_number: string
  expiry_date: string
}

const props = defineProps<{
  show: boolean
  bookings: CustomerBookingItem[]
  submitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save'): void
}>()

const paymentForm = defineModel<PaymentFormData>('paymentForm', { required: true })

const onBookingSelect = () => {
  const b = props.bookings.find((item: CustomerBookingItem) => item.id === Number(paymentForm.value.booking_id))
  if (b) {
    paymentForm.value.car_id = b.car_id
    paymentForm.value.amount = b.total_price
  }
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-money-dollar-circle-line text-emerald-600" />
          <span>Record Customer Payment</span>
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
        <div>
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Link to Rental Booking *</label>
          <select
            v-model="paymentForm.booking_id"
            required
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-medium"
            @change="onBookingSelect"
          >
            <option
              value=""
              disabled
            >
              Select booking order...
            </option>
            <option
              v-for="b in bookings"
              :key="b.id"
              :value="b.id"
            >
              Order #ORD-{{ b.id }} &bull; {{ b.car?.car_name || b.car?.brand }} (${{ b.total_price }})
            </option>
          </select>
        </div>

        <div>
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Payment Amount ($) *</label>
          <input
            v-model="paymentForm.amount"
            type="number"
            step="0.01"
            required
            placeholder="150.00"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono font-bold"
          >
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Card Number (Last 4 Digits)</label>
            <input
              v-model="paymentForm.card_number"
              type="text"
              placeholder="•••• 4242"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono"
            >
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Card Expiry</label>
            <input
              v-model="paymentForm.expiry_date"
              type="text"
              placeholder="12/28"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono"
            >
          </div>
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
            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 disabled:opacity-50 cursor-pointer"
          >
            {{ submitting ? 'Recording...' : 'Record Payment' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
