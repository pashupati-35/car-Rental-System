<script setup lang="ts">
import type { FaqItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'

const item = defineModel<Partial<FaqItem>>('item', { required: true })
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
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Answer / Explanation (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Provide the comprehensive answer, policies, rules, and guidelines..."
        min-height="180px"
      />
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Display Order Position</label>
      <input
        v-model="item.position"
        type="number"
        placeholder="0"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>
  </div>
</template>
