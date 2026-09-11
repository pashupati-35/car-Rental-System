<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'
import { useAdminTheme, type AdminThemeStyle } from '@/composable/useAdminTheme'

const props = defineProps<{
  user?: any
}>()

const page = usePage()
const authUser = computed(() => props.user || (page.props.auth as any)?.admin || (page.props.auth as any)?.user || {})
const { theme, setTheme } = useAdminTheme()

// Initialize profile form with all database fields
const profileForm = ref({
  first_name: authUser.value?.first_name || '',
  middle_name: authUser.value?.middle_name || '',
  last_name: authUser.value?.last_name || '',
  name: authUser.value?.name || authUser.value?.full_name || '',
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
  designation: authUser.value?.designation || 'Super Administrator',
  position: authUser.value?.position || 'Fleet Director',
  emergency_contact: authUser.value?.emergency_contact || '',
  contact_person_name: authUser.value?.contact_person_name || '',
  contact_relationship: authUser.value?.contact_relationship || '',
  theme_style: (authUser.value?.theme_style as AdminThemeStyle) || theme.value || 'dark',
})

const resolveImageUrl = (userObj: any) => {
  if (!userObj) return ''
  if (userObj.image_url) return userObj.image_url
  if (typeof userObj.image_path === 'string') return userObj.image_path
  if (userObj.image_path?.original) return userObj.image_path.original
  if (userObj.image) {
    return userObj.image.startsWith('http') ? userObj.image : '/' + userObj.image.replace(/^\/+/, '')
  }
  if (userObj.avatar) {
    return userObj.avatar.startsWith('http') ? userObj.avatar : '/' + userObj.avatar.replace(/^\/+/, '')
  }
  
  return ''
}

const currentImage = ref(resolveImageUrl(authUser.value))
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)
const imageLoadError = ref(false)

const onImageError = () => {
  imageLoadError.value = true
}

const handleImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]

    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
    imageLoadError.value = false
  }
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const profileLoading = ref(false)
const profileMsg = ref('')
const profileError = ref('')

const themeCards: { id: AdminThemeStyle; title: string; desc: string; icon: string; previewClass: string }[] = [
  {
    id: 'dark',
    title: 'Dark Mode',
    desc: 'Deep slate background with vibrant indigo accents. Easy on the eyes.',
    icon: 'ri-moon-clear-line',
    previewClass: 'bg-slate-900 border-slate-700 text-white',
  },
  {
    id: 'midnight',
    title: 'Midnight Obsidian',
    desc: 'Ultra-deep obsidian black with glowing indigo & purple highlights.',
    icon: 'ri-sparkling-2-line',
    previewClass: 'bg-[#070b14] border-indigo-900/60 text-white',
  },
  {
    id: 'light',
    title: 'Light Studio',
    desc: 'Crisp bright backgrounds with high contrast text and crisp cards.',
    icon: 'ri-sun-line',
    previewClass: 'bg-slate-50 border-slate-300 text-slate-900',
  },
  {
    id: 'system',
    title: 'System Automatic',
    desc: 'Synchronizes dynamically with your operating system preference.',
    icon: 'ri-computer-line',
    previewClass: 'bg-gradient-to-r from-slate-100 to-slate-900 border-slate-500 text-slate-700',
  },
]

