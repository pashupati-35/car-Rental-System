<script setup lang="ts">
import type { TestimonialItem } from '../types'

const props = defineProps<{
  item: Partial<TestimonialItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Client / Reviewer Name *</label>
        <input
          v-model="item.name"
          type="text"
          required
          placeholder="e.g. David Richardson"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Rating (1 to 5 Stars)</label>
        <input
          v-model="item.rating"
          type="number"
          min="1"
          max="5"
          step="0.5"
          placeholder="5"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Designation / Role</label>
        <input
          v-model="item.designation"
          type="text"
          placeholder="e.g. Executive Director"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Company Name</label>
        <input
          v-model="item.company_name"
          type="text"
          placeholder="e.g. Apex Global"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Client Avatar / Photo URL</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Review Feedback *</label>
      <textarea
        v-model="item.description"
        rows="4"
        required
        placeholder="The service was outstanding! The Mercedes S-Class was pristine..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      ></textarea>
    </div>
  </div>
</template>
