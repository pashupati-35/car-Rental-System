<script setup lang="ts">
import type { OwnerStats } from '../../types'

defineProps<{
  stats: OwnerStats
}>()
</script>

<template>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Fleet Cars</span>
        <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center text-sm">
          <i class="ri-car-line" />
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
        {{ stats.total_cars }}
      </div>
      <div class="text-[10px] text-emerald-600 font-bold mt-1">
        {{ stats.verified_cars }} Active &bull; {{ stats.pending_cars }} Pending
      </div>
    </div>

    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Drivers</span>
        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 flex items-center justify-center text-sm">
          <i class="ri-steering-2-line" />
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
        {{ stats.total_drivers }}
      </div>
      <div class="text-[10px] text-purple-600 font-bold mt-1">
        Assigned Crew
      </div>
    </div>

    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Bookings</span>
        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 flex items-center justify-center text-sm">
          <i class="ri-calendar-check-line" />
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
        {{ stats.total_bookings }}
      </div>
      <div class="text-[10px] text-blue-600 font-bold mt-1">
        {{ stats.confirmed_bookings }} Completed
      </div>
    </div>

    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Gross Revenue</span>
        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-sm">
          <i class="ri-money-dollar-circle-line" />
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
        ${{ Number(stats.total_revenue).toLocaleString() }}
      </div>
      <div class="text-[10px] text-emerald-600 font-bold mt-1">
        Total Generated
      </div>
    </div>
  </div>
</template>
