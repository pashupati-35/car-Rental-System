<script setup lang="ts">
import { ref, watch } from 'vue'
import { resolveMediaUrl } from '@/utils/helpers'
import type { CustomerItem } from '../types'
import MessageBox from '@/components/MessageBox.vue'
import RichTextEditor from '@/components/RichTextEditor.vue'

const props = defineProps<{
  show: boolean
  isEditing: boolean
  customer?: CustomerItem | null
  submitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', formData: FormData): void
}>()

const form = ref<{
  name: string
  first_name: string
  middle_name: string
  last_name: string
  username: string
  email: string
  phone_number: string
  phone: string
  mobile: string
  gender: string
  date_of_birth: string
  marital_status: string
  nationality: string
  citizenship_number: string
  passport_number: string
  position: string
  designation: string
  address: string
  emergency_contact: string
  contact_person_name: string
  contact_relationship: string
  notes: string
  is_active: boolean
  is_mfa_enabled: boolean
  password: string
}>({
  name: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  username: '',
  email: '',
  phone_number: '',
  phone: '',
  mobile: '',
  gender: 'male',
  date_of_birth: '',
  marital_status: 'single',
  nationality: 'Nepalese',
  citizenship_number: '',
  passport_number: '',
  position: '',
  designation: '',
  address: '',
  emergency_contact: '',
  contact_person_name: '',
  contact_relationship: '',
  is_active: true,
  is_mfa_enabled: false,
  password: '',
})

const photoFile = ref<File | null>(null)
const photoPreview = ref<string | null>(null)
const activeTab = ref<'personal' | 'contact' | 'emergency'>('personal')

const resolveImageUrl = (img?: string | null, imagePath?: any) => {
  return resolveMediaUrl(img, imagePath, 'customer') || null
}

watch(
  () => props.customer,
  (newCustomer: CustomerItem | null | undefined) => {
    if (newCustomer && props.isEditing) {
      form.value = {
        name: newCustomer.name || newCustomer.full_name || '',
        first_name: newCustomer.first_name || '',
        middle_name: newCustomer.middle_name || '',
        last_name: newCustomer.last_name || '',
        username: newCustomer.username || '',
        email: newCustomer.email || '',
        phone_number: newCustomer.phone_number || newCustomer.phone || '',
        phone: newCustomer.phone || '',
        mobile: newCustomer.mobile || '',
        gender: newCustomer.gender || 'male',
        date_of_birth: newCustomer.date_of_birth ? String(newCustomer.date_of_birth).split('T')[0] : '',
        marital_status: newCustomer.marital_status || 'single',
        nationality: newCustomer.nationality || 'Nepalese',
        citizenship_number: newCustomer.citizenship_number || '',
        passport_number: newCustomer.passport_number || '',
        position: newCustomer.position || '',
        designation: newCustomer.designation || '',
        address: newCustomer.address || '',
        emergency_contact: newCustomer.emergency_contact || '',
        contact_person_name: newCustomer.contact_person_name || '',
        contact_relationship: newCustomer.contact_relationship || '',
        notes: (newCustomer as any).notes || (newCustomer as any).description || '',
        is_active: newCustomer.is_active !== false,
        is_mfa_enabled: !!newCustomer.is_mfa_enabled,
        password: '',
      }
      photoPreview.value = resolveImageUrl(newCustomer.image, newCustomer.image_path)
    } else {
      form.value = {
        name: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        username: '',
        email: '',
        phone_number: '',
        phone: '',
        mobile: '',
        gender: 'male',
        date_of_birth: '',
        marital_status: 'single',
        nationality: 'Nepalese',
        citizenship_number: '',
        passport_number: '',
        position: '',
        designation: '',
        address: '',
        emergency_contact: '',
        contact_person_name: '',
        contact_relationship: '',
        notes: '',
        is_active: true,
        is_mfa_enabled: false,
        password: '',
      }
      photoPreview.value = null
    }
    photoFile.value = null
    activeTab.value = 'personal'
  },
  { immediate: true }
)

const handleImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    photoFile.value = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

const removePhoto = () => {
  photoFile.value = null
  photoPreview.value = null
}

const onNameInput = () => {
  if (!form.value.first_name && !form.value.last_name && form.value.name) {
    const parts = form.value.name.trim().split(' ')
    if (parts.length === 1) {
      form.value.first_name = parts[0]
    } else if (parts.length === 2) {
      form.value.first_name = parts[0]
      form.value.last_name = parts[1]
    } else if (parts.length > 2) {
      form.value.first_name = parts[0]
      form.value.middle_name = parts.slice(1, -1).join(' ')
      form.value.last_name = parts[parts.length - 1]
    }
  }
}

const onNamePartInput = () => {
  const parts = [form.value.first_name, form.value.middle_name, form.value.last_name].filter(Boolean)
  if (parts.length > 0) {
    form.value.name = parts.join(' ')
  }
}

