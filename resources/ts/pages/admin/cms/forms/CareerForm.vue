<script setup lang="ts">
import type { CareerItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'

const item = defineModel<Partial<CareerItem>>('item', { required: true })
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Job Title *</label>
      <input
        v-model="item.job_title"
        type="text"
        required
        placeholder="e.g. Senior Fleet Operations Supervisor"
        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
      >
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Department</label>
        <input
          v-model="item.department"
          type="text"
          placeholder="e.g. Logistics & Fleet"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Location</label>
        <input
          v-model="item.location"
          type="text"
          placeholder="e.g. Kathmandu / Pokhara"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Employment Type</label>
        <select
          v-model="item.employment_type"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
          <option value="Full-time">
            Full-time
          </option>
          <option value="Part-time">
            Part-time
          </option>
          <option value="Contract">
            Contract
          </option>
          <option value="Internship">
            Internship
          </option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Salary Range / Compensation</label>
        <input
          v-model="item.salary_range"
          type="text"
          placeholder="e.g. NPR 60,000 - 90,000 / month"
          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 font-medium"
        >
      </div>
      <div>
        <AppDatePicker
          v-model="item.deadline"
          label="Application Deadline"
          placeholder="Select deadline date..."
        />
      </div>
    </div>

    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Job Description, Duties & Requirements (Rich Text) *</label>
      <RichTextEditor
        v-model="item.description"
        placeholder="Detail the candidate roles, requirements, benefits, and how to apply..."
        min-height="220px"
      />
    </div>
  </div>
</template>
