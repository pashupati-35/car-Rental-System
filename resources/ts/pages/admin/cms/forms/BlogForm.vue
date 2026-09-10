<script setup lang="ts">
import type { BlogItem } from '../types'

const props = defineProps<{
  item: Partial<BlogItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Article Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. Top 10 Scenic Road Trips in 2026"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Author Name</label>
        <input
          v-model="item.author_name"
          type="text"
          placeholder="e.g. Editorial Team"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Custom URL Slug</label>
        <input
          v-model="item.slug"
          type="text"
          placeholder="top-scenic-road-trips"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Cover Image URL</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://images.unsplash.com/photo-..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Summary / Excerpt</label>
      <input
        v-model="item.short_description"
        type="text"
        placeholder="A brief summary for previews..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Full Article Content</label>
      <textarea
        v-model="item.content"
        rows="6"
        placeholder="Full markdown or HTML article content..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-sans"
      ></textarea>
    </div>
  </div>
</template>
