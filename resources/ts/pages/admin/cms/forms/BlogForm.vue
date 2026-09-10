<script setup lang="ts">
import type { BlogItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'

const item = defineModel<Partial<BlogItem>>('item', { required: true })
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Article Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. Top 10 Scenic Road Trips Across Nepal"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Author Name</label>
        <input
          v-model="item.author_name"
          type="text"
          placeholder="e.g. Editorial Fleet Team"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">URL Slug</label>
        <input
          v-model="item.slug"
          type="text"
          placeholder="top-10-scenic-road-trips"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Cover Image URL / Path</label>
        <input
          v-model="item.image"
          type="text"
          placeholder="https://images.unsplash.com/... or /storage/cms/..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <AppDatePicker
          v-model="item.published_date"
          label="Publish Date"
          placeholder="Select publication date..."
        />
      </div>
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Short Summary / Excerpt</label>
      <input
        v-model="item.short_description"
        type="text"
        placeholder="A brief summary for previews and social sharing cards..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Full Article Content (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Write the full formatted blog post with headings, quotes, lists, and links..."
        min-height="240px"
      />
    </div>
  </div>
</template>
