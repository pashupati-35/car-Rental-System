<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'
import { useOwnerTheme, type OwnerThemeStyle } from '@/composable/useOwnerTheme'

const props = defineProps<{
  user?: any
}>()

const page = usePage()
const authUser = computed(() => props.user || (page.props.auth as any)?.owner || (page.props.auth as any)?.user || {})
const { theme, setTheme } = useOwnerTheme()

// Initialize profile form with all database fields
const profileForm = ref({
  first_name: authUser.value?.first_name || '',
  middle_name: authUser.value?.middle_name || '',
  last_name: authUser.value?.last_name || '',
  full_name: authUser.value?.full_name || '',
  username: authUser.value?.username || '',
  email: authUser.value?.email || '',
  contact_number: authUser.value?.contact_number || '',
  mobile: authUser.value?.mobile || '',
  phone: authUser.value?.phone || '',
  address: authUser.value?.address || '',
  gender: authUser.value?.gender || 'male',
  date_of_birth: authUser.value?.date_of_birth ? authUser.value.date_of_birth.substring(0, 10) : '',
  marital_status: authUser.value?.marital_status || 'single',
  nationality: authUser.value?.nationality || 'Nepali',
  citizenship_number: authUser.value?.citizenship_number || '',
  passport_number: authUser.value?.passport_number || '',
  designation: authUser.value?.designation || '',
  position: authUser.value?.position || '',
  emergency_contact: authUser.value?.emergency_contact || '',
  contact_person_name: authUser.value?.contact_person_name || '',
  contact_relationship: authUser.value?.contact_relationship || '',
})

const currentImage = ref(authUser.value?.image ? (authUser.value.image.startsWith('http') ? authUser.value.image : '/' + authUser.value.image) : (authUser.value?.image_path || ''))
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

const handleImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const profileLoading = ref(false)
const profileMsg = ref('')
const profileError = ref('')

const updateProfile = async () => {
  profileLoading.value = true
  profileMsg.value = ''
  profileError.value = ''

  try {
    const formData = new FormData()
    Object.entries(profileForm.value).forEach(([key, value]) => {
      if (value !== null && value !== undefined) {
        formData.append(key, value as string)
      }
    })

    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    const res = await axios.post('/owner/profile?_method=PATCH', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Fleet Owner profile updated successfully.'
      if (res.data?.user?.image) {
        currentImage.value = '/' + res.data.user.image
        imagePreview.value = null
      }
      if (res.data?.user?.full_name) {
        profileForm.value.full_name = res.data.user.full_name
      }
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || 'Failed to update profile details.'
  } finally {
    profileLoading.value = false
  }
}

// Password Form
const passwordForm = ref({
  password: '',
  password_confirmation: '',
})
const passwordLoading = ref(false)
const passwordMsg = ref('')
const passwordError = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const updatePassword = async () => {
  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    passwordError.value = 'Passwords do not match.'
    return
  }
  passwordLoading.value = true
  passwordMsg.value = ''
  passwordError.value = ''
  try {
    const res = await axios.patch('/owner/password', passwordForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      passwordMsg.value = 'Account password changed successfully.'
      passwordForm.value.password = ''
      passwordForm.value.password_confirmation = ''
    }
  } catch (err: any) {
    passwordError.value = err.response?.data?.message || 'Failed to update password.'
  } finally {
    passwordLoading.value = false
  }
}

const themeOptions: { value: OwnerThemeStyle; label: string; desc: string; icon: string }[] = [
  { value: 'dark', label: 'Dark Slate', desc: 'Sleek dark interface with emerald accents', icon: 'ri-moon-clear-line' },
  { value: 'midnight', label: 'Midnight Obsidian', desc: 'Ultra-deep dark palette with high contrast', icon: 'ri-sparkling-2-line' },
  { value: 'light', label: 'Clean Light', desc: 'Bright, crisp white theme for daytime work', icon: 'ri-sun-line' },
  { value: 'system', label: 'System Default', desc: 'Automatically match your OS color scheme', icon: 'ri-computer-line' },
]
</script>

