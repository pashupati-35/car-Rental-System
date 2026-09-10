<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.admin || auth.value?.user || auth.value?.owner || auth.value?.customer)

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
    const res = await axios.patch('/admin/password', passwordForm.value)
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
    const url = emailAuthEnabled.value ? '/admin/mfa/email/deactivate' : '/admin/mfa/email/activate'
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
    const res = await axios.post('/admin/mfa/generate')
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
    const res = await axios.post('/admin/mfa/activate', {
      secret_key: secretKey.value,
      verification_code: verificationCode.value,
      qr_code_url: qrCodeUrl.value,
    })

    if (res.data?.status === 'OK') {
      mfaEnabled.value = true
      showMfaSetup.value = false
      verificationCode.value = ''
      mfaSuccess.value = 'Google Authenticator (TOTP) successfully activated.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Invalid verification code. Please check your authenticator app.'
  } finally {
    mfaLoading.value = false
  }
}

const disableMfa = async () => {
  if (!confirm('Are you sure you want to disable Authenticator App (TOTP) verification?')) return
  mfaLoading.value = true
  mfaError.value = ''
  mfaSuccess.value = ''
  try {
    const res = await axios.post('/admin/mfa/deactivate')
    if (res.data?.status === 'OK') {
      mfaEnabled.value = false
      showMfaSetup.value = false
      mfaSuccess.value = 'Authenticator App (TOTP) disabled.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Failed to disable TOTP.'
  } finally {
    mfaLoading.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Account Security & MFA" />

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
            <span class="text-slate-600 dark:text-slate-300 font-semibold">Account Security & MFA</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 inline-flex items-center justify-center text-lg">
              <i class="ri-shield-keyhole-line" />
            </span>
            Security & Authentication
          </h1>
          <p class="text-xs text-slate-500 mt-1">
            Manage multi-factor verification, email authentication codes, and account password.
          </p>
        </div>

        <!-- Tab Switcher (Profile Information vs Account Security) -->
        <div class="inline-flex p-1 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <Link
            href="/admin/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-1.5"
          >
            <i class="ri-user-line text-sm" />
            <span>Profile Information</span>
          </Link>
          <Link
            href="/admin/security"
            class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-xs transition-all flex items-center gap-1.5"
          >
            <i class="ri-shield-check-line text-sm" />
            <span>Account Security & MFA</span>
          </Link>
        </div>
      </div>

      <!-- Global Success / Error Alerts -->
      <MessageBox
        v-model="mfaSuccess"
        type="success"
      />
      <MessageBox
        v-model="mfaError"
        type="error"
      />

      <!-- Two-Factor Authentication Card -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-6">
        <div>
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ri-shield-star-line text-indigo-600 dark:text-indigo-400 text-lg" />
            Two-factor authentication
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Add an extra layer of security using email verification codes or authenticator apps
          </p>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
          <!-- Option 1: Email Authentication -->
          <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-0.5">
              <div class="flex items-center gap-2">
                <span class="font-bold text-sm text-slate-900 dark:text-white">Email authentication</span>
                <span
                  :class="emailAuthEnabled ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700'"
                  class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full border"
                >
                  {{ emailAuthEnabled ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <p class="text-xs text-slate-500">
                Receive 6-digit verification codes via your registered administrative email upon sign-in.
              </p>
            </div>

            <button
              type="button"
              :disabled="mfaLoading"
              :class="emailAuthEnabled ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:hover:bg-rose-900 dark:text-rose-300 border-rose-200 dark:border-rose-800' : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
              class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-xs disabled:opacity-50 cursor-pointer shrink-0 border"
              @click="toggleEmailAuth"
            >
              {{ emailAuthEnabled ? 'Disable' : 'Activate Email Auth' }}
            </button>
          </div>

          <!-- Option 2: Authenticator App (TOTP) -->
          <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-0.5">
              <div class="flex items-center gap-2">
                <span class="font-bold text-sm text-slate-900 dark:text-white">Authenticator app (TOTP)</span>
                <span
                  :class="mfaEnabled ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700'"
                  class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full border"
                >
                  {{ mfaEnabled ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <p class="text-xs text-slate-500">
                Generate real-time authentication codes using Google Authenticator, Microsoft Authenticator, or 1Password.
              </p>
            </div>

            <button
              v-if="!mfaEnabled"
              type="button"
              :disabled="mfaLoading"
              class="px-4 py-2 rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 font-bold text-xs transition-all shadow-xs disabled:opacity-50 cursor-pointer shrink-0 flex items-center gap-1.5"
              @click="openMfaSetup"
            >
              <i class="ri-shield-keyhole-line" />
              <span>Enable Authenticator</span>
            </button>

            <button
              v-else
              type="button"
              :disabled="mfaLoading"
              class="px-4 py-2 rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 font-bold text-xs transition-all shadow-xs disabled:opacity-50 cursor-pointer shrink-0"
              @click="disableMfa"
            >
              Disable Authenticator
            </button>
          </div>
        </div>

        <!-- Authenticator App Setup Modal / Panel -->
        <div
          v-if="showMfaSetup"
          class="p-5 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900/60 space-y-4 animate-in fade-in duration-200"
        >
          <div class="flex items-center justify-between">
            <h4 class="font-bold text-sm text-indigo-950 dark:text-indigo-200 flex items-center gap-2">
              <i class="ri-qr-code-line text-lg" />
              Scan QR Code with Authenticator App
            </h4>
            <button
              type="button"
              class="text-xs text-slate-400 hover:text-slate-700 dark:hover:text-slate-200"
              @click="showMfaSetup = false"
            >
              Cancel
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
            <div class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-900 rounded-2xl border border-indigo-100 dark:border-slate-800 shadow-xs">
              <img
                v-if="qrCodeUrl"
                :src="qrCodeUrl"
                alt="Authenticator QR Code"
                class="w-40 h-40 object-contain rounded-xl"
              >
              <div
                v-else
                class="w-40 h-40 flex items-center justify-center bg-slate-100 dark:bg-slate-800 rounded-xl"
              >
                <i class="ri-loader-4-line animate-spin text-2xl text-indigo-600" />
              </div>
              <span class="text-[10px] text-slate-400 font-mono mt-2 select-all">Secret: {{ secretKey }}</span>
            </div>

            <div class="space-y-3">
              <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                1. Open your authenticator app (Google Authenticator / Authy).<br>
                2. Scan the QR code or manually enter the secret key.<br>
                3. Enter the 6-digit code below to activate.
              </p>

              <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">6-Digit Verification Code</label>
                <input
                  v-model="verificationCode"
                  type="text"
                  maxlength="6"
                  placeholder="000000"
                  class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-center font-mono text-xl tracking-widest focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                  @keyup.enter="confirmMfaSetup"
                >
              </div>

              <button
                type="button"
                :disabled="mfaLoading || !verificationCode"
                class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 transition-all flex items-center justify-center gap-2 cursor-pointer"
                @click="confirmMfaSetup"
              >
                <span v-if="mfaLoading">Activating Security...</span>
                <span v-else>Confirm & Activate TOTP</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Change Password Card (No Old Password Requirement) -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-6">
        <div>
          <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ri-lock-password-line text-indigo-600 dark:text-indigo-400 text-lg" />
            Change password
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Update your administrative account password
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
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                New Password
              </label>
              <input
                v-model="passwordForm.password"
                type="password"
                placeholder="Enter new password (min. 6 characters)"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Confirm New Password
              </label>
              <input
                v-model="passwordForm.password_confirmation"
                type="password"
                placeholder="Re-enter new password"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
              >
            </div>
          </div>

          <div class="pt-2 flex justify-end">
            <button
              type="submit"
              :disabled="passwordLoading"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <i
                v-if="passwordLoading"
                class="ri-loader-4-line animate-spin text-sm"
              />
              <i
                v-else
                class="ri-lock-2-line text-sm"
              />
              <span>{{ passwordLoading ? 'Updating Password...' : 'Change password' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
