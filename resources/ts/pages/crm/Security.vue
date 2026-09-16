<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'

const props = defineProps<{
  user?: any
}>()

const page = usePage()
const auth = ref(page.props.auth as any)
const currentUser = ref(props.user || auth.value?.admin || auth.value?.user)

const passwordForm = ref({
  password: '',
  password_confirmation: '',
})

const passwordLoading = ref(false)
const passwordMsg = ref('')
const passwordError = ref('')

const mfaEnabled = ref(Boolean(currentUser.value?.is_mfa_enabled))
const emailAuthEnabled = ref(Boolean(currentUser.value?.is_email_authentication_enabled))
const qrCodeUrl = ref('')
const secretKey = ref('')
const verificationCode = ref('')
const showMfaSetup = ref(false)
const mfaError = ref('')
const mfaSuccess = ref('')
const mfaLoading = ref(false)
const copiedKey = ref(false)

const copySecretKey = () => {
  if (!secretKey.value) return
  navigator.clipboard.writeText(secretKey.value)
  copiedKey.value = true
  setTimeout(() => {
    copiedKey.value = false
  }, 2000)
}

const updatePassword = async () => {
  if (!passwordForm.value.password || passwordForm.value.password.length < 8) {
    passwordError.value = 'New password must be at least 8 characters.'
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
    const res = await axios.patch('/crm/security/password', passwordForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      passwordMsg.value = 'CRM account password changed successfully.'
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
    const url = emailAuthEnabled.value ? '/crm/security/mfa/email/deactivate' : '/crm/security/mfa/email/activate'
    const res = await axios.post(url)
    if (res.data?.status === 'OK') {
      emailAuthEnabled.value = !emailAuthEnabled.value
      mfaSuccess.value = emailAuthEnabled.value
        ? 'Email Two-Factor Authentication activated. Security OTP codes will be sent upon CRM login.'
        : 'Email Two-Factor Authentication disabled.'
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
    const res = await axios.post('/crm/security/mfa/generate')
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
  if (!verificationCode.value || verificationCode.value.length < 6) {
    mfaError.value = 'Please enter the 6-digit code from your authenticator app.'
    return
  }
  mfaLoading.value = true
  mfaError.value = ''
  try {
    const res = await axios.post('/crm/security/mfa/activate', {
      secret_key: secretKey.value,
      verification_code: verificationCode.value,
      qr_code_url: qrCodeUrl.value,
    })

    if (res.data?.status === 'OK') {
      mfaEnabled.value = true
      showMfaSetup.value = false
      verificationCode.value = ''
      mfaSuccess.value = 'Authenticator App (TOTP) successfully activated for this CRM account.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || err.response?.data?.errors || 'Invalid verification code. Please check your authenticator app.'
  } finally {
    mfaLoading.value = false
  }
}

const disableMfa = async () => {
  if (!confirm('Are you sure you want to disable Authenticator App (TOTP) verification for your CRM account?')) return
  mfaLoading.value = true
  mfaError.value = ''
  mfaSuccess.value = ''
  try {
    const res = await axios.post('/crm/security/mfa/deactivate')
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
  <CrmLayout>
    <Head title="Account Security & MFA - CRM Portal" />

    <div class="max-w-4xl mx-auto space-y-8 pb-16">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <Link href="/crm/dashboard" class="hover:text-emerald-600 transition flex items-center gap-1">
          <i class="ri-dashboard-3-line" /> CRM Dashboard
        </Link>
        <span>/</span>
        <span>Account Security & MFA</span>
      </div>

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <i class="ri-shield-keyhole-line text-emerald-600 dark:text-emerald-400" />
            CRM Security & Two-Factor Authentication
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Protect your CRM staff credentials, configure time-based OTP authenticator apps, and enforce email login verification.
          </p>
        </div>

        <div class="flex items-center gap-2">
          <span
            class="px-3 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1.5"
            :class="(mfaEnabled || emailAuthEnabled) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300'"
          >
            <i :class="(mfaEnabled || emailAuthEnabled) ? 'ri-shield-check-line text-emerald-600' : 'ri-shield-alert-line text-amber-600'" />
            <span>{{ (mfaEnabled || emailAuthEnabled) ? '2FA Protection Active' : '2FA Recommended' }}</span>
          </span>
        </div>
      </div>

      <!-- Global Messages -->
      <MessageBox
        v-if="mfaSuccess"
        :message="mfaSuccess"
        type="success"
        @close="mfaSuccess = ''"
      />
      <MessageBox
        v-if="mfaError"
        :message="mfaError"
        type="error"
        @close="mfaError = ''"
      />

      <!-- Section 1: Multi-Factor Authentication (MFA) -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs space-y-6">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ri-smartphone-line text-emerald-600 dark:text-emerald-400" />
            Two-Factor Authenticator App (TOTP)
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Use applications like Google Authenticator, Microsoft Authenticator, 1Password, or Authy to generate secure one-time login codes.
          </p>
        </div>

        <!-- MFA Status & Action -->
        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-800/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div
              class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shadow-xs shrink-0"
              :class="mfaEnabled ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-400'"
            >
              <i class="ri-shield-user-line" />
            </div>
            <div>
              <div class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>Authenticator App</span>
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase"
                  :class="mfaEnabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'"
                >
                  {{ mfaEnabled ? 'Active' : 'Disabled' }}
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ mfaEnabled ? 'TOTP code required on every CRM login.' : 'Add an extra layer of protection beyond your password.' }}
              </p>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button
              v-if="!mfaEnabled"
              type="button"
              :disabled="mfaLoading"
              class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-semibold text-xs shadow-xs disabled:opacity-50 transition cursor-pointer flex items-center gap-1.5"
              @click="openMfaSetup"
            >
              <i v-if="mfaLoading" class="ri-loader-4-line animate-spin" />
              <i v-else class="ri-qr-code-line" />
              <span>Enable Authenticator</span>
            </button>

            <button
              v-else
              type="button"
              :disabled="mfaLoading"
              class="px-4 py-2 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-900/50 font-semibold text-xs disabled:opacity-50 transition cursor-pointer flex items-center gap-1.5"
              @click="disableMfa"
            >
              <i v-if="mfaLoading" class="ri-loader-4-line animate-spin" />
              <i v-else class="ri-close-circle-line" />
              <span>Disable Authenticator</span>
            </button>
          </div>
        </div>

        <!-- MFA Setup Modal / Panel -->
        <div v-if="showMfaSetup" class="p-6 rounded-2xl border-2 border-dashed border-emerald-500/40 bg-emerald-50/30 dark:bg-emerald-950/10 space-y-6">
          <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
              <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">1</span>
              Scan QR Code with your Authenticator App
            </h3>
            <button
              type="button"
              class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer"
              @click="showMfaSetup = false"
            >
              <i class="ri-close-line text-lg" />
            </button>
          </div>

          <div class="flex flex-col md:flex-row items-center gap-6">
            <!-- QR Code display -->
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-sm shrink-0">
              <img
                v-if="qrCodeUrl"
                :src="qrCodeUrl"
                alt="Authenticator QR Code"
                class="w-48 h-48 rounded-lg"
              >
            </div>

            <!-- Secret Key manual entry -->
            <div class="space-y-4 flex-1">
              <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                Scan the QR code with your mobile Authenticator app. If you cannot scan QR codes, copy and paste the setup secret key below:
              </p>

              <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-2">
                <code class="text-xs font-mono font-bold text-emerald-700 dark:text-emerald-300 break-all select-all">
                  {{ secretKey }}
                </code>
                <button
                  type="button"
                  class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 text-[11px] font-semibold transition border border-slate-200 dark:border-slate-600 cursor-pointer shrink-0"
                  @click="copySecretKey"
                >
                  {{ copiedKey ? 'Copied!' : 'Copy Key' }}
                </button>
              </div>

              <!-- 6-digit confirmation -->
              <div class="pt-2">
                <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 flex items-center gap-1.5">
                  <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">2</span>
                  Enter the 6-Digit Code from the App to Verify:
                </label>
                <div class="flex items-center gap-3">
                  <input
                    v-model="verificationCode"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    pattern="[0-9]*"
                    placeholder="000000"
                    class="w-40 text-center tracking-[0.4em] font-mono font-bold text-lg py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                  >
                  <button
                    type="button"
                    :disabled="mfaLoading || !verificationCode"
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs disabled:opacity-50 transition cursor-pointer flex items-center gap-1.5"
                    @click="confirmMfaSetup"
                  >
                    <i v-if="mfaLoading" class="ri-loader-4-line animate-spin" />
                    <span>Verify & Activate</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <hr class="border-slate-100 dark:border-slate-800" />

        <!-- Section 2: Email Two-Factor Authentication -->
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ri-mail-shield-line text-emerald-600 dark:text-emerald-400" />
            Email Verification Code (OTP)
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Receive a 6-digit one-time passcode on your registered staff email address (<b class="text-slate-700 dark:text-slate-300">{{ currentUser?.email }}</b>) whenever you log in to the CRM Portal.
          </p>
        </div>

        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-800/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div
              class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shadow-xs shrink-0"
              :class="emailAuthEnabled ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-400'"
            >
              <i class="ri-mail-check-line" />
            </div>
            <div>
              <div class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>Email 2FA Verification</span>
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase"
                  :class="emailAuthEnabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'"
                >
                  {{ emailAuthEnabled ? 'Active' : 'Disabled' }}
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ emailAuthEnabled ? 'OTP email verification is sent on login.' : 'Use email as a secondary login verification factor.' }}
              </p>
            </div>
          </div>

          <button
            type="button"
            :disabled="mfaLoading"
            class="px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer disabled:opacity-50 flex items-center gap-1.5"
            :class="emailAuthEnabled ? 'border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs'"
            @click="toggleEmailAuth"
          >
            <i v-if="mfaLoading" class="ri-loader-4-line animate-spin" />
            <i v-else :class="emailAuthEnabled ? 'ri-toggle-line' : 'ri-toggle-fill'" />
            <span>{{ emailAuthEnabled ? 'Disable Email 2FA' : 'Enable Email 2FA' }}</span>
          </button>
        </div>
      </div>

      <!-- Section 3: Password Update -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs space-y-5">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ri-lock-password-line text-emerald-600 dark:text-emerald-400" />
            Change Account Password
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Ensure your CRM account is using a long, random password to remain secure.
          </p>
        </div>

        <MessageBox
          v-if="passwordMsg"
          :message="passwordMsg"
          type="success"
          @close="passwordMsg = ''"
        />
        <MessageBox
          v-if="passwordError"
          :message="passwordError"
          type="error"
          @close="passwordError = ''"
        />

        <form class="space-y-4 max-w-lg" @submit.prevent="updatePassword">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
              New Password
            </label>
            <input
              v-model="passwordForm.password"
              type="password"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              placeholder="Minimum 8 characters"
            >
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
              Confirm New Password
            </label>
            <input
              v-model="passwordForm.password_confirmation"
              type="password"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              placeholder="Repeat new password"
            >
          </div>

          <div class="pt-2">
            <button
              type="submit"
              :disabled="passwordLoading"
              class="px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-semibold text-xs hover:bg-slate-800 dark:hover:bg-slate-100 disabled:opacity-50 transition cursor-pointer flex items-center gap-1.5"
            >
              <i v-if="passwordLoading" class="ri-loader-4-line animate-spin" />
              <span>Update CRM Password</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </CrmLayout>
</template>
