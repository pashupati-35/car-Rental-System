<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import MessageBox from '@/components/MessageBox.vue'
import AppDatePicker from '@/components/AppDatePicker.vue'
import { resolveMediaUrl } from '@/utils/helpers'
import axios from 'axios'

const props = defineProps<{
  user?: any
}>()

const page = usePage()
const auth = computed(() => (page.props.auth as any) || {})
const customer = ref<any>(props.user || auth.value?.customer || auth.value?.user || {})

const fileInputRef = ref<HTMLInputElement | null>(null)
const selectedImageFile = ref<File | null>(null)
const imagePreviewUrl = ref<string>(resolveMediaUrl(customer.value?.image, customer.value?.image_path, 'customer') || '')
const isImageRemoved = ref(false)

const profileForm = ref({
  first_name: customer.value?.first_name || '',
  middle_name: customer.value?.middle_name || '',
  last_name: customer.value?.last_name || '',
  name: customer.value?.name || customer.value?.full_name || '',
  username: customer.value?.username || '',
  email: customer.value?.email || '',
  phone_number: customer.value?.phone_number || customer.value?.phone || '',
  mobile: customer.value?.mobile || '',
  gender: customer.value?.gender || '',
  date_of_birth: customer.value?.date_of_birth ? customer.value.date_of_birth.split('T')[0] : '',
  marital_status: customer.value?.marital_status || '',
  nationality: customer.value?.nationality || 'Nepalese',
  address: customer.value?.address || '',
  citizenship_number: customer.value?.citizenship_number || '',
  passport_number: customer.value?.passport_number || '',
  designation: customer.value?.designation || customer.value?.position || '',
  contact_person_name: customer.value?.contact_person_name || '',
  emergency_contact: customer.value?.emergency_contact || '',
  contact_relationship: customer.value?.contact_relationship || '',
})

const profileLoading = ref(false)
const profileMsg = ref('')
const profileError = ref('')

const triggerImageUpload = () => {
  fileInputRef.value?.click()
}

const onImageSelected = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]

    selectedImageFile.value = file
    imagePreviewUrl.value = URL.createObjectURL(file)
    isImageRemoved.value = false
  }
}

const removeImage = () => {
  selectedImageFile.value = null
  imagePreviewUrl.value = ''
  isImageRemoved.value = true
  if (fileInputRef.value) {
    fileInputRef.value.value = ''
  }
}

const resetForm = () => {
  profileForm.value = {
    first_name: customer.value?.first_name || '',
    middle_name: customer.value?.middle_name || '',
    last_name: customer.value?.last_name || '',
    name: customer.value?.name || customer.value?.full_name || '',
    username: customer.value?.username || '',
    email: customer.value?.email || '',
    phone_number: customer.value?.phone_number || customer.value?.phone || '',
    mobile: customer.value?.mobile || '',
    gender: customer.value?.gender || '',
    date_of_birth: customer.value?.date_of_birth ? customer.value.date_of_birth.split('T')[0] : '',
    marital_status: customer.value?.marital_status || '',
    nationality: customer.value?.nationality || 'Nepalese',
    address: customer.value?.address || '',
    citizenship_number: customer.value?.citizenship_number || '',
    passport_number: customer.value?.passport_number || '',
    designation: customer.value?.designation || customer.value?.position || '',
    contact_person_name: customer.value?.contact_person_name || '',
    emergency_contact: customer.value?.emergency_contact || '',
    contact_relationship: customer.value?.contact_relationship || '',
  }
  selectedImageFile.value = null
  imagePreviewUrl.value = resolveMediaUrl(customer.value?.image, customer.value?.image_path, 'customer') || ''
  isImageRemoved.value = false
}

