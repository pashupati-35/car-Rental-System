<script setup lang="ts">
import type { MenuItem } from '../types'

const props = defineProps<{
  item: Partial<MenuItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Menu Item Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. Luxury SUVs"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Target URL Link</label>
        <input
          v-model="item.url"
          type="text"
          placeholder="/cars?category=suv"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Open Target</label>
        <select
          v-model="item.target"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        >
          <option value="_self">Same Tab (_self)</option>
          <option value="_blank">New Window / Tab (_blank)</option>
        </select>
      </div>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Menu Order Position</label>
      <input
        v-model="item.position"
        type="number"
        placeholder="0"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
  </div>
</template>
