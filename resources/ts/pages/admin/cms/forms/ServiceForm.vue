<script setup lang="ts">
import type { ServiceItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'

const item = defineModel<Partial<ServiceItem>>('item', { required: true })
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Service Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. Luxury Chauffeur & Airport Transfer"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Starting Price ($ / NPR)</label>
        <input
          v-model="item.price"
          type="number"
          step="0.01"
          placeholder="99.00"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Icon Class (Remixicon)</label>
        <input
          v-model="item.icon"
          type="text"
          placeholder="ri-car-line or ri-shield-check-line"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Featured Image URL / Path</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://images.unsplash.com/... or /storage/services/..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Short Summary</label>
      <input
        v-model="item.short_description"
        type="text"
        placeholder="Short one-line highlight of the service..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Full Service Description (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Describe service features, fleet options, inclusions, and terms..."
        min-height="180px"
      />
    </div>
  </div>
</template>
