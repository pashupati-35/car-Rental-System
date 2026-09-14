<script setup lang="ts">
import type { CustomerBookingItem } from '../../types'

defineProps<{
  bookings: CustomerBookingItem[]
}>()

const emit = defineEmits<{
  (e: 'add-booking'): void
  (e: 'edit-booking', booking: CustomerBookingItem): void
  (e: 'delete-booking', id: number): void
  (e: 'update-status', id: number, status: string): void
}>()
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-calendar-check-line text-indigo-600" />
          Customer Rental Bookings ({{ bookings.length }})
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">
          Full management of rental orders, schedules, pickup/drop locations, and reservation status for this customer.
        </p>
      </div>

      <button
        type="button"
        class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center gap-1.5 cursor-pointer"
        @click="emit('add-booking')"
      >
        <i class="ri-add-line" />
        <span>Book Vehicle for Customer</span>
      </button>
    </div>

    <!-- Empty State -->
    <div
      v-if="bookings.length === 0"
      class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800"
    >
      <i class="ri-calendar-event-line text-4xl text-slate-300 dark:text-slate-700 mb-2 inline-block" />
      <h4 class="font-bold text-sm text-slate-700 dark:text-slate-300">
        No Rental Bookings Yet
      </h4>
      <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
        This customer has no recorded bookings. Click "Book Vehicle for Customer" to create an order.
      </p>
    </div>

    <div
      v-else
      class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden"
    >
      <!-- Mobile Cards -->
      <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
        <div
          v-for="b in bookings"
          :key="b.id"
          class="p-4 space-y-3"
        >
          <div class="flex items-start justify-between gap-2">
            <div>
              <span class="font-mono text-xs font-bold text-slate-400">#ORD-{{ b.id }}</span>
              <h4 class="font-bold text-sm text-slate-900 dark:text-white">
                {{ b.car?.car_name || b.car?.brand || 'Car' }} {{ b.car?.car_model || b.car?.model || '' }}
              </h4>
              <p class="text-[11px] font-mono text-slate-400">
                {{ b.car?.car_number || b.car?.plate_number || 'N/A' }}
              </p>
            </div>

            <span
              :class="b.status === 'confirm' || b.status === 'confirmed' || b.status === 'completed' ? 'bg-emerald-50 text-emerald-700' : b.status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700'"
              class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
            >
              {{ b.status }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs py-2 px-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl">
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Rental Dates</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200 text-[11px]">
                {{ b.pick_up_date }} &rarr; {{ b.last_date }}
              </span>
            </div>
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Gross Price</span>
              <span class="font-bold text-indigo-600 font-mono">${{ b.total_price }}</span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <span class="text-slate-500 text-[11px]">
              {{ b.pickup_location }} &rarr; {{ b.drop_location }}
            </span>
            <div class="flex items-center gap-1.5">
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-xs cursor-pointer"
                @click="emit('edit-booking', b)"
              >
                Edit
              </button>
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
                @click="emit('delete-booking', b.id)"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
            <tr>
              <th class="py-3.5 px-5">
                Order # / Vehicle
              </th>
              <th class="py-3.5 px-5">
                Pickup & Return Dates
              </th>
              <th class="py-3.5 px-5">
                Route Locations
              </th>
              <th class="py-3.5 px-5">
                Total Price
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
              v-for="b in bookings"
              :key="b.id"
              class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
            >
              <td class="py-4 px-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                    <img
                      v-if="b.car?.image"
                      :src="b.car.image"
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
                      {{ b.car?.car_name || b.car?.brand || 'Car' }} {{ b.car?.car_model || b.car?.model || '' }}
                    </span>
                    <span class="font-mono text-slate-400 text-[10px]">#ORD-{{ b.id }} &bull; Plate: {{ b.car?.car_number || b.car?.plate_number || 'N/A' }}</span>
                  </div>
                </div>
              </td>

              <td class="py-4 px-5">
                <span class="font-mono text-slate-800 dark:text-slate-200 block">{{ b.pick_up_date }}</span>
                <span class="text-slate-400 font-mono text-[10px]">Until: {{ b.last_date }}</span>
              </td>

              <td class="py-4 px-5">
                <span class="font-medium text-slate-800 dark:text-slate-200 block truncate max-w-xs">{{ b.pickup_location }}</span>
                <span class="text-slate-400 text-[11px] truncate max-w-xs block">&rarr; {{ b.drop_location }}</span>
              </td>

              <td class="py-4 px-5">
                <span class="font-black font-mono text-sm text-indigo-600 dark:text-indigo-400">
                  ${{ b.total_price }}
                </span>
              </td>

              <td class="py-4 px-5">
                <span
                  :class="b.status === 'confirm' || b.status === 'confirmed' || b.status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : b.status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300'"
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block"
                >
                  {{ b.status }}
                </span>
              </td>

              <td class="py-4 px-5 text-right space-x-1.5">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-[11px] cursor-pointer"
                  @click="emit('edit-booking', b)"
                >
                  Edit
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] cursor-pointer"
                  @click="emit('delete-booking', b.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