const onSelectTheme = async (selected: AdminThemeStyle) => {
  profileForm.value.theme_style = selected
  await setTheme(selected)
}

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

    const res = await axios.post('/admin/profile?_method=PATCH', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Administrative profile details & preferences updated successfully.'
      if (res.data?.user) {
        currentImage.value = resolveImageUrl(res.data.user)
        imagePreview.value = null
        imageFile.value = null
        imageLoadError.value = false
        if (res.data.user.name) {
          profileForm.value.name = res.data.user.name
        }
      }
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || 'Failed to update administrative profile details.'
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
    const res = await axios.patch('/admin/password', passwordForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      passwordMsg.value = 'Administrative account password changed successfully.'
      passwordForm.value.password = ''
      passwordForm.value.password_confirmation = ''
    }
  } catch (err: any) {
    passwordError.value = err.response?.data?.message || 'Failed to update password.'
  } finally {
    passwordLoading.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Admin Profile Information" />

    <div class="space-y-6 max-w-5xl mx-auto pb-12">
      <!-- Breadcrumb Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <Link
              href="/admin/dashboard"
              class="hover:text-indigo-600 transition-colors"
            >
              Dashboard
            </Link>
            <span>/</span>
            <span class="text-slate-600 dark:text-slate-300 font-semibold">Admin Profile</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 inline-flex items-center justify-center text-lg">
              <i class="ri-user-settings-line" />
            </span>
            Admin Profile Settings
          </h1>
          <p class="text-xs text-slate-500 mt-1">
            Manage your administrative account identity, personal contact details, credentials, and organizational roles.
          </p>
        </div>

        <!-- Tab Switcher (Profile Information vs Account Security) -->
        <div class="inline-flex p-1 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs shrink-0 self-start sm:self-auto">
          <Link
            href="/admin/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-xs transition-all flex items-center gap-1.5"
          >
            <i class="ri-user-line text-sm" />
            <span>Profile & Theme</span>
          </Link>
          <Link
            href="/admin/security"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-1.5"
          >
            <i class="ri-shield-check-line text-sm" />
            <span>Account Security & MFA</span>
          </Link>
        </div>
      </div>

      <!-- Success / Error Alert Messages -->
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
              <div class="w-24 h-24 rounded-3xl overflow-hidden bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-black text-3xl flex items-center justify-center shadow-lg shadow-indigo-600/20 border-2 border-indigo-500/30">
                <img
                  v-if="(imagePreview || currentImage) && !imageLoadError"
                  :src="imagePreview || currentImage"
                  alt="Admin Avatar"
                  class="w-full h-full object-cover"
                  @error="onImageError"
                >
                <span v-else>
                  {{ (profileForm.name || profileForm.first_name || 'A')[0].toUpperCase() }}
                </span>
              </div>
              
              <button
                type="button"
                class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center text-sm shadow-md transition-all cursor-pointer"
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
                    {{ profileForm.name || `${profileForm.first_name} ${profileForm.last_name}` || 'Super Administrator' }}
                  </h3>
                  <p class="text-xs text-slate-500 font-mono mt-0.5">
                    {{ profileForm.email }}
                  </p>
                </div>
                <div class="flex flex-wrap items-center justify-center sm:justify-end gap-2">
                  <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800">
                    <i class="ri-shield-user-fill me-1" /> {{ profileForm.designation || 'Administrator' }}
                  </span>
                  <span
                    v-if="authUser?.unique_identifier"
                    class="px-3 py-1 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700"
                  >
                    {{ authUser.unique_identifier }}
                  </span>
                </div>
              </div>

              <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs text-slate-500 dark:text-slate-400">
                <span
                  v-if="profileForm.position"
                  class="flex items-center gap-1.5"
                >
                  <i class="ri-briefcase-line text-indigo-600 dark:text-indigo-400" />
                  <strong class="text-slate-700 dark:text-slate-200">{{ profileForm.position }}</strong>
                </span>
                <span
                  v-if="profileForm.contact_number || profileForm.mobile"
                  class="flex items-center gap-1.5 font-mono"
                >
                  <i class="ri-phone-line text-indigo-600 dark:text-indigo-400" />
                  {{ profileForm.contact_number || profileForm.mobile }}
                </span>
                <span
                  v-if="profileForm.address"
                  class="flex items-center gap-1.5"
                >
                  <i class="ri-map-pin-line text-indigo-600 dark:text-indigo-400" />
                  {{ profileForm.address }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- 1. Personal & Identity Information Card -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <i class="ri-user-smile-line text-indigo-600 text-lg" />
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">
              Personal & Account Identity Details
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">First Name</label>
              <input
                v-model="profileForm.first_name"
                type="text"
                placeholder="e.g. Pashupati"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Middle Name</label>
              <input
                v-model="profileForm.middle_name"
                type="text"
                placeholder="Optional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Last Name</label>
              <input
                v-model="profileForm.last_name"
                type="text"
                placeholder="e.g. Sah"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Display / Full Name</label>
              <input
                v-model="profileForm.name"
                type="text"
                placeholder="e.g. Pashupati Sah"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Username</label>
              <input
                v-model="profileForm.username"
                type="text"
                placeholder="e.g. pashupati_admin"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Gender</label>
              <select
                v-model="profileForm.gender"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Date of Birth</label>
              <input
                v-model="profileForm.date_of_birth"
                type="date"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>
        </div>

        <!-- 2. Official Role & Legal Documentation Card -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <i class="ri-government-line text-indigo-600 text-lg" />
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">
              Official Position & Identity Documentation
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Designation</label>
              <input
                v-model="profileForm.designation"
                type="text"
                placeholder="e.g. Super Administrator"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Role / Position</label>
              <input
                v-model="profileForm.position"
                type="text"
                placeholder="e.g. Fleet Director"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Marital Status</label>
              <select
                v-model="profileForm.marital_status"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
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
                placeholder="e.g. Nepali"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Citizenship / National ID No.</label>
              <input
                v-model="profileForm.citizenship_number"
                type="text"
                placeholder="e.g. 27-01-70-12345"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Passport Number</label>
              <input
                v-model="profileForm.passport_number"
                type="text"
                placeholder="e.g. NP12345678"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>
        </div>

        <!-- 3. Contact & Office Location Information Card -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <i class="ri-contacts-book-2-line text-indigo-600 text-lg" />
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">
              Contact Channels & Address
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Primary Contact Number</label>
              <input
                v-model="profileForm.contact_number"
                type="text"
                placeholder="+977-9841234567"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mobile Number</label>
              <input
                v-model="profileForm.mobile"
                type="text"
                placeholder="+977-9841234567"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alternative / Office Phone</label>
              <input
                v-model="profileForm.phone"
                type="text"
                placeholder="Optional"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Office / Residential Address</label>
              <input
                v-model="profileForm.address"
                type="text"
                placeholder="e.g. Kathmandu, Bagmati, Nepal"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>
        </div>

        <!-- 4. Emergency Contact Information Card -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
          <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
            <i class="ri-alarm-warning-line text-indigo-600 text-lg" />
            <h3 class="font-bold text-sm text-slate-900 dark:text-white">
              Emergency Contact Information
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Person Name</label>
              <input
                v-model="profileForm.contact_person_name"
                type="text"
                placeholder="e.g. Anjali Sah"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Relationship</label>
              <input
                v-model="profileForm.contact_relationship"
                type="text"
                placeholder="e.g. Spouse / Brother / Manager"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Emergency Phone</label>
              <input
                v-model="profileForm.emergency_contact"
                type="text"
                placeholder="+977-9800000000"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
            </div>
          </div>
        </div>

        <!-- Form Submit Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            class="w-full sm:w-auto px-8 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-lg shadow-indigo-600/25 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
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

      <!-- Theme & Appearance Customizer Card (Saved into Database) -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center text-lg shadow-md shadow-purple-500/20">
              <i class="ri-palette-line" />
            </div>
            <div>
              <h3 class="font-black text-lg text-slate-900 dark:text-white">
                Theme & Layout Appearance
              </h3>
              <p class="text-xs text-slate-400">
                Saved in your administrative account database profile and automatically applied across all pages.
              </p>
            </div>
          </div>
          <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800 uppercase">
            Active: {{ theme }}
          </span>
        </div>

        <!-- 4 Interactive Theme Selection Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div
            v-for="card in themeCards"
            :key="card.id"
            class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex flex-col justify-between relative overflow-hidden"
            :class="[
              theme === card.id 
                ? 'border-indigo-600 dark:border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/30 shadow-md ring-2 ring-indigo-500/20' 
                : 'border-slate-200/80 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30'
            ]"
            @click="onSelectTheme(card.id)"
          >
            <!-- Active Checkmark Badge -->
            <div
              v-if="theme === card.id"
              class="absolute top-3 right-3 w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs shadow-xs"
            >
              <i class="ri-check-line font-bold" />
            </div>

            <div class="space-y-3">
              <!-- Mini Preview Frame -->
              <div
                :class="card.previewClass"
                class="w-full h-16 rounded-xl border p-2 flex flex-col justify-between shadow-xs"
              >
                <div class="flex items-center gap-1.5">
                  <div class="w-2 h-2 rounded-full bg-rose-500" />
                  <div class="w-2 h-2 rounded-full bg-amber-500" />
                  <div class="w-2 h-2 rounded-full bg-emerald-500" />
                </div>
                <div class="w-2/3 h-2 rounded-full bg-indigo-500/40" />
              </div>

              <div>
                <div class="flex items-center gap-2 font-bold text-sm text-slate-900 dark:text-white">
                  <i
                    :class="card.icon"
                    class="text-base text-indigo-600 dark:text-indigo-400"
                  />
                  <span>{{ card.title }}</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                  {{ card.desc }}
                </p>
              </div>
            </div>

            <div class="pt-3 mt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
              <span class="text-[10px] font-mono font-bold text-slate-400 uppercase">
                '{{ card.id }}'
              </span>
              <span
                v-if="theme === card.id"
                class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400"
              >
                Selected
              </span>
              <span
                v-else
                class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
              >
                Click to Apply
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Password Update Card -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div>
          <h3 class="text-base font-bold text-slate-900 dark:text-white">
            Change Account Password
          </h3>
          <p class="text-xs text-slate-500">
            Ensure your administrative account uses a secure password of at least 8 characters.
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
                  class="w-full px-3.5 py-2.5 pe-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
                  class="w-full px-3.5 py-2.5 pe-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
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
  </AdminLayout>
</template>
