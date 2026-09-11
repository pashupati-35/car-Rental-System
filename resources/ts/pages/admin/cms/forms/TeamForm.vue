<script setup lang="ts">
import type { TeamItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'

const item = defineModel<Partial<TeamItem>>('item', { required: true })
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
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Designation / Position</label>
        <input
          v-model="item.designation"
          type="text"
          placeholder="e.g. Fleet Operations Director"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Official Email</label>
        <input
          v-model="item.email"
          type="email"
          placeholder="sarah@company.com"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Profile Photo URL / Path</label>
      <input
        v-model="item.image"
        type="text"
        placeholder="https://images.unsplash.com/... or /storage/teams/..."
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">LinkedIn URL</label>
        <input
          v-model="item.linkedin_url"
          type="text"
          placeholder="https://linkedin.com/in/..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Twitter / X URL</label>
        <input
          v-model="item.twitter_url"
          type="text"
          placeholder="https://x.com/..."
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Phone</label>
        <input
          v-model="item.phone"
          type="text"
          placeholder="+977-9800000000"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Biography / Overview (Rich Text)</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Brief career highlights, specialization, and executive background..."
        min-height="150px"
      />
    </div>
  </div>
</template>
