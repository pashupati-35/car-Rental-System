<script setup lang="ts">
import type { BaseCmsItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'

const item = defineModel<Partial<BaseCmsItem>>('item', { required: true })
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
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Image / Asset URL or Path</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://... or /storage/..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Description / Content (Rich Text)</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Enter formatted content details..."
        min-height="160px"
      />
    </div>
  </div>
</template>
