<script setup lang="ts">
import type { CustomerStats } from '../../types'

const props = defineProps<{
  stats: CustomerStats
}>()
</script>

<template>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Bookings</span>
        <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center text-sm">
          <i class="ri-calendar-check-line" />
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ stats.total_bookings }}</div>
      <div class="text-[10px] text-emerald-600 font-bold mt-1">
        {{ stats.confirmed_bookings }} Active &bull; {{ stats.completed_bookings }} Completed
      </div>
    </div>

    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Rentals</span>
        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-sm">
          <i class="ri-car-line" />
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ stats.confirmed_bookings }}</div>
      <div class="text-[10px] text-emerald-600 font-bold mt-1">Current Active Trips</div>
    </div>

    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending Orders</span>
        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 flex items-center justify-center text-sm">
          <i class="ri-time-line" />
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ stats.pending_bookings }}</div>
      <div class="text-[10px] text-amber-600 font-bold mt-1">Awaiting Approval</div>
    </div>

    <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Gross Spent</span>
        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 flex items-center justify-center text-sm">
          <i class="ri-money-dollar-circle-line" />
        </div>
      </div>
      <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
        ${{ Number(stats.total_spent).toLocaleString() }}
      </div>
      <div class="text-[10px] text-purple-600 font-bold mt-1">Lifetime Value</div>
    </div>
  </div>
</template>