<template>
  <OwnerLayout>
    <Head title="Owner Profile Settings" />

    <div class="space-y-6 max-w-5xl mx-auto pb-12">
      <!-- Top Header & Tabs -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-base">
              <i class="ri-user-settings-line" />
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Owner Profile Settings
            </h1>
          </div>
          <p class="text-xs text-slate-500 mt-1">
            Manage your fleet owner identity, theme styling, password, and business contact details.
          </p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl shrink-0 self-start sm:self-auto">
          <Link
            href="/owner/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-300 shadow-xs"
          >
            Profile & Theme
          </Link>
          <Link
            href="/owner/security"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
          >
            Security & MFA
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

      <!-- Profile Form Wrap -->
      <form
        class="space-y-6"
        @submit.prevent="updateProfile"
      >
        <!-- Profile Identity & Avatar Header Card -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <!-- Avatar with Upload Overlay -->
            <div class="relative group shrink-0">
              <div class="w-24 h-24 rounded-3xl overflow-hidden bg-gradient-to-tr from-emerald-600 to-teal-600 text-white font-black text-3xl flex items-center justify-center shadow-lg shadow-emerald-600/20 border-2 border-emerald-500/30">
                <img
                  v-if="imagePreview || currentImage"
                  :src="imagePreview || currentImage"
                  alt="Owner Avatar"
                  class="w-full h-full object-cover"
                >
                <span v-else>
                  {{ (profileForm.full_name || profileForm.first_name || 'O')[0].toUpperCase() }}
                </span>
              </div>
              
              <button
                type="button"
                class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center text-sm shadow-md transition-all cursor-pointer"
                title="Upload Profile Photo"
                @click="triggerFileInput"
              >
                <i class="ri-camera-fill" />
              </button>
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleImageChange"
              >
            </div>

            <!-- Identity Info & Badges -->
            <div class="flex-1 text-center sm:text-left">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                  <h3 class="text-lg font-black text-slate-900 dark:text-white">
                    {{ profileForm.full_name || `${profileForm.first_name} ${profileForm.last_name}` || 'Fleet Owner' }}
                  </h3>
                  <p class="text-xs text-slate-500 font-mono mt-0.5">
                    {{ profileForm.email }}
                  </p>
                </div>
                <div class="flex flex-wrap items-center justify-center sm:justify-end gap-2">
                  <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                    <i class="ri-checkbox-circle-fill mr-1" /> Verified Partner
                  </span>
                  <span
                    v-if="authUser?.unique_identifier"
                    class="px-3 py-1 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                  >
                    {{ authUser.unique_identifier }}
                  </span>
                </div>
              </div>

              <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs text-slate-500 dark:text-slate-400">
                <span v-if="profileForm.designation" class="flex items-center gap-1.5">
                  <i class="ri-building-line text-emerald-600 dark:text-emerald-400" />
                  <strong class="text-slate-700 dark:text-slate-200">{{ profileForm.designation }}</strong>
                </span>
                <span v-if="profileForm.contact_number || profileForm.mobile" class="flex items-center gap-1.5 font-mono">
                  <i class="ri-phone-line text-emerald-600 dark:text-emerald-400" />
                  {{ profileForm.contact_number || profileForm.mobile }}
                </span>
                <span v-if="profileForm.address" class="flex items-center gap-1.5">
                  <i class="ri-map-pin-line text-emerald-600 dark:text-emerald-400" />
                  {{ profileForm.address }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- 1. Personal & Identity Information -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <i class="ri-user-smile-line text-emerald-600 text-lg" />
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">
              Personal & Identity Details
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">First Name</label>
              <input
                v-model="profileForm.first_name"
                type="text"
                placeholder="e.g. Sujata"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Middle Name</label>
              <input
                v-model="profileForm.middle_name"
                type="text"
                placeholder="Optional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Last Name</label>
              <input
                v-model="profileForm.last_name"
                type="text"
                placeholder="e.g. Koirala"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Username</label>
              <input
                v-model="profileForm.username"
                type="text"
                placeholder="e.g. sujata_fleet"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Gender</label>
              <select
                v-model="profileForm.gender"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Date of Birth</label>
              <input
                v-model="profileForm.date_of_birth"
                type="date"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Marital Status</label>
              <select
                v-model="profileForm.marital_status"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="divorced">Divorced</option>
                <option value="widowed">Widowed</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 2. Fleet Enterprise & Legal Documentation -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <i class="ri-building-4-line text-emerald-600 text-lg" />
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">
              Fleet Enterprise & Legal Documentation
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Designation / Fleet Business Name</label>
              <input
                v-model="profileForm.designation"
                type="text"
                placeholder="e.g. Everest Luxury Cars"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Role / Position</label>
              <input
                v-model="profileForm.position"
                type="text"
                placeholder="e.g. Fleet Partner / Managing Director"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nationality</label>
              <input
                v-model="profileForm.nationality"
                type="text"
                placeholder="e.g. Nepali"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Citizenship / National ID No.</label>
              <input
                v-model="profileForm.citizenship_number"
                type="text"
                placeholder="e.g. 27-01-70-11223"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Passport Number</label>
              <input
                v-model="profileForm.passport_number"
                type="text"
                placeholder="e.g. N12345678"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>
          </div>
        </div>

        <!-- 3. Contact & Location Information -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <i class="ri-phone-find-line text-emerald-600 text-lg" />
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">
              Contact & Business Location
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Primary Contact Number</label>
              <input
                v-model="profileForm.contact_number"
                type="text"
                placeholder="+977-9851034567"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mobile Number</label>
              <input
                v-model="profileForm.mobile"
                type="text"
                placeholder="+977-9851034567"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alternative / Landline Phone</label>
              <input
                v-model="profileForm.phone"
                type="text"
                placeholder="Optional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Business / Fleet Office Address</label>
              <input
                v-model="profileForm.address"
                type="text"
                placeholder="e.g. Pokhara, Gandaki, Nepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>
          </div>
        </div>

        <!-- 4. Emergency Contact Information -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <i class="ri-alarm-warning-line text-emerald-600 text-lg" />
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">
              Emergency Contact Person
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Person Name</label>
              <input
                v-model="profileForm.contact_person_name"
                type="text"
                placeholder="e.g. Ram Koirala"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Relationship</label>
              <input
                v-model="profileForm.contact_relationship"
                type="text"
                placeholder="e.g. Spouse / Brother / Manager"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Emergency Phone</label>
              <input
                v-model="profileForm.emergency_contact"
                type="text"
                placeholder="+977-9800000000"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>
          </div>
        </div>

        <!-- Form Submit Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            class="w-full sm:w-auto px-8 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-2 cursor-pointer"
            :disabled="profileLoading"
          >
            <i
              v-if="profileLoading"
              class="ri-loader-4-line animate-spin text-base"
            />
            <i
              v-else
              class="ri-save-line text-base"
            />
            <span>{{ profileLoading ? 'Saving Profile Details...' : 'Save All Profile Changes' }}</span>
          </button>
        </div>
      </form>

      <!-- Theme Style Customization Card -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div>
          <h3 class="text-base font-bold text-slate-900 dark:text-white">
            Portal Appearance & Theme Style
          </h3>
          <p class="text-xs text-slate-500">
            Personalize your Owner Portal workspace visual theme. Changes persist to your account.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
          <button
            v-for="opt in themeOptions"
            :key="opt.value"
            type="button"
            :class="theme === opt.value ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-700'"
            class="p-4 rounded-2xl border text-left flex items-start gap-3.5 transition-all cursor-pointer"
            @click="setTheme(opt.value)"
          >
            <div
              :class="theme === opt.value ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 transition-colors"
            >
              <i :class="opt.icon" />
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ opt.label }}</span>
                <i
                  v-if="theme === opt.value"
                  class="ri-checkbox-circle-fill text-emerald-600 dark:text-emerald-400 text-base"
                />
              </div>
              <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">{{ opt.desc }}</p>
            </div>
          </button>
        </div>
      </div>

      <!-- Password Update Card -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div>
          <h3 class="text-base font-bold text-slate-900 dark:text-white">
            Change Account Password
          </h3>
          <p class="text-xs text-slate-500">
            Ensure your account uses a secure password of at least 8 characters.
          </p>
        </div>

        <MessageBox
          v-model="passwordMsg"
          type="success"
        />
        <MessageBox
          v-model="passwordError"
          type="error"
        />

        <form
          class="space-y-4"
          @submit.prevent="updatePassword"
        >
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">New Password</label>
              <div class="relative">
                <input
                  v-model="passwordForm.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  minlength="8"
                  class="w-full px-3.5 py-2.5 pe-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  placeholder="••••••••••••"
                >
                <button
                  type="button"
                  class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg focus:outline-none cursor-pointer transition-colors"
                  :title="showPassword ? 'Hide password' : 'Show password'"
                  @click="showPassword = !showPassword"
                >
                  <i
                    :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'"
                    class="text-base leading-none block"
                  />
                </button>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Confirm New Password</label>
              <div class="relative">
                <input
                  v-model="passwordForm.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  minlength="8"
                  class="w-full px-3.5 py-2.5 pe-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  placeholder="••••••••••••"
                >
                <button
                  type="button"
                  class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg focus:outline-none cursor-pointer transition-colors"
                  :title="showConfirmPassword ? 'Hide password' : 'Show password'"
                  @click="showConfirmPassword = !showConfirmPassword"
                >
                  <i
                    :class="showConfirmPassword ? 'ri-eye-off-line' : 'ri-eye-line'"
                    class="text-base leading-none block"
                  />
                </button>
              </div>
            </div>
          </div>

          <div class="pt-2 flex justify-end">
            <button
              type="submit"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer"
              :disabled="passwordLoading"
            >
              <i
                v-if="passwordLoading"
                class="ri-loader-4-line animate-spin"
              />
              <span>{{ passwordLoading ? 'Updating Password...' : 'Update Password' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </OwnerLayout>
</template>
