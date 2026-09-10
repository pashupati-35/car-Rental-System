<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { CarItem } from '../types'

defineProps<{
  show: boolean
  car: CarItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'edit', car: CarItem): void
  (e: 'verify', id: number): void
  (e: 'reject', id: number): void
  (e: 'delete', id: number): void
}>()

const getCarImage = (c: CarItem | null) => {
  if (!c) return null
  if (c.image) return c.image
  if (c.image_path?.original) return c.image_path.original
  if (c.car_photo) {
    return c.car_photo.startsWith('http') ? c.car_photo : `/${c.car_photo.replace(/^\/+/, '')}`
  }
  return null
}
</script>

<template>
  <div
    v-if="show && car"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-indigo-600" />
          <h3 class="font-bold text-base text-slate-900 dark:text-white">
            Vehicle Specification & Verification
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

      <!-- Car Banner Image -->
      <div
        v-if="getCarImage(car)"
        class="w-full h-44 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 dark:border-slate-800"
      >
        <img
          :src="getCarImage(car)!"
          class="w-full h-full object-cover"
        >
      </div>

      <div class="space-y-3 text-xs">
        <div class="flex items-start justify-between">
          <div>
            <h4 class="font-black text-lg text-slate-900 dark:text-white">
              {{ car.car_name || car.brand }} {{ car.car_model || car.model }}
            </h4>
            <span class="font-mono text-xs text-slate-400">Plate: {{ car.car_number || car.plate_number || 'N/A' }}</span>
          </div>

          <div class="text-right">
            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 font-mono">
              ${{ car.price_per_day || car.rental_price || 0 }}
            </span>
            <span class="text-slate-400 block text-[10px]">/ 24 hours</span>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-2.5 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Capacity</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ car.seating_capacity || 4 }} Seats</span>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Fuel Type</span>
            <span class="font-bold text-slate-900 dark:text-white capitalize">{{ car.fuel_type || 'Petrol' }}</span>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Transmission</span>
            <span class="font-bold text-slate-900 dark:text-white capitalize">{{ car.transmission || 'Automatic' }}</span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Vehicle Owner</span>
            <Link
              v-if="car.owner"
              :href="`/admin/owners/${car.owner.id}`"
              class="font-bold text-indigo-600 hover:underline"
            >
              {{ car.owner.full_name || car.owner.name }}
            </Link>
            <span
              v-else
              class="font-bold text-slate-700 dark:text-slate-300"
            >Platform Fleet</span>
          </div>
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Assigned Driver</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ car.driver ? car.driver.name : 'Self-Drive' }}</span>
          </div>
        </div>

        <div v-if="car.description">
          <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Description</span>
          <p class="text-slate-600 dark:text-slate-400 leading-relaxed bg-slate-50 dark:bg-slate-800/40 p-3 rounded-2xl">
            {{ car.description }}
          </p>
        </div>
      </div>

      <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
        <button
          type="button"
          class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
          @click="emit('delete', car.id); emit('close');"
        >
          Delete Car
        </button>

        <div class="flex items-center gap-2">
          <button
            type="button"
            class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold text-xs cursor-pointer flex items-center gap-1"
            @click="emit('edit', car); emit('close');"
          >
            <i class="ri-edit-line" />
            <span>Edit Vehicle</span>
          </button>
          <!-- Approve button (when pending or rejected) -->
          <button
            v-if="car.status === 'pending' || car.status === 'rejected' || !car.status"
            type="button"
            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 cursor-pointer flex items-center gap-1"
            @click="emit('verify', car.id); emit('close');"
          >
            <i class="ri-checkbox-circle-line" />
            <span>Approve & Verify</span>
          </button>

          <!-- Disapprove / Reject button (when verified or pending) -->
          <button
            v-if="car.status === 'verified' || car.status === 'available' || car.status === 'pending' || !car.status"
            type="button"
            class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 font-bold text-xs cursor-pointer flex items-center gap-1"
            @click="emit('reject', car.id); emit('close');"
          >
            <i class="ri-close-circle-line" />
            <span>Disapprove</span>
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
