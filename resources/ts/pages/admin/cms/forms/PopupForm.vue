<script setup lang="ts">
import type { PopupItem } from '../types'

const props = defineProps<{
  item: Partial<PopupItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Popup Headline / Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. Special Holiday Weekend 25% Off"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Popup Type</label>
        <select
          v-model="item.popup_type"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        >
          <option value="modal">Center Modal Dialog</option>
          <option value="banner_top">Top Notification Banner</option>
          <option value="corner_toast">Bottom-Right Corner Toast</option>
        </select>
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Target Action URL</label>
        <input
          v-model="item.link_url"
          type="text"
          placeholder="/cars?discount=holiday"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Graphic / Image URL</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Popup Body Content</label>
      <textarea
        v-model="item.description"
        rows="3"
        placeholder="Offer details, terms, coupon code, etc."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      ></textarea>
    </div>
  </div>
</template>