const handleSubmit = () => {
  const data = new FormData()

  // Auto-compose name if missing
  if (!form.value.name) {
    const parts = [form.value.first_name, form.value.middle_name, form.value.last_name].filter(Boolean)
    form.value.name = parts.join(' ')
  }

  // Populate formData
  Object.entries(form.value).forEach(([key, val]) => {
    if (key === 'is_active') {
      data.append(key, val ? '1' : '0')
    } else if (key === 'is_mfa_enabled') {
      data.append(key, val ? '1' : '0')
    } else if (key === 'password') {
      if (val) data.append('password', String(val))
    } else if (val !== null && val !== undefined) {
      data.append(key, String(val))
    }
  })

  if (photoFile.value) {
    data.append('image', photoFile.value)
  }

  emit('save', data)
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200"
  >
    <div
      class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-4xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]"
    >
      <!-- Modal Header -->
      <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40 shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
            <i :class="isEditing ? 'ri-edit-line' : 'ri-user-add-line'" />
          </div>
          <div>
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
              {{ isEditing ? 'Edit Customer Profile' : 'Register New Customer' }}
            </h3>
            <p class="text-xs text-slate-500">
              Configure personal identity, contact, emergency details, and portal credentials.
            </p>
          </div>
        </div>

        <button
          type="button"
          class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center transition-colors cursor-pointer"
          @click="emit('close')"
        >
          <i class="ri-close-line text-lg" />
        </button>
      </div>

      <!-- Tab Navigation -->
      <div class="flex border-b border-slate-100 dark:border-slate-800 px-6 bg-slate-50/30 dark:bg-slate-800/20 shrink-0">
        <button
          type="button"
          :class="[
            'py-3 px-4 font-bold text-xs border-b-2 flex items-center gap-2 cursor-pointer transition-colors',
            activeTab === 'personal'
              ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
              : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
          ]"
          @click="activeTab = 'personal'"
        >
          <i class="ri-user-3-line text-sm" />
          <span>1. Personal & Identity</span>
        </button>

        <button
          type="button"
          :class="[
            'py-3 px-4 font-bold text-xs border-b-2 flex items-center gap-2 cursor-pointer transition-colors',
            activeTab === 'contact'
              ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
              : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
          ]"
          @click="activeTab = 'contact'"
        >
          <i class="ri-phone-line text-sm" />
          <span>2. Contact & Address</span>
        </button>

        <button
          type="button"
          :class="[
            'py-3 px-4 font-bold text-xs border-b-2 flex items-center gap-2 cursor-pointer transition-colors',
            activeTab === 'emergency'
              ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
              : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'
          ]"
          @click="activeTab = 'emergency'"
        >
          <i class="ri-shield-keyhole-line text-sm" />
          <span>3. Emergency & Security</span>
        </button>
      </div>

      <!-- Error Message -->
      <div class="px-6 pt-3">
        <MessageBox
          :message="errorMessage"
          type="error"
        />
      </div>

      <!-- Form Content (Scrollable) -->
      <form
        class="flex-1 overflow-y-auto p-6 space-y-6 text-xs text-slate-700 dark:text-slate-300"
        @submit.prevent="handleSubmit"
      >
        <!-- TAB 1: Personal & Identity -->
        <div
          v-show="activeTab === 'personal'"
          class="space-y-6 animate-in fade-in duration-150"
        >
          <!-- Photo Upload Section -->
          <div class="flex flex-col sm:flex-row items-center gap-5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800">
            <div class="relative w-24 h-24 rounded-2xl bg-white dark:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center overflow-hidden shrink-0 group">
              <img
                v-if="photoPreview"
                :src="photoPreview"
                alt="Profile Preview"
                class="w-full h-full object-cover"
              >
              <div
                v-else
                class="text-center p-2 text-slate-400"
              >
                <i class="ri-user-image-line text-2xl block mb-1" />
                <span class="text-[10px] block font-bold">No Photo</span>
              </div>
            </div>

            <div class="flex-1 space-y-2 text-center sm:text-left">
              <label class="font-bold text-slate-800 dark:text-slate-200 text-xs block">
                Profile Photo (Image / Document)
              </label>
              <p class="text-[11px] text-slate-500">
                Upload JPG, PNG, WEBP, or HEIC photo up to 5MB. Photo will be saved to customer directory.
              </p>
              <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                <label class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5 transition-colors">
                  <i class="ri-upload-2-line text-sm" />
                  <span>Choose Photo</span>
                  <input
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleImageChange"
                  >
                </label>
                <button
                  v-if="photoPreview"
                  type="button"
                  class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer inline-flex items-center gap-1 transition-colors"
                  @click="removePhoto"
                >
                  <i class="ri-delete-bin-line text-xs" />
                  <span>Remove</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Names Grid -->
          <div class="space-y-4">
            <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2">
              <i class="ri-identification-line text-indigo-500" />
              Name & Identity Credentials
            </h4>

            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                Full Display Name <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="e.g. Johnathan Doe"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                @input="onNameInput"
              >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">First Name</label>
                <input
                  v-model="form.first_name"
                  type="text"
                  placeholder="First name"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                  @input="onNamePartInput"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Middle Name</label>
                <input
                  v-model="form.middle_name"
                  type="text"
                  placeholder="Middle name"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                  @input="onNamePartInput"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Last Name</label>
                <input
                  v-model="form.last_name"
                  type="text"
                  placeholder="Last name"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                  @input="onNamePartInput"
                >
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Gender</label>
                <select
                  v-model="form.gender"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Date of Birth</label>
                <input
                  v-model="form.date_of_birth"
                  type="date"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Marital Status</label>
                <select
                  v-model="form.marital_status"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
                  <option value="single">Single</option>
                  <option value="married">Married</option>
                  <option value="divorced">Divorced</option>
                  <option value="widowed">Widowed</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nationality</label>
                <input
                  v-model="form.nationality"
                  type="text"
                  placeholder="e.g. Nepalese"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Citizenship Number</label>
                <input
                  v-model="form.citizenship_number"
                  type="text"
                  placeholder="Citizenship ID"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Passport Number</label>
                <input
                  v-model="form.passport_number"
                  type="text"
                  placeholder="Passport ID"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- TAB 2: Contact & Address -->
        <div
          v-show="activeTab === 'contact'"
          class="space-y-6 animate-in fade-in duration-150"
        >
          <div class="space-y-4">
            <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2">
              <i class="ri-phone-line text-indigo-500" />
              Direct Contact Channels & Profession
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Email Address <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="customer@example.com"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Primary Phone Number <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="form.phone_number"
                  type="text"
                  required
                  placeholder="+977 9801234567"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Mobile Number</label>
                <input
                  v-model="form.mobile"
                  type="text"
                  placeholder="+977 9841000000"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Secondary Phone</label>
                <input
                  v-model="form.phone"
                  type="text"
                  placeholder="Alternate phone"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Position</label>
                <input
                  v-model="form.position"
                  type="text"
                  placeholder="e.g. Senior Consultant"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Designation</label>
                <input
                  v-model="form.designation"
                  type="text"
                  placeholder="e.g. Enterprise Client"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
            </div>

            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Residential / Billing Address</label>
              <textarea
                v-model="form.address"
                rows="2"
                placeholder="Street address, City, Province, Postal Code"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Customer Preferences, Notes & Requirements (Rich Text)</label>
              <RichTextEditor
                v-model="form.notes"
                placeholder="Customer VIP notes, vehicle preferences, special handling requirements, past history..."
                min-height="130px"
              />
            </div>
          </div>
        </div>

        <!-- TAB 3: Emergency & Security -->
        <div
          v-show="activeTab === 'emergency'"
          class="space-y-6 animate-in fade-in duration-150"
        >
          <!-- Emergency Contacts -->
          <div class="space-y-4">
            <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2">
              <i class="ri-alarm-warning-line text-rose-500" />
              Emergency Contact & Relations
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Emergency Phone</label>
                <input
                  v-model="form.emergency_contact"
                  type="text"
                  placeholder="+977 9800000000"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Contact Person Name</label>
                <input
                  v-model="form.contact_person_name"
                  type="text"
                  placeholder="Emergency contact name"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Relationship</label>
                <input
                  v-model="form.contact_relationship"
                  type="text"
                  placeholder="e.g. Spouse / Sibling / Manager"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
            </div>
          </div>

          <!-- Credentials & Security -->
          <div class="space-y-4">
            <h4 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2">
              <i class="ri-shield-keyhole-line text-indigo-500" />
              Credentials & System Status
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Username (Optional)</label>
                <input
                  v-model="form.username"
                  type="text"
                  placeholder="e.g. johndoe"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                  {{ isEditing ? 'Set New Password (Leave blank to keep current)' : 'Password (Optional - generates setup link if empty)' }}
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  placeholder="••••••••"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                >
              </div>
            </div>

            <div class="flex flex-wrap items-center gap-6 pt-2">
              <label class="flex items-center gap-2 cursor-pointer select-none">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-700"
                >
                <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">Active Account Status</span>
              </label>

              <label class="flex items-center gap-2 cursor-pointer select-none">
                <input
                  v-model="form.is_mfa_enabled"
                  type="checkbox"
                  class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-700"
                >
                <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">Enable Multi-Factor Authentication (MFA)</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Hidden submit trigger for keyboard Enter -->
        <button
          type="submit"
          class="hidden"
        />
      </form>

      <!-- Modal Footer -->
      <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
          <button
            v-if="activeTab !== 'personal'"
            type="button"
            class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-xs cursor-pointer"
            @click="activeTab = activeTab === 'emergency' ? 'contact' : 'personal'"
          >
            Back
          </button>
          <button
            v-if="activeTab !== 'emergency'"
            type="button"
            class="px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-xs cursor-pointer"
            @click="activeTab = activeTab === 'personal' ? 'contact' : 'emergency'"
          >
            Next Section
          </button>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="button"
            :disabled="submitting"
            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2 cursor-pointer"
            @click="handleSubmit"
          >
            <i
              v-if="submitting"
              class="ri-loader-4-line animate-spin text-sm"
            />
            <i
              v-else
              :class="isEditing ? 'ri-save-line' : 'ri-user-add-line'"
              class="text-sm"
            />
            <span>{{ submitting ? 'Saving...' : (isEditing ? 'Save Changes' : 'Register Customer') }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
