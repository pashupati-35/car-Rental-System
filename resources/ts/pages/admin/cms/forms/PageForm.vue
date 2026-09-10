<script setup lang="ts">
import type { PageItem } from '../types'

const props = defineProps<{
  item: Partial<PageItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Page Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. Terms & Rental Policy"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Page Slug (URL Identifier)</label>
        <input
          v-model="item.slug"
          type="text"
          placeholder="terms-and-policies"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Banner / Header Image URL</label>
        <input
          v-model="item.banner_image"
          type="text"
          placeholder="https://images.unsplash.com/..."
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Page Content / HTML</label>
      <textarea
        v-model="item.content"
        rows="8"
        placeholder="Full content for this page..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-mono"
      ></textarea>
    </div>
  </div>
</template>
