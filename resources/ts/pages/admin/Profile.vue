<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.admin || auth.value?.user || auth.value?.owner || auth.value?.customer)

const activeTab = ref<'security' | 'profile'>('security')

const profileForm = ref({
  name: user.value?.name || '',
  email: user.value?.email || '',
})
const profileLoading = ref(false)
const profileMsg = ref('')
const profileError = ref('')

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
})
const passwordLoading = ref(false)
const passwordMsg = ref('')
const passwordError = ref('')

const mfaEnabled = ref(Boolean(user.value?.is_mfa_enabled))
const emailAuthEnabled = ref(Boolean(user.value?.is_email_authentication_enabled))
const qrCodeUrl = ref('')
const secretKey = ref('')
const verificationCode = ref('')
const showMfaSetup = ref(false)
const mfaError = ref('')
const mfaSuccess = ref('')
const mfaLoading = ref(false)

const updateProfile = async () => {
  profileLoading.value = true
  profileMsg.value = ''
  profileError.value = ''
  try {
    const res = await axios.patch('/admin/profile', profileForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Profile updated successfully.'
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || 'Failed to update profile.'
  } finally {
    profileLoading.value = false
  }
}

const updatePassword = async () => {
  passwordLoading.value = true
  passwordMsg.value = ''
  passwordError.value = ''
  try {
    const res = await axios.patch('/admin/password', passwordForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      passwordMsg.value = 'Password changed successfully.'
      passwordForm.value = { current_password: '', password: '', password_confirmation: '' }
    }
  } catch (err: any) {
    passwordError.value = err.response?.data?.message || 'Failed to update password.'
  } finally {
    passwordLoading.value = false
  }
}

const toggleEmailAuth = async () => {
  mfaError.value = ''
  mfaSuccess.value = ''
  mfaLoading.value = true
  try {
    const endpoint = emailAuthEnabled.value ? '/admin/mfa/email/deactivate' : '/admin/mfa/email/activate'
    const res = await axios.post(endpoint)
    if (res.data.status === 'OK') {
      emailAuthEnabled.value = !emailAuthEnabled.value
      mfaSuccess.value = emailAuthEnabled.value
        ? 'Email verification enabled on login.'
        : 'Email verification disabled.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Failed to update email authentication.'
  } finally {
    mfaLoading.value = false
  }
}

const initMfaSetup = async () => {
  mfaError.value = ''
  mfaSuccess.value = ''
  mfaLoading.value = true
  try {
    const res = await axios.post('/admin/mfa/generate')
    if (res.data.status === 'OK') {
      secretKey.value = res.data.secret_key
      qrCodeUrl.value = res.data.qr_code_url || res.data.image_url
      showMfaSetup.value = true
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || err.response?.data?.errors || 'Failed to generate MFA secret.'
  } finally {
    mfaLoading.value = false
  }
}

const activateMfa = async () => {
  mfaError.value = ''
  mfaSuccess.value = ''
  mfaLoading.value = true
  try {
    const res = await axios.post('/admin/mfa/activate', {
      secret_key: secretKey.value,
      verification_code: verificationCode.value,
      image_url: qrCodeUrl.value,
    })

    if (res.data.status === 'OK') {
      mfaEnabled.value = true
      showMfaSetup.value = false
      verificationCode.value = ''
      mfaSuccess.value = 'Authenticator App (TOTP) successfully activated!'
    } else {
      mfaError.value = res.data.message || res.data.errors || 'Verification failed.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || err.response?.data?.errors || 'Invalid verification code.'
  } finally {
    mfaLoading.value = false
  }
}

const deactivateMfa = async () => {
  if (!confirm('Are you sure you want to disable authenticator app MFA?')) return
  mfaLoading.value = true
  mfaError.value = ''
  mfaSuccess.value = ''
  try {
    const res = await axios.post('/admin/mfa/deactivate')
    if (res.data.status === 'OK') {
      mfaEnabled.value = false
      showMfaSetup.value = false
      secretKey.value = ''
      qrCodeUrl.value = ''
      verificationCode.value = ''
      mfaSuccess.value = 'Authenticator app has been disabled.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Failed to disable MFA.'
  } finally {
    mfaLoading.value = false
  }
}
</script>

<template>
  <AppLayout>
    <Head title="Security Settings - Admin" />

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Breadcrumb Tabs matching reference image -->
      <div class="flex items-center gap-2 text-xs">
        <Link
          href="/admin/dashboard"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50"
        >
          <i class="ri-dashboard-line" />
          <span>Dashboard</span>
        </Link>
        <button
          type="button"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-blue-600 border-blue-200 bg-blue-50/70 font-semibold"
        >
          <i class="ri-user-3-line" />
          <span>Profile & Security</span>
        </button>
      </div>

      <!-- Page Header matching reference image -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Security settings
          </h2>
          <p class="text-xs text-gray-500 mt-0.5">
            Manage your administrator authentication credentials and account security
          </p>
        </div>

        <!-- Tab Toggle Buttons -->
        <div class="flex rounded-xl bg-gray-100 dark:bg-gray-800 p-1 text-xs font-semibold">
          <button
            type="button"
            :class="activeTab === 'security' ? 'bg-white dark:bg-gray-900 text-blue-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
            class="px-3.5 py-1.5 rounded-lg transition-all"
            @click="activeTab = 'security'"
          >
            Account Security & MFA
          </button>
          <button
            type="button"
            :class="activeTab === 'profile' ? 'bg-white dark:bg-gray-900 text-blue-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
            class="px-3.5 py-1.5 rounded-lg transition-all"
            @click="activeTab = 'profile'"
          >
            Profile Information
          </button>
        </div>
      </div>

      <!-- SECURITY TAB -->
      <div v-if="activeTab === 'security'" class="space-y-6">
        <!-- 1. Two-Factor Authentication Box -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
              Two-factor authentication
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
              Add an extra layer of security using email or authenticator apps
            </p>
          </div>

          <div class="p-6 space-y-6">
            <!-- Email Authentication Row -->
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800">
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Email authentication</p>
                <p class="text-xs text-gray-500">Receive verification codes via your registered email</p>
              </div>
              <div class="flex items-center gap-2">
                <span
                  :class="emailAuthEnabled ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200'"
                  class="px-2.5 py-1 rounded-md text-xs font-semibold border"
                >
                  {{ emailAuthEnabled ? 'Active' : 'Inactive' }}
                </span>
                <button
                  type="button"
                  :disabled="mfaLoading"
                  class="px-3 py-1 rounded-lg text-xs font-medium border transition-colors"
                  :class="emailAuthEnabled ? 'border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100' : 'border-blue-200 text-blue-600 bg-blue-50 hover:bg-blue-100'"
                  @click="toggleEmailAuth"
                >
                  {{ emailAuthEnabled ? 'Disable' : 'Enable' }}
                </button>
              </div>
            </div>

            <!-- Authenticator App Row -->
            <div class="flex items-center justify-between py-2">
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Authenticator app (TOTP)</p>
                <p class="text-xs text-gray-500">Google Authenticator, Microsoft Authenticator or similar</p>
              </div>
              <span
                :class="mfaEnabled ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200'"
                class="px-2.5 py-1 rounded-md text-xs font-semibold border"
              >
                {{ mfaEnabled ? 'Active' : 'Inactive' }}
              </span>
            </div>

            <!-- Alerts -->
            <div v-if="mfaSuccess" class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
              <i class="ri-checkbox-circle-fill text-emerald-600" />
              <span>{{ mfaSuccess }}</span>
            </div>
            <div v-if="mfaError" class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
              <i class="ri-error-warning-fill text-rose-600" />
              <span>{{ mfaError }}</span>
            </div>

            <!-- Setup Box when generating MFA -->
            <div
              v-if="showMfaSetup"
              class="p-5 rounded-2xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40 space-y-4"
            >
              <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                Scan QR Code with Authenticator App
              </h4>
              <div class="flex flex-col sm:flex-row items-center gap-6">
                <div class="p-2.5 bg-white rounded-xl shadow-sm border border-gray-200">
                  <img :src="qrCodeUrl" class="w-36 h-36 object-contain" alt="QR Code" />
                </div>
                <div class="space-y-3 flex-1 text-xs">
                  <p class="text-gray-600 dark:text-gray-300">
                    If you cannot scan, manually enter this secret key:
                  </p>
                  <div class="p-2.5 bg-white dark:bg-gray-800 rounded-lg font-mono font-bold text-blue-600 border border-gray-200 dark:border-gray-700 select-all">
                    {{ secretKey }}
                  </div>
                  <div class="space-y-1 pt-1">
                    <label class="block font-semibold text-gray-700 dark:text-gray-300">Enter 6-digit TOTP Code</label>
                    <div class="flex gap-2">
                      <input
                        v-model="verificationCode"
                        type="text"
                        maxlength="6"
                        placeholder="123456"
                        class="w-36 px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 font-mono text-center tracking-widest text-sm focus:ring-2 focus:ring-blue-500"
                      />
                      <button
                        type="button"
                        :disabled="mfaLoading"
                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md disabled:opacity-50"
                        @click="activateMfa"
                      >
                        {{ mfaLoading ? 'Verifying...' : 'Confirm & Activate' }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Button matching reference design -->
            <div class="pt-2">
              <button
                v-if="mfaEnabled"
                type="button"
                :disabled="mfaLoading"
                class="w-full py-2.5 px-4 rounded-xl border border-rose-200 bg-rose-50/70 hover:bg-rose-100 text-rose-600 text-xs font-semibold flex items-center justify-center gap-2 transition-colors"
                @click="deactivateMfa"
              >
                <i class="ri-close-circle-line text-sm" />
                <span>Disable authenticator</span>
              </button>
              <button
                v-else-if="!showMfaSetup"
                type="button"
                :disabled="mfaLoading"
                class="w-full py-2.5 px-4 rounded-xl border border-blue-200 bg-blue-50/70 hover:bg-blue-100 text-blue-600 text-xs font-semibold flex items-center justify-center gap-2 transition-colors"
                @click="initMfaSetup"
              >
                <i class="ri-shield-keyhole-line text-sm" />
                <span>{{ mfaLoading ? 'Generating Secret...' : 'Enable authenticator' }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- 2. Change Password Box (Exact reference layout) -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 flex items-center justify-center text-base">
              <i class="ri-lock-password-line" />
            </div>
            <div>
              <h3 class="text-base font-bold text-gray-900 dark:text-white">
                Change password
              </h3>
              <p class="text-xs text-gray-500">Update your account password</p>
            </div>
          </div>

          <form class="p-6 space-y-4" @submit.prevent="updatePassword">
            <div v-if="passwordMsg" class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
              <i class="ri-checkbox-circle-fill text-emerald-600" />
              <span>{{ passwordMsg }}</span>
            </div>
            <div v-if="passwordError" class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
              <i class="ri-error-warning-fill text-rose-600" />
              <span>{{ passwordError }}</span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
              <input
                v-model="passwordForm.current_password"
                type="password"
                required
                placeholder="••••••••"
                class="w-full max-w-md px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                <input
                  v-model="passwordForm.password"
                  type="password"
                  required
                  placeholder="••••••••"
                  class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                <input
                  v-model="passwordForm.password_confirmation"
                  type="password"
                  required
                  placeholder="••••••••"
                  class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <div class="pt-2">
              <button
                type="submit"
                :disabled="passwordLoading"
                class="w-full py-2.5 px-4 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs font-semibold flex items-center justify-center gap-2 transition-colors disabled:opacity-50"
              >
                <i class="ri-lock-line text-sm" />
                <span>{{ passwordLoading ? 'Updating Password...' : 'Change password' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- PROFILE INFO TAB -->
      <div v-else class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 space-y-5">
        <div>
          <h3 class="text-base font-bold text-gray-900 dark:text-white">
            Admin Profile Details
          </h3>
          <p class="text-xs text-gray-500 mt-0.5">
            Manage your personal administrative contact details
          </p>
        </div>

        <div v-if="profileMsg" class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
          <i class="ri-checkbox-circle-fill text-emerald-600" />
          <span>{{ profileMsg }}</span>
        </div>
        <div v-if="profileError" class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
          <i class="ri-error-warning-fill text-rose-600" />
          <span>{{ profileError }}</span>
        </div>

        <form class="space-y-4" @submit.prevent="updateProfile">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
              <input
                v-model="profileForm.name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="profileLoading"
              class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 disabled:opacity-50"
            >
              {{ profileLoading ? 'Saving...' : 'Save Profile Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
