<script setup lang="ts">
import type { NoticeItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'

const props = defineProps<{
  item: Partial<NoticeItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Notice Title *</label>
      <input
        v-model="item.title"
        type="text"
        required
        placeholder="e.g. System Scheduled Maintenance Window, Holiday Operations"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <AppDatePicker
          v-model="item.notice_date"
          label="Notice Date"
          placeholder="Select notice date..."
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Attachment Document / File URL</label>
        <input
          v-model="item.file_url"
          type="text"
          placeholder="https://... or /storage/documents/..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        />
      </div>
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Detailed Notice Content (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Provide full notice announcement details, impact, instructions, or schedules..."
        min-height="180px"
      />
    </div>
  </div>
</template>
