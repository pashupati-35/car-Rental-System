<script setup lang="ts">
import { computed, ref } from 'vue'
import type { CmsItem } from '../types'
import type { SiteSettingItem } from '@/types/cms/SiteSettingType'
import RichTextEditor from '@/components/RichTextEditor.vue'
import FormToggle from '@/components/FormToggle.vue'
import MessageBox from '@/components/MessageBox.vue'
import { resolveMediaUrl } from '@/utils/helpers'
import SiteSettingService from '@/services/cms/SiteSettingService'

defineProps<{
  submitting: boolean
}>()

const emit = defineEmits<{
  (e: 'save', payload: FormData): void
}>()

const settings = defineModel<CmsItem>('settings', { required: true })

// Stepper Step Definitions
const currentStep = ref<number>(1)

const steps = [
  { id: 1, label: 'Company & Contact', icon: 'ri-building-4-line', description: 'Identity, phone, emails, tax & address' },
  { id: 2, label: 'Content & Legal', icon: 'ri-file-text-line', description: 'About, terms, copyright & cookies' },
  { id: 3, label: 'Branding & Media', icon: 'ri-palette-line', description: 'Logos, login background & brand colors' },
  { id: 4, label: 'Social & Chat', icon: 'ri-share-forward-line', description: 'Social channels, Viber & FB chat' },
  { id: 5, label: 'SEO & Analytics', icon: 'ri-search-eye-line', description: 'Meta tags, Google Analytics & Pixels' },
  { id: 6, label: 'SMTP & Mail', icon: 'ri-mail-settings-line', description: 'SMTP server settings & test email' },
  { id: 7, label: 'Storage & Security', icon: 'ri-cloud-line', description: 'S3/R2 storage & security toggles' },
]

// Image Upload and Removal State
const imageFiles = ref<{ [key: string]: File | null }>({
  logo: null,
  app_logo: null,
  footer_logo: null,
  fav_icon: null,
  email_logo_image: null,
  login_bg_image: null,
})

const imagePreviews = ref<{ [key: string]: string }>({
  logo: '',
  app_logo: '',
  footer_logo: '',
  fav_icon: '',
  email_logo_image: '',
  login_bg_image: '',
})

const removeFlags = ref<{ [key: string]: boolean }>({
  remove_logo: false,
  remove_app_logo: false,
  remove_footer_logo: false,
  remove_fav_icon: false,
  remove_email_logo_image: false,
  remove_login_bg_image: false,
})

// Handle image selection
const handleFileSelect = (key: string, event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]

    imageFiles.value[key] = file
    imagePreviews.value[key] = URL.createObjectURL(file)
    removeFlags.value[`remove_${key}`] = false
  }
}

// Remove an image
const handleRemoveImage = (key: string) => {
  imageFiles.value[key] = null
  imagePreviews.value[key] = ''
  removeFlags.value[`remove_${key}`] = true
}

// Get Image Display Source
const getImageSrc = (key: string, savedPath?: any, savedFilename?: string): string => {
  if (imagePreviews.value[key]) {
    return imagePreviews.value[key]
  }
  if (removeFlags.value[`remove_${key}`]) {
    return ''
  }
  
  return resolveMediaUrl(savedFilename, savedPath, 'setting')
}

// SMTP Test Email State
const testEmail = ref<string>('')
const isSendingTestEmail = ref<boolean>(false)
const testEmailMessage = ref<string>('')
const testEmailError = ref<string>('')
const siteSettingService = new SiteSettingService()

const handleSendTestEmail = async () => {
  if (!testEmail.value || !testEmail.value.includes('@')) {
    testEmailError.value = 'Please enter a valid email address to receive the test email.'
    
    return
  }

  isSendingTestEmail.value = true
  testEmailMessage.value = ''
  testEmailError.value = ''

  try {
    const res = await siteSettingService.sendTestEmail(testEmail.value)
    if (res?.status === 'OK' || res) {
      testEmailMessage.value = `Test email successfully dispatched to ${testEmail.value}!`
    } else {
      testEmailError.value = 'Failed to send test email. Please verify SMTP credentials and port settings.'
    }
  } catch (err: any) {
    testEmailError.value = err.response?.data?.message || 'SMTP connection failed. Check mail server settings.'
  } finally {
    isSendingTestEmail.value = false
  }
}

