<script setup lang="ts">
import type { AlbumItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'

const item = defineModel<Partial<AlbumItem>>('item', { required: true })
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
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Cover Image URL / Path</label>
      <input
        v-model="item.cover_image"
        type="text"
        placeholder="https://images.unsplash.com/... or /storage/albums/..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Album Description (Rich Text)</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Brief description of this photo album or gallery..."
        min-height="150px"
      />
    </div>
  </div>
</template>
