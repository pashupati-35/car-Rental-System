<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'
import { useOwnerTheme, type OwnerThemeStyle } from '@/composable/useOwnerTheme'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.owner || auth.value?.user)
const { theme, setTheme } = useOwnerTheme()

const profileForm = ref({
  full_name: user.value?.full_name || user.value?.name || '',
  email: user.value?.email || '',
  contact_number: user.value?.contact_number || '',
  address: user.value?.address || '',
})

const profileLoading = ref(false)
const profileMsg = ref('')
const profileError = ref('')

const updateProfile = async () => {
  profileLoading.value = true
  profileMsg.value = ''
  profileError.value = ''
  try {
    const res = await axios.patch('/owner/profile', profileForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Fleet Owner profile updated successfully.'
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || 'Failed to update profile.'
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

    <div class="space-y-6 max-w-5xl mx-auto">
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

      <!-- Profile Information Card -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white font-black text-2xl flex items-center justify-center shadow-md shadow-emerald-600/20">
            {{ (profileForm.full_name || 'O')[0].toUpperCase() }}
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
              {{ profileForm.full_name || 'Fleet Partner' }}
            </h3>
            <p class="text-xs text-slate-500 font-mono">
              {{ profileForm.email }}
            </p>
            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
              Verified Fleet Partner
            </span>
          </div>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="updateProfile"
        >
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Full Name</label>
              <input
                v-model="profileForm.full_name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address</label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Phone</label>
              <input
                v-model="profileForm.contact_number"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Business Address</label>
              <input
                v-model="profileForm.address"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>
          </div>

          <div class="pt-2 flex justify-end">
            <button
              type="submit"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/25 transition-all flex items-center justify-center gap-2 cursor-pointer"
              :disabled="profileLoading"
            >
              <i
                v-if="profileLoading"
                class="ri-loader-4-line animate-spin"
              />
              <span>{{ profileLoading ? 'Saving Profile...' : 'Save Profile Changes' }}</span>
            </button>
          </div>
        </form>
      </div>

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
