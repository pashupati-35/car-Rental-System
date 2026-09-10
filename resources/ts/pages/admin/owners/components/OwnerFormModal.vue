<script setup lang="ts">
import type { OwnerItem } from '../types'

defineProps<{
  show: boolean
  isEditing: boolean
  submitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save'): void
}>()

const form = defineModel<Partial<OwnerItem>>('form', { required: true })
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-user-star-line text-indigo-600" />
          <span>{{ isEditing ? 'Edit Fleet Owner Profile' : 'Register New Fleet Owner' }}</span>
        </h3>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <div
        v-if="errorMessage"
        class="p-3 rounded-2xl bg-rose-50 text-rose-800 text-xs font-semibold"
      >
        {{ errorMessage }}
      </div>

      <form
        class="space-y-4 text-xs"
        @submit.prevent="emit('save')"
      >
        <div>
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Full Name *</label>
          <input
            v-model="form.full_name"
            type="text"
            required
            placeholder="e.g. Alexander Hayes"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
          >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Email Address *</label>
            <input
              v-model="form.email"
              type="email"
              required
              :disabled="isEditing"
              placeholder="owner@example.com"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 disabled:opacity-60"
            >
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Contact Number</label>
            <input
              v-model="form.contact_number"
              type="text"
              placeholder="+1 (555) 019-2834"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Gender</label>
            <select
              v-model="form.gender"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
              <option value="male">
                Male
              </option>
              <option value="female">
                Female
              </option>
              <option value="other">
                Other
              </option>
            </select>
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Office / Physical Address</label>
            <input
              v-model="form.address"
              type="text"
              placeholder="Beverly Hills, CA"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
          </div>
        </div>

        <div
          v-if="!isEditing"
          class="p-3 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/60 text-indigo-900 dark:text-indigo-200 text-[11px] leading-relaxed"
        >
          <i
            class="ri-shield-check-line font-bold"
            style="margin-right: 0.25rem"
          />
          An automated invitation email containing a secure password setup link will be dispatched to this owner upon registration.
        </div>

        <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer"
          >
            {{ submitting ? 'Saving...' : (isEditing ? 'Save Changes' : 'Register Owner') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
