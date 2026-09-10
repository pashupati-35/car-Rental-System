<script setup lang="ts">
import type { PageItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'

const item = defineModel<Partial<PageItem>>('item', { required: true })
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Page Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. Terms & Conditions, Privacy Policy, About Us"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Page URL Slug</label>
        <input
          v-model="item.slug"
          type="text"
          placeholder="terms-and-conditions"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Banner / Header Image URL</label>
        <input
          v-model="item.banner_image"
          type="text"
          placeholder="https://images.unsplash.com/... or /storage/cms/..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Page Content (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Design full structured page with headings, lists, policies, and embedded links..."
        min-height="250px"
      />
    </div>
  </div>
</template>
