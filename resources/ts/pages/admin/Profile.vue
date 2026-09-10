<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'
import { useAdminTheme, type AdminThemeStyle } from '@/composable/useAdminTheme'

const page = usePage()
const { theme, setTheme } = useAdminTheme()

const auth = ref(page.props.auth as any)
const user = ref(auth.value?.admin || auth.value?.user || auth.value?.owner || auth.value?.customer)

const profileForm = ref({
  name: user.value?.name || '',
  email: user.value?.email || '',
  contact_number: user.value?.contact_number || '',
  address: user.value?.address || '',
  designation: user.value?.designation || 'Super Administrator',
  avatar: user.value?.avatar || '',
  theme_style: (user.value?.theme_style as AdminThemeStyle) || theme.value || 'dark',
})

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
    const res = await axios.patch('/admin/profile', profileForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Administrative profile & theme details updated successfully.'
      if (res.data?.user) {
        user.value = res.data.user
      }
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || 'Failed to update profile.'
  } finally {
    profileLoading.value = false
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
            Profile Information
          </h1>
          <p class="text-xs text-slate-500 mt-1">
            Manage your administrative account identity, personal contact details, and organization role.
          </p>
        </div>

        <!-- Tab Switcher (Profile Information vs Account Security) -->
        <div class="inline-flex p-1 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <Link
            href="/admin/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-xs transition-all flex items-center gap-1.5"
          >
            <i class="ri-user-line text-sm" />
            <span>Profile Information</span>
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

      <!-- Profile Form Card -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-6">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
            {{ (profileForm.name || 'A')[0].toUpperCase() }}
          </div>
          <div>
            <h3 class="font-black text-lg text-slate-900 dark:text-white">
              {{ profileForm.name || 'Super Administrator' }}
            </h3>
            <p class="text-xs text-slate-400 font-mono">
              {{ profileForm.email }}
            </p>
            <span class="mt-1 inline-block text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800">
              {{ profileForm.designation || 'Fleet Director' }}
            </span>
          </div>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="updateProfile"
        >
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Full Name
              </label>
              <input
                v-model="profileForm.name"
                type="text"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Email Address
              </label>
              <input
                v-model="profileForm.email"
                type="email"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Contact Number / Mobile
              </label>
              <input
                v-model="profileForm.contact_number"
                type="text"
                placeholder="+977-9841234567"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Designation / Position
              </label>
              <input
                v-model="profileForm.designation"
                type="text"
                placeholder="Super Administrator & Fleet Director"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Office / Residential Address
              </label>
              <input
                v-model="profileForm.address"
                type="text"
                placeholder="Kathmandu, Bagmati, Nepal"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>
          </div>

          <div class="pt-2 flex justify-end">
            <button
              type="submit"
              :disabled="profileLoading"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <i
                v-if="profileLoading"
                class="ri-loader-4-line animate-spin text-sm"
              />
              <i
                v-else
                class="ri-save-line text-sm"
              />
              <span>{{ profileLoading ? 'Saving Profile...' : 'Save Profile Changes' }}</span>
            </button>
          </div>
        </form>
      </div>

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
            :class="[
              'p-4 rounded-2xl border-2 transition-all cursor-pointer flex flex-col justify-between relative overflow-hidden',
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
                  <i :class="card.icon" class="text-base text-indigo-600 dark:text-indigo-400" />
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
    </div>
  </AdminLayout>
</template>
