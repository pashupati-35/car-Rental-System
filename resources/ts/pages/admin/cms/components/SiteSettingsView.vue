<script setup lang="ts">
import type { CmsItem } from '../types'
import RichTextEditor from '@/components/RichTextEditor.vue'
import FormToggle from '@/components/FormToggle.vue'

const props = defineProps<{
  settings: CmsItem
  submitting: boolean
}>()

const emit = defineEmits<{
  (e: 'save'): void
}>()
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden p-5 sm:p-7">
    <div class="mb-6 border-b border-slate-100 dark:border-slate-800 pb-4 flex items-center justify-between">
      <div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-settings-4-line text-indigo-600 dark:text-indigo-400" />
          Global Platform Settings & SEO Configuration
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">
          Configure corporate identity, contact points, SEO search metadata, cookie consent, and public links.
        </p>
      </div>

      <button
        type="button"
        :disabled="submitting"
        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer flex items-center gap-1.5 transition-all"
        @click="emit('save')"
      >
        <i v-if="submitting" class="ri-loader-4-line animate-spin" />
        <i v-else class="ri-save-line" />
        <span>{{ submitting ? 'Saving...' : 'Save Settings' }}</span>
      </button>
    </div>

    <form class="space-y-6" @submit.prevent="emit('save')">
      <!-- Section 1: Brand & Contact -->
      <div class="space-y-4">
        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800 pb-1">
          1. Company Identity & Contact Points
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Company / Brand Name</label>
            <input
              v-model="settings.company_name"
              type="text"
              placeholder="e.g. AutoRent Nepal"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Official Support Email</label>
            <input
              v-model="settings.email"
              type="email"
              placeholder="support@carrental.local"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Helpline / Phone</label>
            <input
              v-model="settings.mobile"
              type="text"
              placeholder="+977-9841234567"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Physical Address</label>
            <input
              v-model="settings.address"
              type="text"
              placeholder="Kathmandu, Bagmati, Nepal"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Website URL</label>
            <input
              v-model="settings.website"
              type="text"
              placeholder="https://carrental.local"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>
      </div>

      <!-- Section 2: Rich Text Descriptions -->
      <div class="space-y-4">
        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800 pb-1">
          2. About & Footer Descriptions (Rich Text)
        </h4>

        <div>
          <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Platform Overview & About Description</label>
          <RichTextEditor
            v-model="settings.description"
            placeholder="Provide company background, mission statement, fleet scale..."
            min-height="160px"
          />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Copyright Text</label>
            <input
              v-model="settings.copy_right_text"
              type="text"
              placeholder="© 2026 AutoRent Nepal. All rights reserved."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Cookie Notice Text</label>
            <input
              v-model="settings.cookie_content_text"
              type="text"
              placeholder="We use cookies to improve your rental experience."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>
      </div>

      <!-- Section 3: SEO & Social Media -->
      <div class="space-y-4">
        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800 pb-1">
          3. SEO Metadata & Social Links
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">SEO Meta Title</label>
            <input
              v-model="settings.seo_title"
              type="text"
              placeholder="AutoRent Nepal - Premium Car Rental & Fleet Portal"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Meta Keywords</label>
            <input
              v-model="settings.seo_keyword"
              type="text"
              placeholder="car rental kathmandu, luxury cars nepal, hire car"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">SEO Meta Description</label>
          <textarea
            v-model="settings.seo_description"
            rows="2"
            placeholder="Brief snippet shown in search engine results..."
            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
          ></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Facebook</label>
            <input
              v-model="settings.facebook"
              type="text"
              placeholder="https://facebook.com/..."
              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            />
          </div>
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Instagram</label>
            <input
              v-model="settings.instagram"
              type="text"
              placeholder="https://instagram.com/..."
              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            />
          </div>
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">LinkedIn</label>
            <input
              v-model="settings.linkedin"
              type="text"
              placeholder="https://linkedin.com/..."
              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            />
          </div>
          <div>
            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">WhatsApp</label>
            <input
              v-model="settings.whatsapp"
              type="text"
              placeholder="+977-9841234567"
              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            />
          </div>
        </div>
      </div>

      <!-- Section 4: System Toggles -->
      <div class="space-y-3 pt-2">
        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800 pb-1">
          4. Feature Toggles
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormToggle
            v-model="settings.enable_cookies"
            label="Enable Cookie Consent Banner"
            description="Display privacy and cookie agreement prompt to visiting customers"
            active-text="Cookies Enabled"
            inactive-text="Disabled"
          />
          <FormToggle
            v-model="settings.display_smtp"
            label="Enable System SMTP Mailing"
            description="Route email notifications and templates via dedicated mail server"
            active-text="SMTP Active"
            inactive-text="Default Mailer"
          />
        </div>
      </div>

      <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
        <button
          type="submit"
          :disabled="submitting"
          class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer flex items-center gap-2"
        >
          <i v-if="submitting" class="ri-loader-4-line animate-spin" />
          <i v-else class="ri-save-line" />
          <span>{{ submitting ? 'Saving Settings...' : 'Save Site Settings' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>
