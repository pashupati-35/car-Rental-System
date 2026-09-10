<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Pagination from '@/components/Pagination.vue'
import type { CarItem } from '../types'

defineProps<{
  cars: CarItem[]
  pagination?: any
}>()

const emit = defineEmits<{
  (e: 'view', car: CarItem): void
  (e: 'verify', id: number): void
  (e: 'reject', id: number): void
  (e: 'delete', id: number): void
}>()
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
    <!-- Empty State -->
    <div
      v-if="cars.length === 0"
      class="py-16 text-center px-4"
    >
      <i class="ri-car-line text-4xl text-slate-300 dark:text-slate-700 mb-2 inline-block" />
      <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
        No cars found
      </p>
      <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
        No vehicle records match your selected status or filter query.
      </p>
    </div>

    <template v-else>
      <!-- Mobile Cards (xs, sm) -->
      <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
        <div
          v-for="car in cars"
          :key="car.id"
          class="p-4 space-y-3"
        >
          <div class="flex items-start justify-between gap-2">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                <img
                  v-if="car.image"
                  :src="car.image"
                  class="w-full h-full object-cover"
                >
                <div
                  v-else
                  class="w-full h-full flex items-center justify-center text-slate-400"
                >
                  <i class="ri-car-line text-xl" />
                </div>
              </div>
              <div>
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">
                  {{ car.car_name || car.brand }} {{ car.car_model || car.model }}
                </h4>
                <p class="text-[11px] font-mono text-slate-400">
                  {{ car.car_number || car.plate_number || 'No Plate' }}
                </p>
              </div>
            </div>

            <!-- Status Pill -->
            <span
              :class="car.status === 'verified' || car.status === 'available' ? 'bg-emerald-50 text-emerald-700' : car.status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700'"
              class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0"
            >
              {{ car.status || 'pending' }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs py-2 px-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl">
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Fleet Owner</span>
              <Link
                v-if="car.owner"
                :href="`/admin/owners/${car.owner.id}`"
                class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline truncate block"
              >
                {{ car.owner.full_name || car.owner.name }}
              </Link>
              <span
                v-else
                class="text-slate-400"
              >Direct Fleet</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Rental Rate</span>
              <span class="font-bold text-slate-900 dark:text-white font-mono">
                ${{ car.price_per_day || car.rental_price || 0 }}/day
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <button
              type="button"
              class="font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 cursor-pointer"
              @click="emit('view', car)"
            >
              <span>Inspect Car</span>
              <i class="ri-eye-line" />
            </button>

            <div class="flex items-center gap-1.5">
              <button
                v-if="car.status === 'pending' || !car.status"
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs cursor-pointer"
                @click="emit('verify', car.id)"
              >
                Approve
              </button>
              <button
                v-if="car.status === 'pending' || !car.status"
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs cursor-pointer"
                @click="emit('reject', car.id)"
              >
                Reject
              </button>
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
                @click="emit('delete', car.id)"
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
                Vehicle Name & Model
              </th>
              <th class="py-3.5 px-5">
                Fleet Owner
              </th>
              <th class="py-3.5 px-5">
                Assigned Driver
              </th>
              <th class="py-3.5 px-5">
                Rate / Day
              </th>
              <th class="py-3.5 px-5">
                Approval Status
              </th>
              <th class="py-3.5 px-5 text-right">
                Admin Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
            <tr
              v-for="car in cars"
              :key="car.id"
              class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
            >
              <!-- Vehicle -->
              <td class="py-4 px-5">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                    <img
                      v-if="car.image"
                      :src="car.image"
                      class="w-full h-full object-cover"
                    >
                    <div
                      v-else
                      class="w-full h-full flex items-center justify-center text-slate-400"
                    >
                      <i class="ri-car-line text-lg" />
                    </div>
                  </div>
                  <div>
                    <span class="font-bold text-slate-900 dark:text-white block text-sm">
                      {{ car.car_name || car.brand }} {{ car.car_model || car.model }}
                    </span>
                    <span class="text-slate-400 font-mono text-[11px]">{{ car.car_number || car.plate_number || 'N/A' }}</span>
                  </div>
                </div>
              </td>

              <!-- Owner -->
              <td class="py-4 px-5">
                <Link
                  v-if="car.owner"
                  :href="`/admin/owners/${car.owner.id}`"
                  class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline block"
                >
                  {{ car.owner.full_name || car.owner.name }}
                </Link>
                <span
                  v-else
                  class="text-slate-400 font-medium"
                >Platform Direct Fleet</span>
              </td>

              <!-- Driver -->
              <td class="py-4 px-5">
                <span
                  v-if="car.driver"
                  class="font-medium text-slate-800 dark:text-slate-200 block"
                >
                  {{ car.driver.name }}
                </span>
                <span
                  v-else
                  class="text-slate-400 italic"
                >Self-Drive / None</span>
              </td>

              <!-- Price -->
              <td class="py-4 px-5">
                <span class="font-bold font-mono text-slate-900 dark:text-white">
                  ${{ car.price_per_day || car.rental_price || 0 }}
                </span>
                <span class="text-[10px] text-slate-400 block">/ day</span>
              </td>

              <!-- Status -->
              <td class="py-4 px-5">
                <span
                  :class="car.status === 'verified' || car.status === 'available' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : car.status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300'"
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block"
                >
                  {{ car.status || 'pending' }}
                </span>
              </td>

              <!-- Actions -->
              <td class="py-4 px-5 text-right space-x-1.5">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-[11px] cursor-pointer"
                  @click="emit('view', car)"
                >
                  Inspect
                </button>
                <button
                  v-if="car.status === 'pending' || !car.status"
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs cursor-pointer"
                  @click="emit('verify', car.id)"
                >
                  Approve
                </button>
                <button
                  v-if="car.status === 'pending' || !car.status"
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] cursor-pointer"
                  @click="emit('reject', car.id)"
                >
                  Reject
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] cursor-pointer"
                  @click="emit('delete', car.id)"
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
