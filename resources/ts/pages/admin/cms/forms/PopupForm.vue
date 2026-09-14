<script setup lang="ts">
import type { PopupItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'
import FormToggle from '@/components/FormToggle.vue'

const item = defineModel<Partial<PopupItem>>('item', { required: true })
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
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Popup Type</label>
        <select
          v-model="item.popup_type"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
          <option value="modal">
            Center Modal Dialog
          </option>
          <option value="banner_top">
            Top Notification Banner
          </option>
          <option value="corner_toast">
            Bottom-Right Corner Toast
          </option>
        </select>
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Target Action URL</label>
        <input
          v-model="item.link_url"
          type="text"
          placeholder="/cars?discount=holiday"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <AppDatePicker
          v-model="item.start_date"
          label="Display Start Date"
          placeholder="Select start date..."
        />
      </div>
      <div>
        <AppDatePicker
          v-model="item.end_date"
          label="Display Expiry Date"
          placeholder="Select expiry date..."
        />
      </div>
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Graphic / Image URL</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://... or /storage/popups/..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Popup Body Content (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Offer details, terms, coupon code, discount highlights..."
        min-height="160px"
      />
    </div>

    <div>
      <FormToggle
        v-model="item.show_button"
        label="Show Call-To-Action Button"
        description="Include an action button linking to the target URL"
        active-text="Button Enabled"
        inactive-text="No Button"
      />
    </div>
  </div>
</template>
