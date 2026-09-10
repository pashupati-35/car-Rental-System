<script setup lang="ts">
import type { TeamItem } from '../types'

const props = defineProps<{
  item: Partial<TeamItem>
}>()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Team Member Name *</label>
      <input
        v-model="item.name"
        type="text"
        required
        placeholder="e.g. Sarah Jenkins"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Designation / Role</label>
        <input
          v-model="item.designation"
          type="text"
          placeholder="e.g. Head of Customer Experience"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Work Email</label>
        <input
          v-model="item.email"
          type="email"
          placeholder="sarah@company.com"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Profile Photo URL</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://images.unsplash.com/..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">LinkedIn URL</label>
        <input
          v-model="item.linkedin_url"
          type="text"
          placeholder="https://linkedin.com/in/..."
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Twitter / X URL</label>
        <input
          v-model="item.twitter_url"
          type="text"
          placeholder="https://x.com/..."
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Phone</label>
        <input
          v-model="item.phone"
          type="text"
          placeholder="+1 (555) 012-3456"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    </div>
  </div>
</template>
