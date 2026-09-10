<script setup lang="ts">
import type { AlbumItem } from '../types'

const props = defineProps<{
  item: Partial<AlbumItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Album Name / Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. VIP Fleet Showcase 2026"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Cover Image URL</label>
      <input
        v-model="item.cover_image"
        type="text"
        placeholder="https://images.unsplash.com/..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Album Description</label>
      <textarea
        v-model="item.description"
        rows="3"
        placeholder="Brief description of this photo album or gallery..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      ></textarea>
    </div>
  </div>
</template>
