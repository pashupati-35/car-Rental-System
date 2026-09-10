<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.customer || auth.value?.user)

const passwordForm = ref({
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

const updatePassword = async () => {
  if (!passwordForm.value.password || passwordForm.value.password.length < 6) {
    passwordError.value = 'New password must be at least 6 characters.'

    return
  }
  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    passwordError.value = 'Password confirmation does not match.'

    return
  }

  passwordLoading.value = true
  passwordMsg.value = ''
  passwordError.value = ''
  try {
    const res = await axios.patch('/customer/password', passwordForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      passwordMsg.value = 'Password changed successfully.'
      passwordForm.value = { password: '', password_confirmation: '' }
    }
  } catch (err: any) {
    passwordError.value = err.response?.data?.message || err.response?.data?.errors?.password?.[0] || 'Failed to update password.'
  } finally {
    passwordLoading.value = false
  }
}

const toggleEmailAuth = async () => {
  mfaLoading.value = true
  mfaError.value = ''
  mfaSuccess.value = ''
  try {
    const url = emailAuthEnabled.value ? '/customer/mfa/email/deactivate' : '/customer/mfa/email/activate'
    const res = await axios.post(url)
    if (res.data?.status === 'OK') {
      emailAuthEnabled.value = !emailAuthEnabled.value
      mfaSuccess.value = emailAuthEnabled.value
        ? 'Email verification active. Security codes will be sent upon login.'
        : 'Email authentication disabled.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Failed to update email authentication.'
  } finally {
    mfaLoading.value = false
  }
}

const openMfaSetup = async () => {
  mfaLoading.value = true
  mfaError.value = ''
  try {
    const res = await axios.post('/customer/mfa/generate')
    if (res.data?.status === 'OK') {
      qrCodeUrl.value = res.data.qr_code_url || res.data.image_url
      secretKey.value = res.data.secret_key
      showMfaSetup.value = true
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Failed to generate authenticator secret.'
  } finally {
    mfaLoading.value = false
  }
}

const confirmMfaSetup = async () => {
  if (!verificationCode.value) {
    mfaError.value = 'Please enter the 6-digit code from your authenticator app.'

    return
  }

  mfaLoading.value = true
  mfaError.value = ''
  try {
    const res = await axios.post('/customer/mfa/activate', {
      secret_key: secretKey.value,
      verification_code: verificationCode.value,
      qr_code_url: qrCodeUrl.value,
    })

    if (res.data?.status === 'OK') {
      mfaEnabled.value = true
      showMfaSetup.value = false
      verificationCode.value = ''
      mfaSuccess.value = 'Authenticator App (TOTP) 2FA successfully activated!'
    } else {
      mfaError.value = res.data?.message || res.data?.errors || 'Invalid code.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || err.response?.data?.errors || 'Invalid code.'
  } finally {
    mfaLoading.value = false
  }
}

const disableMfa = async () => {
  if (!confirm('Are you sure you want to disable Authenticator 2FA?')) return

  mfaLoading.value = true
  mfaError.value = ''
  mfaSuccess.value = ''
  try {
    const res = await axios.post('/customer/mfa/deactivate')
    if (res.data?.status === 'OK') {
      mfaEnabled.value = false
      mfaSuccess.value = 'Authenticator 2FA disabled.'
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
    <Head title="Customer Account Security & MFA" />

    <div class="space-y-6 max-w-5xl mx-auto">
      <!-- Top Header & Tabs -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-base">
              <i class="ri-shield-keyhole-line" />
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Customer Security & 2-Step Auth
            </h1>
          </div>
          <p class="text-xs text-slate-500 mt-1">
            Manage your two-factor login verification, email authentication, and account password.
          </p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl shrink-0 self-start sm:self-auto">
          <Link
            href="/customer/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
          >
            Profile Details
          </Link>
          <Link
            href="/customer/security"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-300 shadow-xs"
          >
            Security & MFA
          </Link>
        </div>
      </div>

      <!-- Feedback Alerts -->
      <MessageBox
        v-model="mfaSuccess"
        type="success"
      />
      <MessageBox
        v-model="mfaError"
        type="error"
      />
      <MessageBox
        v-model="passwordMsg"
        type="success"
      />
      <MessageBox
        v-model="passwordError"
        type="error"
      />

      <!-- Two-Factor Authentication Box -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
        <div>
          <h2 class="text-base font-extrabold text-slate-900 dark:text-white">
            Two-factor authentication
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Add an extra layer of security to your customer account using email verification or authenticator apps
          </p>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
          <!-- Email Authentication Row -->
          <div class="py-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h3 class="text-xs font-bold text-slate-900 dark:text-white">
                Email authentication
              </h3>
              <p class="text-xs text-slate-500">
                Receive verification codes via your registered email ({{ user?.email }})
              </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
              <span
                :class="emailAuthEnabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider"
              >
                {{ emailAuthEnabled ? 'Active' : 'Inactive' }}
              </span>

              <button
                type="button"
                :class="emailAuthEnabled ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 border-rose-200 dark:border-rose-800' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-xs'"
                class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all border border-transparent cursor-pointer"
                :disabled="mfaLoading"
                @click="toggleEmailAuth"
              >
                {{ emailAuthEnabled ? 'Disable' : 'Enable Email Auth' }}
              </button>
            </div>
          </div>

          <!-- Authenticator App Row -->
          <div class="py-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h3 class="text-xs font-bold text-slate-900 dark:text-white">
                Authenticator app (TOTP)
              </h3>
              <p class="text-xs text-slate-500">
                Google Authenticator, Microsoft Authenticator, or 1Password
              </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
              <span
                :class="mfaEnabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider"
              >
                {{ mfaEnabled ? 'Active' : 'Inactive' }}
              </span>

              <button
                v-if="!mfaEnabled"
                type="button"
                class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer"
                :disabled="mfaLoading"
                @click="openMfaSetup"
              >
                Enable Authenticator
              </button>
              <button
                v-else
                type="button"
                class="px-4 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800 text-xs font-bold transition-all cursor-pointer"
                :disabled="mfaLoading"
                @click="disableMfa"
              >
                Disable TOTP
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Change Password Section (No Current Password Required) -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
        <div class="flex items-center gap-2">
          <i class="ri-lock-password-line text-lg text-blue-600" />
          <h2 class="text-base font-extrabold text-slate-900 dark:text-white">
            Change Password
          </h2>
        </div>

        <div
          v-if="passwordMsg"
          class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold flex items-center justify-between"
        >
          <span>{{ passwordMsg }}</span>
          <button
            class="text-emerald-700 cursor-pointer"
            @click="passwordMsg = ''"
          >
            <i class="ri-close-line" />
          </button>
        </div>

        <div
          v-if="passwordError"
          class="p-3.5 rounded-2xl bg-rose-50 text-rose-800 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-xs font-semibold flex items-center justify-between"
        >
          <span>{{ passwordError }}</span>
          <button
            class="text-rose-700 cursor-pointer"
            @click="passwordError = ''"
          >
            <i class="ri-close-line" />
          </button>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="updatePassword"
        >
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">New Password</label>
              <input
                v-model="passwordForm.password"
                type="password"
                required
                placeholder="••••••••"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Confirm New Password</label>
              <input
                v-model="passwordForm.password_confirmation"
                type="password"
                required
                placeholder="••••••••"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>

          <div class="pt-2">
            <button
              type="submit"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 font-bold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs"
              :disabled="passwordLoading"
            >
              <i
                v-if="passwordLoading"
                class="ri-loader-4-line animate-spin"
              />
              <span>{{ passwordLoading ? 'Updating Password...' : 'Change Password' }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Authenticator Setup Modal -->
      <div
        v-if="showMfaSetup"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-150"
      >
        <div
          class="relative w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-4"
          @click.stop
        >
          <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
              Setup Authenticator App
            </h3>
            <button
              type="button"
              class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer"
              @click="showMfaSetup = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <p class="text-xs text-slate-500">
            Scan this QR code using Google Authenticator, Microsoft Authenticator, or 1Password.
          </p>

          <!-- QR Code Preview -->
          <div class="flex justify-center p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700">
            <img
              v-if="qrCodeUrl"
              :src="qrCodeUrl"
              alt="MFA QR Code"
              class="w-44 h-44 rounded-xl shadow-xs"
            >
            <div
              v-else
              class="w-44 h-44 flex items-center justify-center text-xs text-slate-400"
            >
              Loading QR Code...
            </div>
          </div>

          <!-- Secret Key Fallback -->
          <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-xl space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Or enter code manually:</span>
            <code class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400 break-all select-all">{{ secretKey }}</code>
          </div>

          <!-- Verification Code Input -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Enter 6-Digit Code</label>
            <input
              v-model="verificationCode"
              type="text"
              maxlength="6"
              placeholder="000000"
              class="w-full text-center tracking-[0.3em] font-mono text-xl py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              @keyup.enter="confirmMfaSetup"
            >
          </div>

          <div class="flex gap-2 pt-2">
            <button
              type="button"
              class="flex-1 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200 cursor-pointer"
              @click="showMfaSetup = false"
            >
              Cancel
            </button>
            <button
              type="button"
              class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs cursor-pointer"
              :disabled="mfaLoading"
              @click="confirmMfaSetup"
            >
              {{ mfaLoading ? 'Verifying...' : 'Activate MFA' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
