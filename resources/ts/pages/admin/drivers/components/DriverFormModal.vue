<script setup lang="ts">
import type { DriverItem } from '../types'

defineProps<{
  show: boolean
  isEditing: boolean
  owners: Array<any>
  submitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save'): void
}>()

const form = defineModel<Partial<DriverItem>>('form', { required: true })
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-steering-2-line text-indigo-600" />
          <span>{{ isEditing ? 'Edit Chauffeur / Driver Profile' : 'Register New Chauffeur / Driver' }}</span>
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
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Driver Full Name *</label>
          <input
            v-model="form.name"
            type="text"
            required
            placeholder="e.g. Samuel Rodriguez"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
          >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Phone Number *</label>
            <input
              v-model="form.phone"
              type="text"
              required
              placeholder="+1 (555) 456-7890"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Email Address (Optional)</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="driver@example.com"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Commercial License # *</label>
            <input
              v-model="form.license_number"
              type="text"
              required
              placeholder="DL-928172648"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Years of Experience</label>
            <input
              v-model="form.experience_years"
              type="number"
              min="0"
              placeholder="3"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Fleet Owner Affiliation</label>
            <select
              v-model="form.owner_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
              <option value="">
                Independent / Platform Driver
              </option>
              <option
                v-for="owner in owners"
                :key="owner.id"
                :value="owner.id"
              >
                {{ owner.full_name || owner.name }} (ID: {{ owner.id }})
              </option>
            </select>
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Status</label>
            <select
              v-model="form.status"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
              <option value="active">
                Active & Available
              </option>
              <option value="inactive">
                Inactive / On Leave
              </option>
            </select>
          </div>
        </div>

        <div>
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Residential Address</label>
          <input
            v-model="form.address"
            type="text"
            placeholder="San Diego, CA"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
          >
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
            {{ submitting ? 'Saving...' : (isEditing ? 'Save Changes' : 'Register Driver') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