// Handle Form Submission with Full FormData
const handleFormSubmit = () => {
  const formData = new FormData()
  const data = settings.value as SiteSettingItem

  // Append all scalar fields
  const excludedKeys = [
    'logo',
    'app_logo',
    'footer_logo',
    'fav_icon',
    'email_logo_image',
    'login_bg_image',
    'logo_path',
    'app_logo_path',
    'footer_logo_path',
    'fav_icon_path',
    'email_logo_path',
    'login_bg_path',
    'has_mail_password',
    'has_storage_access_key',
    'has_storage_secret_key',
    'has_recaptcha_secret_key',
    'display_smtp_setting',
    'fb_chat_json_values',
    'colors_variables_json_values',
  ]

  Object.entries(data).forEach(([key, val]) => {
    if (excludedKeys.includes(key)) return
    if (val === null || val === undefined) {
      formData.append(key, '')
    } else if (typeof val === 'boolean') {
      formData.append(key, val ? '1' : '0')
    } else {
      formData.append(key, String(val))
    }
  })

  // Append uploaded files or removal flags
  Object.keys(imageFiles.value).forEach(key => {
    if (imageFiles.value[key]) {
      formData.append(key, imageFiles.value[key] as File)
    }
    if (removeFlags.value[`remove_${key}`]) {
      formData.append(`remove_${key}`, '1')
    }
  })

  emit('save', formData)
}

const isCurrentStepValid = computed(() => {
  return true
})