const updateProfile = async () => {
  profileLoading.value = true
  profileMsg.value = ''
  profileError.value = ''

  try {
    const formData = new FormData()

    Object.entries(profileForm.value).forEach(([key, val]) => {
      if (val !== null && val !== undefined) {
        formData.append(key, String(val))
      }
    })

    if (selectedImageFile.value) {
      formData.append('image', selectedImageFile.value)
    }

    if (isImageRemoved.value) {
      formData.append('remove_image', '1')
    }

    const res = await axios.post('/customer/profile', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Your customer profile has been updated successfully!'
      if (res.data?.user) {
        customer.value = res.data.user
        imagePreviewUrl.value = resolveMediaUrl(customer.value.image, customer.value.image_path, 'customer') || ''
        selectedImageFile.value = null
        isImageRemoved.value = false
      }
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || err.response?.data?.error || 'Failed to update profile. Please verify your fields.'
  } finally {
    profileLoading.value = false
  }
}
</script>

<template>
  <CustomerLayout>
    <Head title="Customer Profile - Traveler Hub" />

    <div class="space-y-8 max-w-6xl mx-auto pb-12">
      <!-- Top Header & Tabs -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
          <div class="flex items-center gap-2.5">
            <span class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-lg shadow-xs">
              <i class="ri-user-settings-line" />
            </span>
            <div>
              <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Customer Profile Settings
              </h1>
              <p class="text-xs text-slate-500 mt-0.5">
                Manage your personal identity, verified travel credentials, contact details, and emergency info.
              </p>
            </div>
          </div>
        </div>

        <!-- Tab Navigation -->
        <div class="flex items-center gap-1.5 p-1.5 bg-slate-100 dark:bg-slate-800 rounded-2xl shrink-0 self-start sm:self-auto shadow-xs">
          <Link
            href="/customer/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-300 shadow-xs flex items-center gap-1.5"
          >
            <i class="ri-user-3-line" />
            <span>Profile Details</span>
          </Link>
          <Link
            href="/customer/security"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1.5"
          >
            <i class="ri-shield-keyhole-line" />
            <span>Security & MFA</span>
          </Link>
        </div>
      </div>

      <!-- Feedback Alerts -->
      <MessageBox
        v-model="profileMsg"
        type="success"
      />
      <MessageBox
        v-model="profileError"
        type="error"
      />

      <form
        class="space-y-8"
        @submit.prevent="updateProfile"
      >
        <!-- 1. Profile Header & Avatar Card -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <!-- Avatar Image / Preview -->
            <div class="relative group shrink-0">
              <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl overflow-hidden bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-3xl flex items-center justify-center shadow-lg shadow-blue-500/20 border-4 border-white dark:border-slate-800 ring-2 ring-blue-500/30">
                <img
                  v-if="imagePreviewUrl"
                  :src="imagePreviewUrl"
                  class="w-full h-full object-cover"
                  alt="Customer Avatar"
                >
                <span v-else>{{ (profileForm.first_name || profileForm.name || 'C')[0].toUpperCase() }}</span>
              </div>

              <!-- Hidden File Input -->
              <input
                ref="fileInputRef"
                type="file"
                accept="image/png, image/jpeg, image/webp, image/gif"
                class="hidden"
                @change="onImageSelected"
              >

              <!-- Upload Floating Action Button -->
              <button
                type="button"
                class="absolute -bottom-2 -end-2 w-9 h-9 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-md transition-transform hover:scale-105 cursor-pointer"
                title="Change Photo"
                @click="triggerImageUpload"
              >
                <i class="ri-camera-fill text-sm" />
              </button>
            </div>

            <!-- Profile Overview Info -->
            <div class="flex-1 text-center sm:text-start space-y-2">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <h3 class="text-xl font-black text-slate-900 dark:text-white">
                    {{ profileForm.name || `${profileForm.first_name} ${profileForm.last_name}`.trim() || 'Customer Account' }}
                  </h3>
                  <p class="text-xs text-slate-500 font-mono mt-0.5">
                    {{ profileForm.email }} &bull; {{ profileForm.phone_number || 'No phone set' }}
                  </p>
                </div>
                <div class="flex flex-wrap gap-2 justify-center sm:justify-start">
                  <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50 flex items-center gap-1">
                    <i class="ri-verified-badge-fill text-xs" /> Verified Customer
                  </span>
                  <span
                    v-if="customer?.unique_identifier"
                    class="px-3 py-1 rounded-full text-[11px] font-black font-mono bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                  >
                    {{ customer.unique_identifier }}
                  </span>
                </div>
              </div>

              <!-- Photo Action Buttons -->
              <div class="flex flex-wrap items-center gap-2.5 pt-2 justify-center sm:justify-start">
                <button
                  type="button"
                  class="px-3.5 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer"
                  @click="triggerImageUpload"
                >
                  <i class="ri-upload-2-line" />
                  <span>Upload New Photo</span>
                </button>
                <button
                  v-if="imagePreviewUrl"
                  type="button"
                  class="px-3.5 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer"
                  @click="removeImage"
                >
                  <i class="ri-delete-bin-line" />
                  <span>Remove Photo</span>
                </button>
                <span class="text-[11px] text-slate-400">
                  Allowed formats: JPG, PNG, WEBP &bull; Max: 5MB
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Personal Details Section -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <span class="w-7 h-7 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 flex items-center justify-center font-bold">
              <i class="ri-user-line text-sm" />
            </span>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              Personal Information
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">First Name *</label>
              <input
                v-model="profileForm.first_name"
                type="text"
                placeholder="e.g. Sandesh"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Middle Name (Optional)</label>
              <input
                v-model="profileForm.middle_name"
                type="text"
                placeholder="e.g. Bahadur"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Last Name *</label>
              <input
                v-model="profileForm.last_name"
                type="text"
                placeholder="e.g. Karki"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Preferred Full Name</label>
              <input
                v-model="profileForm.name"
                type="text"
                placeholder="Full display name"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Username</label>
              <input
                v-model="profileForm.username"
                type="text"
                placeholder="e.g. sandesh35"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Gender</label>
              <select
                v-model="profileForm.gender"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">
                  Select Gender
                </option>
                <option value="male">
                  Male
                </option>
                <option value="female">
                  Female
                </option>
                <option value="other">
                  Other
                </option>
                <option value="prefer_not_to_say">
                  Prefer not to say
                </option>
              </select>
            </div>

            <div>
              <AppDatePicker
                v-model="profileForm.date_of_birth"
                label="Date of Birth"
                placeholder="Select birth date..."
                max-date="today"
                :show-legend="false"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Marital Status</label>
              <select
                v-model="profileForm.marital_status"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">
                  Select Status
                </option>
                <option value="single">
                  Single
                </option>
                <option value="married">
                  Married
                </option>
                <option value="divorced">
                  Divorced
                </option>
                <option value="widowed">
                  Widowed
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nationality</label>
              <input
                v-model="profileForm.nationality"
                type="text"
                placeholder="e.g. Nepalese"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>
        </div>

        <!-- 3. Contact & Residential Location -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <span class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center font-bold">
              <i class="ri-map-pin-user-line text-sm" />
            </span>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              Contact & Address
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address *</label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Primary Phone Number *</label>
              <input
                v-model="profileForm.phone_number"
                type="text"
                placeholder="e.g. +977-9812345601"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alternative Mobile Number</label>
              <input
                v-model="profileForm.mobile"
                type="text"
                placeholder="e.g. +977-9800000000"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Residential / Delivery Address *</label>
              <input
                v-model="profileForm.address"
                type="text"
                placeholder="e.g. Baluwatar, Kathmandu, Nepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>
        </div>

        <!-- 4. Travel Verification & Government Documents -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <span class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold">
              <i class="ri-passport-line text-sm" />
            </span>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              Identification & Verification Documents
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Citizenship Number</label>
              <input
                v-model="profileForm.citizenship_number"
                type="text"
                placeholder="e.g. 27-01-78-01923"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Passport Number</label>
              <input
                v-model="profileForm.passport_number"
                type="text"
                placeholder="e.g. PA0891234"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Profession / Designation</label>
              <input
                v-model="profileForm.designation"
                type="text"
                placeholder="e.g. Software Engineer / Consultant"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>
        </div>

        <!-- 5. Emergency Contact & Next of Kin -->
        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <span class="w-7 h-7 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 flex items-center justify-center font-bold">
              <i class="ri-phone-lock-line text-sm" />
            </span>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              Emergency Contact & Next of Kin
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Person Name</label>
              <input
                v-model="profileForm.contact_person_name"
                type="text"
                placeholder="e.g. Aarav Karki"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Relationship</label>
              <select
                v-model="profileForm.contact_relationship"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">
                  Select Relationship
                </option>
                <option value="spouse">
                  Spouse
                </option>
                <option value="parent">
                  Parent
                </option>
                <option value="sibling">
                  Sibling
                </option>
                <option value="child">
                  Child
                </option>
                <option value="friend">
                  Friend
                </option>
                <option value="colleague">
                  Colleague
                </option>
                <option value="guardian">
                  Legal Guardian
                </option>
                <option value="other">
                  Other
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Emergency Phone Number</label>
              <input
                v-model="profileForm.emergency_contact"
                type="text"
                placeholder="e.g. +977-9841000000"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>
        </div>

        <!-- Bottom Action Bar (Sticky or anchored) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 rounded-3xl bg-slate-900 text-white shadow-xl">
          <div class="text-xs text-slate-400">
            Ensure all information is accurate to expedite car handover and agreement verification.
          </div>

          <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <button
              type="button"
              class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors cursor-pointer"
              @click="resetForm"
            >
              Discard Changes
            </button>
            <button
              type="submit"
              class="px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer"
              :disabled="profileLoading"
            >
              <i
                v-if="profileLoading"
                class="ri-loader-4-line animate-spin text-sm"
              />
              <span>{{ profileLoading ? 'Saving Changes...' : 'Save Profile Changes' }}</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </CustomerLayout>
</template>
