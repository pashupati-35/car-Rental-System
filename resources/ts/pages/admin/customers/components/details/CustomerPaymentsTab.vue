<script setup lang="ts">
import type { CustomerPaymentItem } from '../../types'

const props = defineProps<{
  payments: CustomerPaymentItem[]
}>()

const emit = defineEmits<{
  (e: 'record-payment'): void
  (e: 'delete-payment', id: number): void
}>()
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-money-dollar-circle-line text-emerald-600" />
          Financial & Payment History ({{ payments.length }})
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">
          Recorded transactions, credit payments, and payment settlements for this customer.
        </p>
      </div>

      <button
        type="button"
        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5 cursor-pointer"
        @click="emit('record-payment')"
      >
        <i class="ri-add-line" />
        <span>Record New Payment</span>
      </button>
    </div>

    <!-- Empty State -->
    <div v-if="payments.length === 0" class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800">
      <i class="ri-file-list-3-line text-4xl text-slate-300 dark:text-slate-700 mb-2 inline-block" />
      <h4 class="font-bold text-sm text-slate-700 dark:text-slate-300">No Recorded Payments</h4>
      <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">No payment records currently exist for this customer account.</p>
    </div>

    <div v-else class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
      <!-- Mobile Cards -->
      <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="p in payments" :key="p.id" class="p-4 space-y-2">
          <div class="flex items-start justify-between">
            <div>
              <span class="text-xs font-mono font-bold text-slate-400">#PAY-{{ p.id }}</span>
              <h4 class="font-bold text-sm text-slate-900 dark:text-white">
                Linked to Booking #ORD-{{ p.booking_id }}
              </h4>
              <p class="text-[11px] text-slate-400">{{ p.car?.car_name || 'Vehicle' }}</p>
            </div>
            <span class="text-base font-black font-mono text-emerald-600">${{ p.amount }}</span>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <span class="text-slate-400 font-mono text-[10px]">
              Card: •••• {{ (p.card_number || '4242').slice(-4) }}
            </span>
            <button
              type="button"
              class="px-2.5 py-1 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
              @click="emit('delete-payment', p.id)"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
            <tr>
              <th class="py-3.5 px-5">Payment Transaction #</th>
              <th class="py-3.5 px-5">Linked Order / Vehicle</th>
              <th class="py-3.5 px-5">Payment Method</th>
              <th class="py-3.5 px-5">Amount Paid</th>
              <th class="py-3.5 px-5">Timestamp</th>
              <th class="py-3.5 px-5 text-right">Admin Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
            <tr v-for="p in payments" :key="p.id" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
              <td class="py-4 px-5">
                <span class="font-mono font-bold text-slate-900 dark:text-white block">#PAY-{{ p.id }}</span>
                <span class="text-[10px] text-emerald-600 font-bold">Settled</span>
              </td>

              <td class="py-4 px-5">
                <span class="font-bold text-slate-800 dark:text-slate-200 block">Order #ORD-{{ p.booking_id }}</span>
                <span class="text-slate-400 text-[11px]">{{ p.car?.car_name || 'Vehicle' }}</span>
              </td>

              <td class="py-4 px-5 font-mono text-slate-600 dark:text-slate-400">
                <span>•••• {{ (p.card_number || '4242').slice(-4) }}</span>
                <span class="text-[10px] text-slate-400 block">Exp: {{ p.expiry_date || '12/28' }}</span>
              </td>

              <td class="py-4 px-5">
                <span class="font-black font-mono text-sm text-emerald-600 dark:text-emerald-400">
                  ${{ p.amount }}
                </span>
              </td>

              <td class="py-4 px-5 text-slate-400 text-[11px]">
                {{ p.created_at ? new Date(p.created_at).toLocaleString() : 'N/A' }}
              </td>

              <td class="py-4 px-5 text-right">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] cursor-pointer"
                  @click="emit('delete-payment', p.id)"
                >
                  Delete Record
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
