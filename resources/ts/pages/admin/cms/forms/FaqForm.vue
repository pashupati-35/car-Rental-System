<script setup lang="ts">
import type { FaqItem } from '../types'

const props = defineProps<{
  item: Partial<FaqItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Question *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. What documents do I need to rent a car?"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Answer / Explanation *</label>
      <textarea
        v-model="item.description"
        rows="4"
        required
        placeholder="Detailed FAQ answer explaining requirements, policies, or procedures..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      ></textarea>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Display Order Position</label>
      <input
        v-model="item.position"
        type="number"
        placeholder="0"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
  </div>
</template>
