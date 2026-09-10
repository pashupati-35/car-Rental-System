<script setup lang="ts">
import type { PartnerItem } from '../types'

const props = defineProps<{
  item: Partial<PartnerItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Partner / Brand Name *</label>
      <input
        v-model="item.name"
        type="text"
        required
        placeholder="e.g. Continental Insurance Co."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Partner Logo Image URL</label>
        <input
          v-model="item.logo"
          type="text"
          placeholder="https://..."
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Official Website URL</label>
        <input
          v-model="item.website_url"
          type="text"
          placeholder="https://partner-company.com"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Partnership Overview / Notes</label>
      <textarea
        v-model="item.description"
        rows="3"
        placeholder="Information regarding this corporate partnership or collaboration..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      ></textarea>
    </div>
  </div>
</template>
