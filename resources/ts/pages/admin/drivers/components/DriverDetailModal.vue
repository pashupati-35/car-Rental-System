<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { DriverItem } from '../types'

defineProps<{
  show: boolean
  driver: DriverItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'edit', driver: DriverItem): void
}>()

const formatDate = (date?: string) => {
  if (!date) {
    return 'N/A'
  }

  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show && driver"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
        @click.self="emit('close')"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out delay-75"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="show && driver"
            class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full border border-slate-200 dark:border-slate-800 shadow-2xl relative overflow-hidden"
          >
            <!-- Header with gradient background -->
            <div class="relative bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-700 px-6 pt-6 pb-12">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full bg-white/60" />
                  <h3 class="font-bold text-sm text-white/90 tracking-wide uppercase">
                    Driver Profile
                  </h3>
                </div>
                <button
                  type="button"
                  class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer"
                  @click="emit('close')"
                >
                  <i class="ri-close-line text-lg" />
                </button>
              </div>
            </div>

            <!-- Avatar overlapping header -->
            <div class="relative -mt-8 px-6">
              <div class="flex items-end gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white dark:bg-slate-800 border-4 border-white dark:border-slate-900 shadow-lg flex items-center justify-center shrink-0">
                  <span class="text-2xl font-black text-purple-600 dark:text-purple-400">
                    {{ (driver.name || 'D').charAt(0).toUpperCase() }}
                  </span>
                </div>
                <div class="pb-1">
                  <h4 class="font-black text-lg text-slate-900 dark:text-white leading-tight">
                    {{ driver.name }}
                  </h4>
                  <span class="text-xs text-slate-400 font-mono">#DRV-{{ driver.id }}</span>
                </div>
              </div>
            </div>

            <!-- Body content -->
            <div class="px-6 py-5 space-y-4">
              <!-- Status badge -->
              <div class="flex items-center gap-2">
                <span
                  :class="driver.status === 'active'
                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-500/20 dark:bg-emerald-950 dark:text-emerald-300'
                    : 'bg-slate-100 text-slate-600 ring-slate-500/10 dark:bg-slate-800 dark:text-slate-400'"
                  class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider ring-1"
                >
                  {{ driver.status || 'Active' }}
                </span>
                <span
                  v-if="driver.experience_years"
                  class="px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 ring-1 ring-blue-500/20 dark:bg-blue-950 dark:text-blue-300"
                >
                  {{ driver.experience_years }} Years Exp
                </span>
              </div>

              <!-- Contact & License section -->
              <div class="grid grid-cols-2 gap-3">
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
                  <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block mb-1">
                    <i class="ri-phone-line me-1" />Phone
                  </span>
                  <span class="font-bold text-sm text-slate-900 dark:text-white block">
                    {{ driver.phone || 'N/A' }}
                  </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
                  <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block mb-1">
                    <i class="ri-mail-line me-1" />Email
                  </span>
                  <span class="font-bold text-sm text-slate-900 dark:text-white block truncate">
                    {{ driver.email || 'Not provided' }}
                  </span>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
                  <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block mb-1">
                    <i class="ri-file-text-line me-1" />License Number
                  </span>
                  <span class="font-mono font-bold text-sm text-slate-900 dark:text-white block">
                    {{ driver.license_number || 'N/A' }}
                  </span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
                  <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block mb-1">
                    <i class="ri-building-line me-1" />Fleet Owner
                  </span>
                  <Link
                    v-if="driver.owner"
                    :href="`/admin/owners/${driver.owner.id}`"
                    class="font-bold text-sm text-indigo-600 dark:text-indigo-400 hover:underline block truncate"
                  >
                    {{ driver.owner.full_name || driver.owner.name }}
                  </Link>
                  <span
                    v-else
                    class="font-bold text-sm text-slate-500 block"
                  >Independent Pool</span>
                </div>
              </div>

              <!-- Address -->
              <div
                v-if="driver.address"
                class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60"
              >
                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block mb-1">
                  <i class="ri-map-pin-line me-1" />Address
                </span>
                <span class="font-semibold text-sm text-slate-800 dark:text-slate-200">
                  {{ driver.address }}
                </span>
              </div>

              <!-- Timestamps -->
              <div class="flex items-center gap-4 text-[11px] text-slate-400 pt-1">
                <span>
                  <i class="ri-calendar-line me-0.5" />
                  Joined: {{ formatDate(driver.created_at) }}
                </span>
                <span>
                  <i class="ri-refresh-line me-0.5" />
                  Updated: {{ formatDate(driver.updated_at) }}
                </span>
              </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
              <button
                type="button"
                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 font-bold text-xs cursor-pointer transition-colors"
                @click="emit('close')"
              >
                Close
              </button>
              <button
                type="button"
                class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 cursor-pointer transition-colors flex items-center gap-1.5"
                @click="emit('edit', driver); emit('close')"
              >
                <i class="ri-edit-line text-sm" />
                Edit Driver
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
