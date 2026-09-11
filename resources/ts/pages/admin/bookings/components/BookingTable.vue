<script setup lang="ts">
import Pagination from '@/components/Pagination.vue'
import type { BookingItem } from '../types'

defineProps<{
  bookings: BookingItem[]
  pagination?: any
}>()

const emit = defineEmits<{
  (e: 'view', booking: BookingItem): void
  (e: 'confirm', id: number): void
  (e: 'cancel', id: number): void
  (e: 'delete', id: number): void
}>()
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
    <!-- Empty State -->
    <div
      v-if="bookings.length === 0"
      class="py-16 text-center px-4"
    >
      <i class="ri-calendar-event-line text-4xl text-slate-300 dark:text-slate-700 mb-2 inline-block" />
      <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
        No rental bookings found
      </p>
      <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
        No booking orders match the current status filter.
      </p>
    </div>

    <template v-else>
      <!-- Mobile Cards (xs, sm) -->
      <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
        <div
          v-for="booking in bookings"
          :key="booking.id"
          class="p-4 space-y-3"
        >
          <div class="flex items-start justify-between gap-2">
            <div>
              <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-bold text-slate-400">#ORD-{{ booking.id }}</span>
                <span
                  :class="booking.status === 'confirm' || booking.status === 'confirmed' || booking.status === 'completed' ? 'bg-emerald-50 text-emerald-700' : booking.status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700'"
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                >
                  {{ booking.status || 'pending' }}
                </span>
              </div>
              <h4 class="font-bold text-sm text-slate-900 dark:text-white mt-1">
                {{ booking.car?.car_name || booking.car?.brand || 'Car' }} {{ booking.car?.car_model || booking.car?.model || '' }}
              </h4>
            </div>

            <div class="text-right">
              <span class="font-black text-sm text-indigo-600 dark:text-indigo-400 font-mono block">
                ${{ booking.total_price || 0 }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs py-2 px-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl">
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Client</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200">
                {{ booking.customer?.name || booking.customer?.full_name || 'Walk-in Client' }}
              </span>
            </div>
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Rental Dates</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200 text-[11px]">
                {{ booking.start_date }} &rarr; {{ booking.end_date }}
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <button
              type="button"
              class="font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 cursor-pointer"
              @click="emit('view', booking)"
            >
              <span>Inspect</span>
              <i class="ri-eye-line" />
            </button>

            <div class="flex items-center gap-1.5">
              <button
                v-if="booking.status === 'pending'"
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs cursor-pointer"
                @click="emit('confirm', booking.id)"
              >
                Confirm
              </button>
              <button
                v-if="booking.status === 'pending'"
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs cursor-pointer"
                @click="emit('cancel', booking.id)"
              >
                Cancel
              </button>
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
                @click="emit('delete', booking.id)"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Desktop Table (md and up) -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
            <tr>
              <th class="py-3.5 px-5">
                Order # / Car
              </th>
              <th class="py-3.5 px-5">
                Customer Client
              </th>
              <th class="py-3.5 px-5">
                Booking Dates
              </th>
              <th class="py-3.5 px-5">
                Gross Total
              </th>
              <th class="py-3.5 px-5">
                Status
              </th>
              <th class="py-3.5 px-5 text-right">
                Admin Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
            <tr
              v-for="booking in bookings"
              :key="booking.id"
              class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
            >
              <td class="py-4 px-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                    <img
                      v-if="booking.car?.image"
                      :src="booking.car.image"
                      class="w-full h-full object-cover"
                    >
                    <div
                      v-else
                      class="w-full h-full flex items-center justify-center text-slate-400"
                    >
                      <i class="ri-car-line" />
                    </div>
                  </div>
                  <div>
                    <span class="font-bold text-slate-900 dark:text-white block text-sm">
                      {{ booking.car?.car_name || booking.car?.brand || 'Car' }} {{ booking.car?.car_model || booking.car?.model || '' }}
                    </span>
                    <span class="font-mono text-slate-400 text-[10px]">#ORD-{{ booking.id }} &bull; {{ booking.car?.car_number || booking.car?.plate_number || 'N/A' }}</span>
                  </div>
                </div>
              </td>

              <td class="py-4 px-5">
                <span class="font-bold text-slate-800 dark:text-slate-200 block">
                  {{ booking.customer?.name || booking.customer?.full_name || 'Client' }}
                </span>
                <span class="text-slate-400 font-mono text-[11px]">{{ booking.customer?.email }}</span>
              </td>

              <td class="py-4 px-5">
                <span class="font-mono text-slate-800 dark:text-slate-200 block">{{ booking.start_date }}</span>
                <span class="text-slate-400 font-mono text-[10px]">To: {{ booking.end_date }}</span>
              </td>

              <td class="py-4 px-5">
                <span class="font-black font-mono text-sm text-indigo-600 dark:text-indigo-400">
                  ${{ booking.total_price || 0 }}
                </span>
              </td>

              <td class="py-4 px-5">
                <span
                  :class="booking.status === 'confirm' || booking.status === 'confirmed' || booking.status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : booking.status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300'"
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block"
                >
                  {{ booking.status || 'pending' }}
                </span>
              </td>

              <td class="py-4 px-5 text-right space-x-1.5">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-[11px] cursor-pointer"
                  @click="emit('view', booking)"
                >
                  Inspect
                </button>
                <button
                  v-if="booking.status === 'pending'"
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] cursor-pointer"
                  @click="emit('confirm', booking.id)"
                >
                  Confirm
                </button>
                <button
                  v-if="booking.status === 'pending'"
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] cursor-pointer"
                  @click="emit('cancel', booking.id)"
                >
                  Cancel
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] cursor-pointer"
                  @click="emit('delete', booking.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="pagination && (pagination.links || pagination.total)"
        class="border-t border-slate-100 dark:border-slate-800"
      >
        <Pagination
          :links="pagination.links"
          :from="pagination.from"
          :to="pagination.to"
          :total="pagination.total"
          :current-page="pagination.current_page"
          :last-page="pagination.last_page"
          :per-page="pagination.per_page"
        />
      </div>
    </template>
  </div>
</template>
