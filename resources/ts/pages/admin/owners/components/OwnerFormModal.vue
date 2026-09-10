<script setup lang="ts">
import { ref, watch } from 'vue'
import type { OwnerItem } from '../types'

const props = defineProps<{
  show: boolean
  isEditing: boolean
  owner?: OwnerItem | null
  submitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', formData: FormData): void
}>()

const form = ref<{
  full_name: string
  first_name: string
  middle_name: string
  last_name: string
  username: string
  email: string
  contact_number: string
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
  is_active: boolean
  is_mfa_enabled: boolean
  password: string
}>({
  full_name: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  username: '',
  email: '',
  contact_number: '',
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
  if (imagePath?.original) return imagePath.original
  if (img) {
    return img.startsWith('http') ? img : `/${img.replace(/^\/+/, '')}`
  }
  return null
}

watch(
  () => props.owner,
  (newOwner: OwnerItem | null | undefined) => {
    if (newOwner && props.isEditing) {
      form.value = {
        full_name: newOwner.full_name || newOwner.name || '',
        first_name: newOwner.first_name || '',
        middle_name: newOwner.middle_name || '',
        last_name: newOwner.last_name || '',
        username: newOwner.username || '',
        email: newOwner.email || '',
        contact_number: newOwner.contact_number || '',
        phone: newOwner.phone || '',
        mobile: newOwner.mobile || '',
        gender: newOwner.gender || 'male',
        date_of_birth: newOwner.date_of_birth ? String(newOwner.date_of_birth).split('T')[0] : '',
        marital_status: newOwner.marital_status || 'single',
        nationality: newOwner.nationality || 'Nepalese',
        citizenship_number: newOwner.citizenship_number || '',
        passport_number: newOwner.passport_number || '',
        position: newOwner.position || '',
        designation: newOwner.designation || '',
        address: newOwner.address || '',
        emergency_contact: newOwner.emergency_contact || '',
        contact_person_name: newOwner.contact_person_name || '',
        contact_relationship: newOwner.contact_relationship || '',
        is_active: newOwner.is_active !== false,
        is_mfa_enabled: !!newOwner.is_mfa_enabled,
        password: '',
      }
      photoPreview.value = resolveImageUrl(newOwner.image, newOwner.image_path)
    } else {
      form.value = {
        full_name: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        username: '',
        email: '',
        contact_number: '',
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
      }
      photoPreview.value = null
    }
    photoFile.value = null
    activeTab.value = 'personal'
  },
  { immediate: true },
)

const handlePhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    photoFile.value = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

const handleSubmit = () => {
  const data = new FormData()
  data.append('full_name', form.value.full_name || `${form.value.first_name} ${form.value.last_name}`.trim())
  data.append('first_name', form.value.first_name)
  data.append('middle_name', form.value.middle_name)
  data.append('last_name', form.value.last_name)
  data.append('username', form.value.username)
  data.append('email', form.value.email)
  data.append('contact_number', form.value.contact_number)
  data.append('phone', form.value.phone)
  data.append('mobile', form.value.mobile)
  data.append('gender', form.value.gender)
  data.append('date_of_birth', form.value.date_of_birth)
  data.append('marital_status', form.value.marital_status)
  data.append('nationality', form.value.nationality)
  data.append('citizenship_number', form.value.citizenship_number)
  data.append('passport_number', form.value.passport_number)
  data.append('position', form.value.position)
  data.append('designation', form.value.designation)
  data.append('address', form.value.address)
  data.append('emergency_contact', form.value.emergency_contact)
  data.append('contact_person_name', form.value.contact_person_name)
  data.append('contact_relationship', form.value.contact_relationship)
  data.append('is_active', form.value.is_active ? '1' : '0')
  data.append('is_mfa_enabled', form.value.is_mfa_enabled ? '1' : '0')

  if (form.value.password) {
    data.append('password', form.value.password)
  }

  if (photoFile.value) {
    data.append('image', photoFile.value)
  }

  emit('save', data)
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4 my-8 max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-building-line text-indigo-600" />
          <span>{{ isEditing ? 'Edit Fleet Owner Profile' : 'Register New Fleet Owner' }}</span>
        </h3>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <!-- Error Alert -->
      <div
        v-if="errorMessage"
        class="p-3 rounded-2xl bg-rose-50 text-rose-800 text-xs font-semibold"
      >
        {{ errorMessage }}
      </div>

      <!-- Modal Tabs -->
      <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2 text-xs">
        <button
          type="button"
          :class="activeTab === 'personal' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
          class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer font-medium"
          @click="activeTab = 'personal'"
        >
          Identity & Personal
        </button>
        <button
          type="button"
          :class="activeTab === 'contact' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
          class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer font-medium"
          @click="activeTab = 'contact'"
        >
          Contact & Location
        </button>
        <button
          type="button"
          :class="activeTab === 'emergency' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
          class="px-3.5 py-1.5 rounded-xl transition-all cursor-pointer font-medium"
          @click="activeTab = 'emergency'"
        >
          Emergency & Security
        </button>
      </div>

      <form
        class="space-y-4 text-xs"
        @submit.prevent="handleSubmit"
      >
        <!-- Tab 1: Identity & Personal -->
        <div v-show="activeTab === 'personal'" class="space-y-4">
          <!-- Avatar Upload -->
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Profile Image</label>
            <div class="flex items-center gap-4">
              <div
                v-if="photoPreview"
                class="w-14 h-14 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 dark:border-slate-700 shrink-0"
              >
                <img :src="photoPreview" class="w-full h-full object-cover">
              </div>
              <div
                v-else
                class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 shrink-0"
              >
                <i class="ri-user-line text-xl" />
              </div>
              <input
                type="file"
                accept="image/*"
                class="text-xs text-slate-500 file:me-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                @change="handlePhotoChange"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">First Name</label>
              <input
                v-model="form.first_name"
                type="text"
                placeholder="e.g. John"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Middle Name</label>
              <input
                v-model="form.middle_name"
                type="text"
                placeholder="Optional"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Last Name</label>
              <input
                v-model="form.last_name"
                type="text"
                placeholder="e.g. Doe"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Display / Business Name *</label>
              <input
                v-model="form.full_name"
                type="text"
                required
                placeholder="e.g. Premier Auto Holdings"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-medium"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Username</label>
              <input
                v-model="form.username"
                type="text"
                placeholder="e.g. premier_fleet"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Gender</label>
              <select
                v-model="form.gender"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Date of Birth</label>
              <input
                v-model="form.date_of_birth"
                type="date"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Marital Status</label>
              <select
                v-model="form.marital_status"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="divorced">Divorced</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Nationality</label>
              <input
                v-model="form.nationality"
                type="text"
                placeholder="e.g. Nepalese / US"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Citizenship Number</label>
              <input
                v-model="form.citizenship_number"
                type="text"
                placeholder="27-01-76-12345"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Passport Number</label>
              <input
                v-model="form.passport_number"
                type="text"
                placeholder="N1234567"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono"
              >
            </div>
          </div>
        </div>

        <!-- Tab 2: Contact & Location -->
        <div v-show="activeTab === 'contact'" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Email Address *</label>
              <input
                v-model="form.email"
                type="email"
                required
                :disabled="isEditing"
                placeholder="owner@domain.com"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 disabled:opacity-60"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Contact Number *</label>
              <input
                v-model="form.contact_number"
                type="text"
                placeholder="+1 (555) 019-2834"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Telephone / Secondary</label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="+1 (555) 111-2233"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Mobile Number</label>
              <input
                v-model="form.mobile"
                type="text"
                placeholder="+1 (555) 999-8877"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Position</label>
              <input
                v-model="form.position"
                type="text"
                placeholder="e.g. Managing Director"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Designation</label>
              <input
                v-model="form.designation"
                type="text"
                placeholder="e.g. Fleet Partner"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
          </div>

          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Office / Physical Address</label>
            <input
              v-model="form.address"
              type="text"
              placeholder="e.g. 450 North Canon Dr, Beverly Hills, CA"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
            >
          </div>
        </div>

        <!-- Tab 3: Emergency & Security -->
        <div v-show="activeTab === 'emergency'" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Contact Person Name</label>
              <input
                v-model="form.contact_person_name"
                type="text"
                placeholder="e.g. Sarah Hayes"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Relationship</label>
              <input
                v-model="form.contact_relationship"
                type="text"
                placeholder="e.g. Spouse / Manager"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Emergency Phone</label>
              <input
                v-model="form.emergency_contact"
                type="text"
                placeholder="+1 (555) 999-0000"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">
                {{ isEditing ? 'Change Password (leave blank to keep current)' : 'Account Password' }}
              </label>
              <input
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>

            <div class="space-y-2 pt-4">
              <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700 dark:text-slate-300">
                <input v-model="form.is_active" type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500">
                <span>Account Active & Enabled</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700 dark:text-slate-300">
                <input v-model="form.is_mfa_enabled" type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500">
                <span>Require 2FA / MFA Authentication</span>
              </label>
            </div>
          </div>

          <div
            v-if="!isEditing"
            class="p-3 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/60 text-indigo-900 dark:text-indigo-200 text-[11px] leading-relaxed"
          >
            <i class="ri-shield-check-line font-bold mr-1" />
            An automated invitation email containing a secure password setup link will also be dispatched upon registration.
          </div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-xs cursor-pointer"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer transition-colors"
          >
            {{ submitting ? 'Saving...' : (isEditing ? 'Save Changes' : 'Register Owner') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
