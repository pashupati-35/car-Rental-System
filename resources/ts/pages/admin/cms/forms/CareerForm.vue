<script setup lang="ts">
import type { CareerItem } from '../types'

const props = defineProps<{
  item: Partial<CareerItem>
}>()
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
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Department</label>
        <input
          v-model="item.department"
          type="text"
          placeholder="e.g. Logistics & Fleet"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Location</label>
        <input
          v-model="item.location"
          type="text"
          placeholder="e.g. Los Angeles, CA (Hybrid)"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        />
      </div>
      <div>
        <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Employment Type</label>
        <select
          v-model="item.employment_type"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
        >
          <option value="Full-time">Full-time</option>
          <option value="Part-time">Part-time</option>
          <option value="Contract">Contract</option>
          <option value="Internship">Internship</option>
        </select>
      </div>
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Salary Range / Compensation</label>
      <input
        v-model="item.salary_range"
        type="text"
        placeholder="e.g. $65,000 - $85,000 / year"
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      />
    </div>
    <div>
      <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Job Description & Responsibilities</label>
      <textarea
        v-model="item.description"
        rows="5"
        placeholder="Outline candidate qualifications, duties, and requirements..."
        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
      ></textarea>
    </div>
  </div>
</template>
