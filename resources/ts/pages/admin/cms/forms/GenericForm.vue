<script setup lang="ts">
import type { BaseCmsItem } from '../types'

const props = defineProps<{
  item: Partial<BaseCmsItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Title / Name *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="Enter record title..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Image / Asset URL</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Description / Content</label>
      <textarea
        v-model="item.description"
        rows="4"
        placeholder="Enter details..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      ></textarea>
    </div>
  </div>
</template>
