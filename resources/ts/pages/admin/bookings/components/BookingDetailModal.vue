<script setup lang="ts">
import type { BookingItem } from '../types'

defineProps<{
  show: boolean
  booking: BookingItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'confirm', id: number): void
  (e: 'cancel', id: number): void
  (e: 'delete', id: number): void
}>()
</script>

<template>
  <div
    v-if="show && booking"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-indigo-600" />
          <h3 class="font-bold text-base text-slate-900 dark:text-white">
            Booking Order #ORD-{{ booking.id }}
          </h3>
        </div>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <div class="space-y-3 text-xs">
        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl flex items-center justify-between">
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Vehicle</span>
            <span class="font-bold text-sm text-slate-900 dark:text-white">
              {{ booking.car?.car_name || booking.car?.brand }} {{ booking.car?.car_model || booking.car?.model }}
            </span>
            <span class="text-slate-400 font-mono text-[10px] block">Plate: {{ booking.car?.car_number || booking.car?.plate_number || 'N/A' }}</span>
          </div>

          <div class="text-right">
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Gross Total</span>
            <span class="text-xl font-black text-indigo-600 dark:text-indigo-400 font-mono">
              ${{ booking.total_price || 0 }}
            </span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl">
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Customer Client</span>
            <span class="font-bold text-slate-900 dark:text-white block">{{ booking.customer?.name || booking.customer?.full_name || 'Walk-in' }}</span>
            <span class="text-slate-400 font-mono text-[10px] block">{{ booking.customer?.email }}</span>
            <span class="text-slate-400 text-[10px] block">{{ booking.customer?.phone_number || booking.customer?.phone }}</span>
          </div>

          <div>
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Rental Period</span>
            <span class="font-mono text-slate-800 dark:text-slate-200 block">From: {{ booking.start_date }}</span>
            <span class="font-mono text-slate-800 dark:text-slate-200 block">To: {{ booking.end_date }}</span>
            <span class="text-slate-400 text-[10px] mt-1 block">Status: {{ booking.status }}</span>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
        <button
          type="button"
          class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
          @click="emit('delete', booking.id); emit('close');"
        >
          Delete Order
        </button>

        <div class="flex items-center gap-2">
          <button
            v-if="booking.status === 'pending'"
            type="button"
            class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs cursor-pointer"
            @click="emit('cancel', booking.id); emit('close');"
          >
            Cancel Order
          </button>
          <button
            v-if="booking.status === 'pending'"
            type="button"
            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 cursor-pointer"
            @click="emit('confirm', booking.id); emit('close');"
          >
            Confirm Booking
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-xs cursor-pointer"
            @click="emit('close')"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
