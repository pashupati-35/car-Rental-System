<script setup lang="ts">
import type { NewsItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'

const props = defineProps<{
  item: Partial<NewsItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">News Headline / Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. Electric Vehicle Fleet Expansion Announced"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Slug</label>
        <input
          v-model="item.slug"
          type="text"
          placeholder="electric-vehicle-fleet-expansion"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        />
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
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">News Thumbnail / Image URL</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://images.unsplash.com/... or /storage/cms/..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      />
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">News Story Content (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Write the full press release and news coverage story..."
        min-height="200px"
      />
    </div>
  </div>
</template>
