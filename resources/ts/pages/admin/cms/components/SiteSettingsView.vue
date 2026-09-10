<script setup lang="ts">
import type { CmsItem } from '../types'

const props = defineProps<{
  settings: CmsItem
  submitting: boolean
}>()

const emit = defineEmits<{
  (e: 'save'): void
}>()
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden p-4 sm:p-6">
    <div class="mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
      <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
        <i class="ri-settings-4-line text-indigo-600" />
        Global Site Configuration & SEO Parameters
      </h3>
      <p class="text-xs text-slate-500 mt-0.5">
        Manage corporate identity, contact info, meta search tags, and social networks for the entire public platform.
      </p>
    </div>

    <form class="space-y-5" @submit.prevent="emit('save')">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold mb-1">Company / Platform Name</label>
          <input
            v-model="settings.company_name"
            type="text"
            placeholder="e.g. DriveElite Rentals"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
        <div>
          <label class="block text-xs font-bold mb-1">Official Contact Email</label>
          <input
            v-model="settings.email"
            type="email"
            placeholder="support@company.com"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold mb-1">Mobile / Helpline</label>
          <input
            v-model="settings.mobile"
            type="text"
            placeholder="+1 (555) 019-2834"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
        <div>
          <label class="block text-xs font-bold mb-1">Website URL</label>
          <input
            v-model="settings.website"
            type="text"
            placeholder="https://example.com"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold mb-1">Headquarters Address</label>
        <input
          v-model="settings.address"
          type="text"
          placeholder="123 Luxury Ave, Suite 400, Los Angeles, CA"
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
        />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold mb-1">SEO Meta Title</label>
          <input
            v-model="settings.seo_title"
            type="text"
            placeholder="Luxury Car Rental & Fleet Management"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
        <div>
          <label class="block text-xs font-bold mb-1">Meta Keywords</label>
          <input
            v-model="settings.meta_keywords"
            type="text"
            placeholder="car rental, luxury cars, car hire, fleet"
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold mb-1">SEO Meta Description</label>
        <textarea
          v-model="settings.seo_description"
          rows="2"
          placeholder="Brief meta search description for Google/Bing crawlers..."
          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
        ></textarea>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold mb-1">Facebook URL</label>
          <input
            v-model="settings.facebook_url"
            type="text"
            placeholder="https://facebook.com/..."
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
        <div>
          <label class="block text-xs font-bold mb-1">Instagram URL</label>
          <input
            v-model="settings.instagram_url"
            type="text"
            placeholder="https://instagram.com/..."
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
        <div>
          <label class="block text-xs font-bold mb-1">Twitter / X URL</label>
          <input
            v-model="settings.twitter_url"
            type="text"
            placeholder="https://x.com/..."
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          />
        </div>
      </div>

      <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
        <button
          type="submit"
          :disabled="submitting"
          class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer flex items-center gap-2"
        >
          <i class="ri-save-line" />
          <span>{{ submitting ? 'Saving Settings...' : 'Save Site Settings' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>
