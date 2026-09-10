<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/components/Pagination.vue'
import type { CarItem } from '../types'

defineProps<{
  cars: CarItem[]
  pagination?: any
}>()

const emit = defineEmits<{
  (e: 'view', car: CarItem): void
  (e: 'edit', car: CarItem): void
  (e: 'verify', id: number): void
  (e: 'reject', id: number): void
  (e: 'delete', id: number): void
}>()

const activeDropdown = ref<number | null>(null)

const toggleDropdown = (id: number, event: MouseEvent) => {
  event.stopPropagation()
  activeDropdown.value = activeDropdown.value === id ? null : id
}

const closeDropdown = () => {
  activeDropdown.value = null
}

onMounted(() => {
  window.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  window.removeEventListener('click', closeDropdown)
})

const getCarImage = (car: CarItem) => {
  if (car.image) return car.image
  if (car.image_path?.original) return car.image_path.original
  if (car.car_photo) {
    return car.car_photo.startsWith('http') ? car.car_photo : `/${car.car_photo.replace(/^\/+/, '')}`
  }
  return null
}
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
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
                  v-if="getCarImage(car)"
                  :src="getCarImage(car)!"
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
                <button
                  type="button"
                  class="font-bold text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline cursor-pointer text-left transition-colors"
                  @click="emit('view', car)"
                >
                  {{ car.car_name || car.brand }} {{ car.car_model || car.model }}
                </button>
                <p class="text-[11px] font-mono text-slate-400">
                  {{ car.car_number || car.plate_number || 'No Plate' }}
                </p>
              </div>
            </div>

            <!-- Status Pill & 3 dots -->
            <div class="flex items-center gap-1.5 shrink-0">
              <span
                :class="car.status === 'verified' || car.status === 'available' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : car.status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300'"
                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0"
              >
                {{ car.status || 'pending' }}
              </span>

              <!-- Mobile Actions Dropdown -->
              <div class="relative">
                <button
                  type="button"
                  class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                  :class="{ 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white': activeDropdown === car.id }"
                  @click="toggleDropdown(car.id, $event)"
                >
                  <i class="ri-more-2-fill text-base" />
                </button>

                <div
                  v-if="activeDropdown === car.id"
                  class="absolute right-0 top-full mt-1.5 w-48 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200/80 dark:border-slate-800 py-1.5 z-50 text-left"
                  @click.stop
                >
                  <!-- Inspect -->
                  <button
                    type="button"
                    class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-300 flex items-center gap-2.5 transition-colors cursor-pointer"
                    @click="emit('view', car); closeDropdown()"
                  >
                    <i class="ri-eye-line text-slate-400 text-sm" />
                    <span>Inspect</span>
                  </button>

                  <!-- Edit -->
                  <button
                    type="button"
                    class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-300 flex items-center gap-2.5 transition-colors cursor-pointer"
                    @click="emit('edit', car); closeDropdown()"
                  >
                    <i class="ri-edit-line text-slate-400 text-sm" />
                    <span>Edit</span>
                  </button>

                  <div class="my-1 border-t border-slate-100 dark:border-slate-800" />

                  <!-- Approve (when pending or rejected) -->
                  <button
                    v-if="car.status === 'pending' || car.status === 'rejected' || !car.status"
                    type="button"
                    class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 flex items-center gap-2.5 transition-colors cursor-pointer"
                    @click="emit('verify', car.id); closeDropdown()"
                  >
                    <i class="ri-checkbox-circle-line text-emerald-500 text-sm" />
                    <span>Approve</span>
                  </button>

                  <!-- Disapprove (when verified or pending) -->
                  <button
                    v-if="car.status === 'verified' || car.status === 'available' || car.status === 'pending' || !car.status"
                    type="button"
                    class="w-full px-3.5 py-2 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 flex items-center gap-2.5 transition-colors cursor-pointer"
                    @click="emit('reject', car.id); closeDropdown()"
                  >
                    <i class="ri-close-circle-line text-amber-500 text-sm" />
                    <span>Disapprove</span>
                  </button>

                  <div class="my-1 border-t border-slate-100 dark:border-slate-800" />

                  <!-- Delete -->
                  <button
                    type="button"
                    class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 flex items-center gap-2.5 transition-colors cursor-pointer"
                    @click="emit('delete', car.id); closeDropdown()"
                  >
                    <i class="ri-delete-bin-line text-rose-500 text-sm" />
                    <span>Delete</span>
                  </button>
                </div>
              </div>
            </div>
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
        </div>
      </div>

      <!-- Desktop Table (md and up) -->
      <div class="hidden md:block overflow-x-auto min-h-[320px]">
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
                      v-if="getCarImage(car)"
                      :src="getCarImage(car)!"
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
                    <button
                      type="button"
                      class="font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline cursor-pointer block text-sm text-left transition-colors"
                      @click="emit('view', car)"
                    >
                      {{ car.car_name || car.brand }} {{ car.car_model || car.model }}
                    </button>
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

              <!-- 3 Dots Action Dropdown -->
              <td class="py-4 px-5 text-right">
                <div class="relative inline-block text-left">
                  <button
                    type="button"
                    class="w-8 h-8 rounded-xl inline-flex items-center justify-center text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer border border-transparent hover:border-slate-200 dark:hover:border-slate-700"
                    :class="{ 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white': activeDropdown === car.id }"
                    title="Actions"
                    @click="toggleDropdown(car.id, $event)"
                  >
                    <i class="ri-more-2-fill text-lg" />
                  </button>

                  <!-- Dropdown Menu -->
                  <div
                    v-if="activeDropdown === car.id"
                    class="absolute right-0 top-full mt-1.5 w-48 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200/80 dark:border-slate-800 py-1.5 z-50 text-left"
                    @click.stop
                  >
                    <!-- Inspect Details -->
                    <button
                      type="button"
                      class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-300 flex items-center gap-2.5 transition-colors cursor-pointer"
                      @click="emit('view', car); closeDropdown()"
                    >
                      <i class="ri-eye-line text-slate-400 text-sm" />
                      <span>Inspect</span>
                    </button>

                    <!-- Edit Vehicle -->
                    <button
                      type="button"
                      class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 hover:text-indigo-600 dark:hover:text-indigo-300 flex items-center gap-2.5 transition-colors cursor-pointer"
                      @click="emit('edit', car); closeDropdown()"
                    >
                      <i class="ri-edit-line text-slate-400 text-sm" />
                      <span>Edit</span>
                    </button>

                    <div class="my-1 border-t border-slate-100 dark:border-slate-800" />

                    <!-- Approve Vehicle (when pending or rejected) -->
                    <button
                      v-if="car.status === 'pending' || car.status === 'rejected' || !car.status"
                      type="button"
                      class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 flex items-center gap-2.5 transition-colors cursor-pointer"
                      @click="emit('verify', car.id); closeDropdown()"
                    >
                      <i class="ri-checkbox-circle-line text-emerald-500 text-sm" />
                      <span>Approve</span>
                    </button>

                    <!-- Disapprove / Reject Vehicle (when verified or pending) -->
                    <button
                      v-if="car.status === 'verified' || car.status === 'available' || car.status === 'pending' || !car.status"
                      type="button"
                      class="w-full px-3.5 py-2 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 flex items-center gap-2.5 transition-colors cursor-pointer"
                      @click="emit('reject', car.id); closeDropdown()"
                    >
                      <i class="ri-close-circle-line text-amber-500 text-sm" />
                      <span>Disapprove</span>
                    </button>

                    <div class="my-1 border-t border-slate-100 dark:border-slate-800" />

                    <!-- Delete Car -->
                    <button
                      type="button"
                      class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 flex items-center gap-2.5 transition-colors cursor-pointer"
                      @click="emit('delete', car.id); closeDropdown()"
                    >
                      <i class="ri-delete-bin-line text-rose-500 text-sm" />
                      <span>Delete</span>
                    </button>
                  </div>
                </div>
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
