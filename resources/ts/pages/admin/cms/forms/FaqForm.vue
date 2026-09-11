<script setup lang="ts">
import type { FaqItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'

const item = defineModel<Partial<FaqItem>>('item', { required: true })
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Title / Question *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. What services or documents does your car rental provide?"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Short Description / Excerpt</label>
      <textarea
        v-model="item.short_description"
        rows="2"
        placeholder="Brief overview summary for quick previews..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      />
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Description / Detailed Answer (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Provide the comprehensive answer, terms, and guidelines with rich formatting..."
        min-height="180px"
      />
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Display Order Position</label>
      <input
        v-model="item.position"
        type="number"
        placeholder="0"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">SEO Meta Title</label>
        <input
          v-model="item.seo_title"
          type="text"
          placeholder="SEO optimized title..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">SEO Meta Description</label>
        <input
          v-model="item.seo_description"
          type="text"
          placeholder="Meta description for search engines..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
    </div>
  </div>
</template>
