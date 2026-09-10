<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Pagination from '@/components/Pagination.vue'
import type { DriverItem } from '../types'

defineProps<{
  drivers: DriverItem[]
  pagination?: any
}>()

const emit = defineEmits<{
  (e: 'edit', driver: DriverItem): void
  (e: 'delete', id: number): void
  (e: 'view-details', driver: DriverItem): void
}>()

const getDriverImage = (d: DriverItem) => {
  if (d.image) return d.image
  if (d.image_path?.original) return d.image_path.original
  if (d.photo) {
    return d.photo.startsWith('http') ? d.photo : `/${d.photo.replace(/^\/+/, '')}`
  }
  return null
}
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
    <!-- Empty State -->
    <div
      v-if="drivers.length === 0"
      class="py-16 text-center px-4"
    >
      <i class="ri-steering-2-line text-4xl text-slate-300 dark:text-slate-700 mb-2 inline-block" />
      <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
        No Drivers found
      </p>
      <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
        Click "Add Chauffeur / Driver" to register a driver profile.
      </p>
    </div>

    <template v-else>
      <!-- Mobile Cards (xs, sm) -->
      <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
        <div
          v-for="driver in drivers"
          :key="driver.id"
          class="p-4 space-y-3"
        >
          <div class="flex items-start justify-between gap-2">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950 text-purple-600 flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden border border-slate-200 dark:border-slate-800">
                <img
                  v-if="getDriverImage(driver)"
                  :src="getDriverImage(driver)!"
                  class="w-full h-full object-cover"
                >
                <span v-else>
                  {{ (driver.name || 'D').charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <button
                  type="button"
                  class="font-bold text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline cursor-pointer text-left transition-colors"
                  @click="emit('view-details', driver)"
                >
                  {{ driver.name }}
                </button>
                <p class="text-[11px] text-slate-400 font-mono">
                  {{ driver.phone }}
                </p>
              </div>
            </div>

            <span
              :class="driver.status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
              class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0"
            >
              {{ driver.status || 'Active' }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs py-2 px-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl">
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">License Number</span>
              <span class="font-mono font-semibold text-slate-800 dark:text-slate-200">{{ driver.license_number || 'N/A' }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Affiliation</span>
              <Link
                v-if="driver.owner"
                :href="`/admin/owners/${driver.owner.id}`"
                class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline truncate block"
              >
                {{ driver.owner.full_name || driver.owner.name }}
              </Link>
              <span
                v-else
                class="text-slate-400"
              >Independent / Platform</span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <span class="text-[11px] text-slate-400">
              Exp: {{ driver.experience_years ? `${driver.experience_years} Years` : '1 Year' }}
            </span>
            <div class="flex items-center gap-1.5">
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200 font-bold text-xs cursor-pointer"
                @click="emit('edit', driver)"
              >
                Edit
              </button>
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
                @click="emit('delete', driver.id)"
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
                Driver Name & ID
              </th>
              <th class="py-3.5 px-5">
                Contact Details
              </th>
              <th class="py-3.5 px-5">
                License & Experience
              </th>
              <th class="py-3.5 px-5">
                Fleet Owner Affiliation
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
              v-for="driver in drivers"
              :key="driver.id"
              class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
            >
              <td class="py-4 px-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden border border-slate-200 dark:border-slate-800">
                    <img
                      v-if="getDriverImage(driver)"
                      :src="getDriverImage(driver)!"
                      class="w-full h-full object-cover"
                    >
                    <span v-else>
                      {{ (driver.name || 'D').charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <div>
                    <button
                      type="button"
                      class="font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline cursor-pointer block text-sm text-left transition-colors"
                      @click="emit('view-details', driver)"
                    >
                      {{ driver.name }}
                    </button>
                    <span class="text-slate-400 font-mono text-[10px]">#DRV-{{ driver.id }}</span>
                  </div>
                </div>
              </td>

              <td class="py-4 px-5">
                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ driver.phone }}</span>
                <span class="text-slate-400 text-[11px]">{{ driver.email || 'No email' }}</span>
              </td>

              <td class="py-4 px-5">
                <span class="font-mono bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-[11px] block w-max">
                  {{ driver.license_number || 'N/A' }}
                </span>
                <span class="text-slate-400 text-[10px] mt-0.5 block">
                  {{ driver.experience_years ? `${driver.experience_years} yrs exp` : 'Standard' }}
                </span>
              </td>

              <td class="py-4 px-5">
                <Link
                  v-if="driver.owner"
                  :href="`/admin/owners/${driver.owner.id}`"
                  class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline block"
                >
                  {{ driver.owner.full_name || driver.owner.name }}
                </Link>
                <span
                  v-else
                  class="text-slate-400"
                >Independent Pool</span>
              </td>

              <td class="py-4 px-5">
                <span
                  :class="driver.status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block"
                >
                  {{ driver.status || 'Active' }}
                </span>
              </td>

              <td class="py-4 px-5 text-right space-x-1.5">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-[11px] cursor-pointer"
                  @click="emit('edit', driver)"
                >
                  Edit
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] cursor-pointer"
                  @click="emit('delete', driver.id)"
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