const nextStep = () => {
  if (currentStep.value < steps.length) {
    currentStep.value += 1
  }
}

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value -= 1
  }
}
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
    <!-- Header with Quick Action -->
    <div class="p-5 sm:p-7 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/20">
      <div>
        <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-2xl bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shadow-xs">
            <i class="ri-settings-4-line" />
          </div>
          <span>Platform Global Settings & CMS Control</span>
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          Step-by-step master configuration for corporate identity, SMTP, cloud storage, SEO, and appearance.
        </p>
      </div>

      <div class="flex items-center gap-2 self-end sm:self-auto">
        <button
          type="button"
          :disabled="submitting"
          class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer flex items-center gap-2 transition-all"
          @click="handleFormSubmit"
        >
          <i
            v-if="submitting"
            class="ri-loader-4-line animate-spin text-sm"
          />
          <i
            v-else
            class="ri-save-line text-sm"
          />
          <span>{{ submitting ? 'Saving...' : 'Save Settings' }}</span>
        </button>
      </div>
    </div>

    <!-- Interactive Stepper Navigation Bar -->
    <div class="px-5 sm:px-7 pt-4 pb-2 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-x-auto no-scrollbar">
      <div class="flex items-center min-w-max gap-2 py-1">
        <button
          v-for="s in steps"
          :key="s.id"
          type="button"
          class="flex items-center gap-2.5 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all cursor-pointer border"
          :class="[
            currentStep === s.id
              ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-500/20'
              : currentStep > s.id
                ? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700'
                : 'bg-white dark:bg-slate-900 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800/60 border-slate-200/60 dark:border-slate-800'
          ]"
          @click="currentStep = s.id"
        >
          <div
            class="w-6 h-6 rounded-xl flex items-center justify-center text-xs font-black shrink-0"
            :class="[
              currentStep === s.id
                ? 'bg-white/20 text-white'
                : currentStep > s.id
                  ? 'bg-emerald-500 text-white'
                  : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'
            ]"
          >
            <i
              v-if="currentStep > s.id"
              class="ri-check-line text-xs"
            />
            <span v-else>{{ s.id }}</span>
          </div>
          <div class="text-left">
            <span class="block leading-tight">{{ s.label }}</span>
          </div>
        </button>
      </div>
    </div>

    <!-- Active Step Content Area -->
    <div class="p-5 sm:p-8">
      <form
        class="space-y-6"
        @submit.prevent="handleFormSubmit"
      >
        <!-- STEP 1: Company Profile & Contacts -->
        <div
          v-if="currentStep === 1"
          class="space-y-6"
        >
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-building-4-line text-indigo-500" />
                <span>1. Company Profile, Contacts & Tax Details</span>
              </h4>
              <p class="text-xs text-slate-500 mt-0.5">
                Official organization identification, primary contact telephone lines, and tax registrations.
              </p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold text-xs">Step 1 of 7</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Company / Brand Name *</label>
              <input
                v-model="settings.company_name"
                type="text"
                placeholder="e.g. AutoRent Car Rental System"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Company Slogan</label>
              <input
                v-model="settings.slogan"
                type="text"
                placeholder="e.g. Drive Your Ambition with Ease"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Tagline</label>
              <input
                v-model="settings.tagline"
                type="text"
                placeholder="e.g. Premium Mobility & Self-Drive Fleet"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Primary Contact Email</label>
              <input
                v-model="settings.email"
                type="email"
                placeholder="info@carrental.local"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Official Support Email</label>
              <input
                v-model="settings.support_email"
                type="email"
                placeholder="support@carrental.local"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Helpline / Mobile</label>
              <input
                v-model="settings.mobile"
                type="text"
                placeholder="+977-9841234567"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Office Telephone / Phone</label>
              <input
                v-model="settings.phone"
                type="text"
                placeholder="+977-1-4455667"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Physical Location Address</label>
              <input
                v-model="settings.address"
                type="text"
                placeholder="Kathmandu Metropolitan City, Bagmati Province, Nepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Address / Location Type</label>
              <input
                v-model="settings.address_type"
                type="text"
                placeholder="Headquarters / Corporate Hub"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Website URL</label>
              <input
                v-model="settings.website"
                type="text"
                placeholder="https://carrental.local"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Tax / VAT Rate (%)</label>
              <input
                v-model="settings.tax_percentage"
                type="number"
                step="0.01"
                placeholder="13.00"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">PAN Number</label>
              <input
                v-model="settings.pan_no"
                type="text"
                placeholder="PAN-60982314"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">VAT Number</label>
              <input
                v-model="settings.vat_no"
                type="text"
                placeholder="VAT-30019284"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>
        </div>

        <!-- STEP 2: Content, Legal & Rich Text -->
        <div
          v-else-if="currentStep === 2"
          class="space-y-6"
        >
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-file-text-line text-blue-500" />
                <span>2. Content, Legal Policies & Map Integrations</span>
              </h4>
              <p class="text-xs text-slate-500 mt-0.5">
                Overview narrative, terms & conditions, copyright statement, and virtual meeting links.
              </p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 font-bold text-xs">Step 2 of 7</span>
          </div>

          <div>
            <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">About Platform / Corporate Narrative (Rich Text)</label>
            <RichTextEditor
              v-model="settings.description"
              placeholder="Provide company background, fleet scale, customer trust guarantees..."
              min-height="160px"
            />
          </div>

          <div>
            <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Rental Terms & Conditions Policy (Rich Text)</label>
            <RichTextEditor
              v-model="settings.terms_condition"
              placeholder="Outline standard agreement clauses, driver license requirements, deposit policies..."
              min-height="160px"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Footer Copyright Statement</label>
              <input
                v-model="settings.copy_right_text"
                type="text"
                placeholder="© 2026 AutoRent Nepal. All rights reserved."
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Cookie Notice Message</label>
              <input
                v-model="settings.cookie_content_text"
                type="text"
                placeholder="We use essential cookies to maintain secure sessions and enhance booking flow."
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Google Maps Embed / Location Link</label>
              <input
                v-model="settings.map_url"
                type="text"
                placeholder="https://maps.google.com/embed?pb=..."
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Virtual Meeting / Zoom Link</label>
              <input
                v-model="settings.zoom_link"
                type="text"
                placeholder="https://zoom.us/j/..."
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>
        </div>

        <!-- STEP 3: Branding, Media & Theme Colors -->
        <div
          v-else-if="currentStep === 3"
          class="space-y-6"
        >
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-palette-line text-purple-500" />
                <span>3. Branding, Visual Assets & Color Scheme</span>
              </h4>
              <p class="text-xs text-slate-500 mt-0.5">
                Upload brand logos, email headers, login wallpaper, and configure primary UI colors.
              </p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 font-bold text-xs">Step 3 of 7</span>
          </div>

          <!-- Image Upload Cards Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- 1. Main Logo -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-3">
              <div>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Primary Web Logo</span>
                <span class="text-[10px] text-slate-400 block">Displayed in top navigation & invoices</span>
              </div>
              <div class="h-28 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2 relative overflow-hidden group">
                <img
                  v-if="getImageSrc('logo', settings.logo_path, settings.logo)"
                  :src="getImageSrc('logo', settings.logo_path, settings.logo)"
                  class="max-h-full max-w-full object-contain"
                  alt="Logo"
                >
                <span
                  v-else
                  class="text-xs text-slate-400 font-medium"
                >No Logo Uploaded</span>

                <button
                  v-if="getImageSrc('logo', settings.logo_path, settings.logo)"
                  type="button"
                  class="absolute top-2 right-2 w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow"
                  title="Remove Logo"
                  @click="handleRemoveImage('logo')"
                >
                  <i class="ri-delete-bin-line" />
                </button>
              </div>
              <label class="block cursor-pointer">
                <span class="w-full py-2 px-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1.5 transition-colors">
                  <i class="ri-upload-cloud-line" />
                  <span>Choose File</span>
                </span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleFileSelect('logo', $event)"
                >
              </label>
            </div>

            <!-- 2. App Logo -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-3">
              <div>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Mobile App / Square Logo</span>
                <span class="text-[10px] text-slate-400 block">Square ratio icon for mobile & PWA</span>
              </div>
              <div class="h-28 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2 relative overflow-hidden group">
                <img
                  v-if="getImageSrc('app_logo', settings.app_logo_path, settings.app_logo)"
                  :src="getImageSrc('app_logo', settings.app_logo_path, settings.app_logo)"
                  class="max-h-full max-w-full object-contain"
                  alt="App Logo"
                >
                <span
                  v-else
                  class="text-xs text-slate-400 font-medium"
                >No App Logo Uploaded</span>

                <button
                  v-if="getImageSrc('app_logo', settings.app_logo_path, settings.app_logo)"
                  type="button"
                  class="absolute top-2 right-2 w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow"
                  title="Remove App Logo"
                  @click="handleRemoveImage('app_logo')"
                >
                  <i class="ri-delete-bin-line" />
                </button>
              </div>
              <label class="block cursor-pointer">
                <span class="w-full py-2 px-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1.5 transition-colors">
                  <i class="ri-upload-cloud-line" />
                  <span>Choose File</span>
                </span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleFileSelect('app_logo', $event)"
                >
              </label>
            </div>

            <!-- 3. Footer Logo -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-3">
              <div>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Footer Logo</span>
                <span class="text-[10px] text-slate-400 block">Optimized for dark or themed footers</span>
              </div>
              <div class="h-28 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2 relative overflow-hidden group">
                <img
                  v-if="getImageSrc('footer_logo', settings.footer_logo_path, settings.footer_logo)"
                  :src="getImageSrc('footer_logo', settings.footer_logo_path, settings.footer_logo)"
                  class="max-h-full max-w-full object-contain"
                  alt="Footer Logo"
                >
                <span
                  v-else
                  class="text-xs text-slate-400 font-medium"
                >No Footer Logo</span>

                <button
                  v-if="getImageSrc('footer_logo', settings.footer_logo_path, settings.footer_logo)"
                  type="button"
                  class="absolute top-2 right-2 w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow"
                  title="Remove Footer Logo"
                  @click="handleRemoveImage('footer_logo')"
                >
                  <i class="ri-delete-bin-line" />
                </button>
              </div>
              <label class="block cursor-pointer">
                <span class="w-full py-2 px-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1.5 transition-colors">
                  <i class="ri-upload-cloud-line" />
                  <span>Choose File</span>
                </span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleFileSelect('footer_logo', $event)"
                >
              </label>
            </div>

            <!-- 4. Favicon -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-3">
              <div>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Favicon (.ico / .png)</span>
                <span class="text-[10px] text-slate-400 block">Browser tab icon (32x32 recommended)</span>
              </div>
              <div class="h-28 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2 relative overflow-hidden group">
                <img
                  v-if="getImageSrc('fav_icon', settings.fav_icon_path, settings.fav_icon)"
                  :src="getImageSrc('fav_icon', settings.fav_icon_path, settings.fav_icon)"
                  class="w-10 h-10 object-contain"
                  alt="Favicon"
                >
                <span
                  v-else
                  class="text-xs text-slate-400 font-medium"
                >No Favicon</span>

                <button
                  v-if="getImageSrc('fav_icon', settings.fav_icon_path, settings.fav_icon)"
                  type="button"
                  class="absolute top-2 right-2 w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow"
                  title="Remove Favicon"
                  @click="handleRemoveImage('fav_icon')"
                >
                  <i class="ri-delete-bin-line" />
                </button>
              </div>
              <label class="block cursor-pointer">
                <span class="w-full py-2 px-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1.5 transition-colors">
                  <i class="ri-upload-cloud-line" />
                  <span>Choose File</span>
                </span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleFileSelect('fav_icon', $event)"
                >
              </label>
            </div>

            <!-- 5. Email Header Logo -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-3">
              <div>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Email Template Header Logo</span>
                <span class="text-[10px] text-slate-400 block">Used in automated transactional mailers</span>
              </div>
              <div class="h-28 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2 relative overflow-hidden group">
                <img
                  v-if="getImageSrc('email_logo_image', settings.email_logo_path, settings.email_logo_image)"
                  :src="getImageSrc('email_logo_image', settings.email_logo_path, settings.email_logo_image)"
                  class="max-h-full max-w-full object-contain"
                  alt="Email Logo"
                >
                <span
                  v-else
                  class="text-xs text-slate-400 font-medium"
                >No Email Logo</span>

                <button
                  v-if="getImageSrc('email_logo_image', settings.email_logo_path, settings.email_logo_image)"
                  type="button"
                  class="absolute top-2 right-2 w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow"
                  title="Remove Email Logo"
                  @click="handleRemoveImage('email_logo_image')"
                >
                  <i class="ri-delete-bin-line" />
                </button>
              </div>
              <label class="block cursor-pointer">
                <span class="w-full py-2 px-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1.5 transition-colors">
                  <i class="ri-upload-cloud-line" />
                  <span>Choose File</span>
                </span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleFileSelect('email_logo_image', $event)"
                >
              </label>
            </div>

            <!-- 6. Login Background Wallpaper -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-3">
              <div>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Login Background Wallpaper</span>
                <span class="text-[10px] text-slate-400 block">Hero image on admin/customer login portal</span>
              </div>
              <div class="h-28 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2 relative overflow-hidden group">
                <img
                  v-if="getImageSrc('login_bg_image', settings.login_bg_path, settings.login_bg_image)"
                  :src="getImageSrc('login_bg_image', settings.login_bg_path, settings.login_bg_image)"
                  class="w-full h-full object-cover"
                  alt="Login Background"
                >
                <span
                  v-else
                  class="text-xs text-slate-400 font-medium"
                >No Login Image</span>

                <button
                  v-if="getImageSrc('login_bg_image', settings.login_bg_path, settings.login_bg_image)"
                  type="button"
                  class="absolute top-2 right-2 w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow"
                  title="Remove Wallpaper"
                  @click="handleRemoveImage('login_bg_image')"
                >
                  <i class="ri-delete-bin-line" />
                </button>
              </div>
              <label class="block cursor-pointer">
                <span class="w-full py-2 px-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1.5 transition-colors">
                  <i class="ri-upload-cloud-line" />
                  <span>Choose File</span>
                </span>
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleFileSelect('login_bg_image', $event)"
                >
              </label>
            </div>
          </div>

          <!-- Color Customizer -->
          <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-4">
            <h5 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
              Theme Color Palette
            </h5>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Primary Brand Color</label>
                <div class="flex items-center gap-2">
                  <input
                    v-model="settings.primary_color"
                    type="color"
                    class="w-10 h-10 rounded-xl border-0 cursor-pointer p-0.5 bg-transparent"
                  >
                  <input
                    v-model="settings.primary_color"
                    type="text"
                    placeholder="#4f46e5"
                    class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold"
                  >
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Secondary Accent Color</label>
                <div class="flex items-center gap-2">
                  <input
                    v-model="settings.secondary_color"
                    type="color"
                    class="w-10 h-10 rounded-xl border-0 cursor-pointer p-0.5 bg-transparent"
                  >
                  <input
                    v-model="settings.secondary_color"
                    type="text"
                    placeholder="#06b6d4"
                    class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold"
                  >
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Login Overlay Color</label>
                <div class="flex items-center gap-2">
                  <input
                    v-model="settings.login_bg_color"
                    type="color"
                    class="w-10 h-10 rounded-xl border-0 cursor-pointer p-0.5 bg-transparent"
                  >
                  <input
                    v-model="settings.login_bg_color"
                    type="text"
                    placeholder="#0f172a"
                    class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- STEP 4: Social Channels & Messaging -->
        <div
          v-else-if="currentStep === 4"
          class="space-y-6"
        >
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-share-forward-line text-emerald-500" />
                <span>4. Social Networks, Chat & Messaging Links</span>
              </h4>
              <p class="text-xs text-slate-500 mt-0.5">
                Official social profile channels, instant messaging links, and live customer chat widgets.
              </p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-bold text-xs">Step 4 of 7</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-facebook-circle-fill text-blue-600 text-sm" /> Facebook Page URL
              </label>
              <input
                v-model="settings.facebook"
                type="text"
                placeholder="https://facebook.com/autorentnepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-instagram-line text-pink-600 text-sm" /> Instagram Profile URL
              </label>
              <input
                v-model="settings.instagram"
                type="text"
                placeholder="https://instagram.com/autorentnepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-linkedin-box-fill text-blue-700 text-sm" /> LinkedIn Page URL
              </label>
              <input
                v-model="settings.linkedin"
                type="text"
                placeholder="https://linkedin.com/company/autorent"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-twitter-x-line text-slate-900 dark:text-white text-sm" /> Twitter / X Profile
              </label>
              <input
                v-model="settings.twitter"
                type="text"
                placeholder="https://x.com/autorentnepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-youtube-fill text-rose-600 text-sm" /> YouTube Channel URL
              </label>
              <input
                v-model="settings.youtube"
                type="text"
                placeholder="https://youtube.com/@autorentnepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-tiktok-fill text-slate-900 dark:text-white text-sm" /> TikTok Profile URL
              </label>
              <input
                v-model="settings.tiktok"
                type="text"
                placeholder="https://tiktok.com/@autorentnepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-whatsapp-fill text-emerald-600 text-sm" /> WhatsApp Direct Link / Number
              </label>
              <input
                v-model="settings.whatsapp"
                type="text"
                placeholder="+977-9841234567 or https://wa.me/9779841234567"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-chat-voice-fill text-purple-600 text-sm" /> Viber Channel / Number
              </label>
              <input
                v-model="settings.viber"
                type="text"
                placeholder="+977-9841234567"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <i class="ri-pinterest-fill text-red-600 text-sm" /> Pinterest Profile URL
              </label>
              <input
                v-model="settings.pininterest"
                type="text"
                placeholder="https://pinterest.com/autorent"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Facebook Messenger / Customer Chat Widget Snippet</label>
            <textarea
              v-model="settings.facebook_chat_widgets"
              rows="3"
              placeholder="Paste custom Facebook Customer Chat JavaScript or Page Plugin code here..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-mono focus:ring-2 focus:ring-indigo-500"
            />
          </div>
        </div>

        <!-- STEP 5: SEO & Analytics -->
        <div
          v-else-if="currentStep === 5"
          class="space-y-6"
        >
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-search-eye-line text-cyan-500" />
                <span>5. Search Engine Optimization (SEO) & Tracking Scripts</span>
              </h4>
              <p class="text-xs text-slate-500 mt-0.5">
                Meta titles, descriptions, keyword tags, Google Analytics and Meta Pixel tracking codes.
              </p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-400 font-bold text-xs">Step 5 of 7</span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Default SEO Meta Title</label>
              <input
                v-model="settings.seo_title"
                type="text"
                placeholder="AutoRent Nepal - Premium Car Rental & Fleet Portal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Meta Keywords</label>
              <input
                v-model="settings.seo_keyword"
                type="text"
                placeholder="car rental kathmandu, luxury fleet nepal, rent car self drive"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">SEO Meta Description</label>
            <textarea
              v-model="settings.seo_description"
              rows="3"
              placeholder="Search engine index snippet describing fleet rental options, transparent rates, and verification guarantees..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Google Analytics (G-XXXXXXX or Script)</label>
              <textarea
                v-model="settings.google_analytics"
                rows="3"
                placeholder="G-ABC123XYZ or gtag('js', new Date());"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-mono focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div>
              <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Facebook / Meta Pixels</label>
              <textarea
                v-model="settings.pixels"
                rows="3"
                placeholder="fbq('init', '1234567890'); fbq('track', 'PageView');"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-mono focus:ring-2 focus:ring-indigo-500"
              />
            </div>
          </div>

          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3">
            <h5 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
              Google reCAPTCHA Security Keys
            </h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">reCAPTCHA Site Key</label>
                <input
                  v-model="settings.recaptcha_site_key"
                  type="text"
                  placeholder="6LeIx0aBAAAA..."
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                >
              </div>

              <div>
                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">reCAPTCHA Secret Key</label>
                <input
                  v-model="settings.recaptcha_secret_key"
                  type="password"
                  placeholder="Leave empty to retain existing secret"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- STEP 6: SMTP & Mail Server -->
        <div
          v-else-if="currentStep === 6"
          class="space-y-6"
        >
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-mail-settings-line text-amber-500" />
                <span>6. SMTP Email Server & Dispatch Testing</span>
              </h4>
              <p class="text-xs text-slate-500 mt-0.5">
                Configure dedicated mail server routing and verify live email delivery.
              </p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 font-bold text-xs">Step 6 of 7</span>
          </div>

          <div class="p-4 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/60 flex items-center justify-between gap-4">
            <div>
              <span class="text-xs font-bold text-slate-900 dark:text-white block">System SMTP Mailing Engine</span>
              <span class="text-[11px] text-slate-500 block">Enable or disable external mail server relay for system emails</span>
            </div>
            <FormToggle
              v-model="settings.display_smtp"
              active-text="SMTP Active"
              inactive-text="Default Mailer"
            />
          </div>

          <div
            v-if="settings.display_smtp"
            class="space-y-4"
          >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Mail Driver</label>
                <select
                  v-model="settings.mail_driver"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="smtp">
                    SMTP (Standard)
                  </option>
                  <option value="sendmail">
                    Sendmail
                  </option>
                  <option value="mailgun">
                    Mailgun
                  </option>
                  <option value="ses">
                    Amazon SES
                  </option>
                  <option value="log">
                    Local Log (Dev)
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">SMTP Host *</label>
                <input
                  v-model="settings.mail_host"
                  type="text"
                  placeholder="e.g. smtp.mailgun.org or mailhog"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">SMTP Port *</label>
                <input
                  v-model="settings.mail_port"
                  type="text"
                  placeholder="587, 465, or 1025"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">SMTP Username</label>
                <input
                  v-model="settings.mail_user_name"
                  type="text"
                  placeholder="postmaster@yourdomain.com"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">SMTP Password</label>
                <input
                  v-model="settings.mail_password"
                  type="password"
                  placeholder="Leave empty to retain existing password"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Encryption Protocol</label>
                <select
                  v-model="settings.mail_encryption"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="tls">
                    TLS (Recommended)
                  </option>
                  <option value="ssl">
                    SSL
                  </option>
                  <option value="none">
                    None / Plain
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Default Sender Name</label>
                <input
                  v-model="settings.mail_sender_name"
                  type="text"
                  placeholder="AutoRent Nepal Notifications"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Default Sender Email Address</label>
                <input
                  v-model="settings.mail_sender_address"
                  type="email"
                  placeholder="noreply@carrental.local"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
              </div>
            </div>

            <!-- Test Email Dispatch Tool -->
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3 mt-4">
              <h5 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <i class="ri-send-plane-2-line text-indigo-500" />
                <span>Test Live SMTP Email Dispatch</span>
              </h5>
              <p class="text-xs text-slate-500">
                Send an immediate test message using the saved credentials to verify mailbox connectivity.
              </p>

              <MessageBox
                v-if="testEmailMessage"
                :message="testEmailMessage"
                type="success"
              />
              <MessageBox
                v-if="testEmailError"
                :message="testEmailError"
                type="error"
              />

              <div class="flex flex-col sm:flex-row gap-3">
                <input
                  v-model="testEmail"
                  type="email"
                  placeholder="Enter recipient email address (e.g. admin@example.com)"
                  class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-indigo-500"
                >
                <button
                  type="button"
                  :disabled="isSendingTestEmail"
                  class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs shadow-xs disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2 transition-all shrink-0"
                  @click="handleSendTestEmail"
                >
                  <i
                    v-if="isSendingTestEmail"
                    class="ri-loader-4-line animate-spin text-sm"
                  />
                  <i
                    v-else
                    class="ri-mail-send-line text-sm"
                  />
                  <span>{{ isSendingTestEmail ? 'Dispatching Test...' : 'Send Test Email' }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- STEP 7: Cloud Storage & System Toggles -->
        <div
          v-else-if="currentStep === 7"
          class="space-y-6"
        >
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-cloud-line text-indigo-500" />
                <span>7. Cloud Storage Drivers & Master System Toggles</span>
              </h4>
              <p class="text-xs text-slate-500 mt-0.5">
                Connect Amazon S3, Cloudflare R2 or MinIO buckets, and configure public policy flags.
              </p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold text-xs">Step 7 of 7</span>
          </div>

          <!-- Feature Toggles -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <FormToggle
              v-model="settings.enable_cookies"
              label="Cookie Consent Banner"
              description="Show GDPR/Privacy cookie notice to site visitors"
              active-text="Banner Active"
              inactive-text="Hidden"
            />
            <FormToggle
              v-model="settings.is_admission_form_active"
              label="Public Inquiries Form"
              description="Allow visitors to submit direct registration inquiries"
              active-text="Inquiries Open"
              inactive-text="Closed"
            />
            <FormToggle
              v-model="settings.display_storage"
              label="Cloud Object Storage"
              description="Upload files to AWS S3, R2 or MinIO instead of local disk"
              active-text="Cloud S3 Active"
              inactive-text="Local Disk"
            />
          </div>

          <!-- Storage Details -->
          <div
            v-if="settings.display_storage"
            class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-4"
          >
            <h5 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">
              Cloud Storage Bucket Configuration
            </h5>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Storage Provider Type</label>
                <select
                  v-model="settings.storage_type"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium"
                >
                  <option value="s3">
                    Amazon AWS S3
                  </option>
                  <option value="r2">
                    Cloudflare R2
                  </option>
                  <option value="minio">
                    MinIO Object Storage
                  </option>
                  <option value="local">
                    Local Storage
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Bucket Name</label>
                <input
                  v-model="settings.storage_bucket_name"
                  type="text"
                  placeholder="autorent-production-media"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium"
                >
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Region</label>
                <input
                  v-model="settings.storage_region"
                  type="text"
                  placeholder="ap-south-1 / us-east-1"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium"
                >
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Custom Storage Endpoint (Optional)</label>
                <input
                  v-model="settings.storage_endpoint"
                  type="text"
                  placeholder="https://<account_id>.r2.cloudflarestorage.com"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium"
                >
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Public CDN / Storage URL</label>
                <input
                  v-model="settings.storage_url"
                  type="text"
                  placeholder="https://cdn.carrental.local"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-medium"
                >
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Access Key ID</label>
                <input
                  v-model="settings.storage_access_key"
                  type="text"
                  placeholder="AKIAIOSFODNN7EXAMPLE"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                >
              </div>

              <div>
                <label class="block text-xs font-bold mb-1.5 text-slate-700 dark:text-slate-300">Secret Access Key</label>
                <input
                  v-model="settings.storage_secret_key"
                  type="password"
                  placeholder="Leave empty to retain saved credentials"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Stepper Navigation Footer Actions -->
        <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
          <div>
            <button
              v-if="currentStep > 1"
              type="button"
              class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center gap-2 cursor-pointer transition-all"
              @click="prevStep"
            >
              <i class="ri-arrow-left-line text-sm" />
              <span>Previous Step</span>
            </button>
          </div>

          <div class="flex items-center gap-3 w-full sm:w-auto">
            <button
              v-if="currentStep < steps.length"
              type="button"
              class="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs flex items-center justify-center gap-2 cursor-pointer transition-all shadow-xs"
              @click="nextStep"
            >
              <span>Next: {{ steps[currentStep]?.label }}</span>
              <i class="ri-arrow-right-line text-sm" />
            </button>

            <button
              type="submit"
              :disabled="submitting || !isCurrentStepValid"
              class="flex-1 sm:flex-initial px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2 transition-all"
            >
              <i
                v-if="submitting"
                class="ri-loader-4-line animate-spin text-sm"
              />
              <i
                v-else
                class="ri-save-line text-sm"
              />
              <span>{{ submitting ? 'Saving All Settings...' : 'Save All Settings' }}</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
