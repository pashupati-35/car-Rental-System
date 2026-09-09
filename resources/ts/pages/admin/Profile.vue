<script setup lang="ts">
import { ref } from 'vue'
import { Head, usePage, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.user || auth.value?.admin || auth.value?.owner || auth.value?.customer)

const mfaEnabled = ref(Boolean(user.value?.is_mfa_enabled))
const qrCodeUrl = ref('')
const secretKey = ref('')
const verificationCode = ref('')
const showMfaSetup = ref(false)
const mfaError = ref('')
const mfaSuccess = ref('')

const form = useForm({
  name: user.value?.name || '',
  email: user.value?.email || '',
  current_password: '',
  password: '',
  password_confirmation: '',
})

const initMfaSetup = async () => {
  mfaError.value = ''
  mfaSuccess.value = ''
  try {
    const res = await axios.post('/api/auth/mfa/generate')
    if (res.data.status === 'OK') {
      secretKey.value = res.data.secret_key
      qrCodeUrl.value = res.data.qr_code_url
      showMfaSetup.value = true
    }
  } catch (err: any) {
    mfaError.value = 'Failed to generate MFA secret.'
  }
}

const activateMfa = async () => {
  mfaError.value = ''
  mfaSuccess.value = ''
  try {
    const res = await axios.post('/api/auth/mfa/activate', {
      secret_key: secretKey.value,
      verification_code: verificationCode.value,
    })
    if (res.data.status === 'OK') {
      mfaEnabled.value = true
      showMfaSetup.value = false
      mfaSuccess.value = 'MFA Two-Factor Authentication is now active on your account!'
    } else {
      mfaError.value = res.data.message || 'Verification failed.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Invalid verification code.'
  }
}

const deactivateMfa = async () => {
  if (!confirm('Are you sure you want to disable Multi-Factor Authentication?')) return
  try {
    const res = await axios.post('/api/auth/mfa/deactivate')
    if (res.data.status === 'OK') {
      mfaEnabled.value = false
      mfaSuccess.value = 'MFA has been disabled.'
    }
  } catch (err) {
    mfaError.value = 'Failed to disable MFA.'
  }
}
</script>

<template>
  <AppLayout>
    <Head title="My Profile & Security" />

    <template #header>
      Account Profile & MFA Security
    </template>

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Profile Information -->
      <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h3 class="font-bold text-lg text-gray-900 dark:text-white">Profile Details</h3>
        <p class="text-xs text-gray-500 mt-0.5">Manage your personal and contact details</p>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
            <p class="font-medium text-gray-900 dark:text-white">{{ user?.name }}</p>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
            <p class="font-medium text-gray-900 dark:text-white">{{ user?.email }}</p>
          </div>
        </div>
      </div>

      <!-- Multi-Factor Authentication (MFA) Section -->
      <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-start justify-between">
          <div>
            <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
              <i class="ri-shield-keyhole-line text-blue-600"></i>
              Two-Factor Authentication (MFA)
            </h3>
            <p class="text-xs text-gray-500 mt-1 max-w-xl">
              Add an extra layer of security to your account. When enabled, you will be prompted for a 6-digit TOTP code during sign-in using apps like Google Authenticator or Microsoft Authenticator.
            </p>
          </div>

          <div>
            <span :class="mfaEnabled ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-full text-xs font-semibold">
              {{ mfaEnabled ? 'Enabled' : 'Disabled' }}
            </span>
          </div>
        </div>

        <div v-if="mfaSuccess" class="mt-4 p-3 rounded-xl bg-emerald-50 text-emerald-800 text-xs flex items-center gap-2">
          <i class="ri-checkbox-circle-fill"></i>
          <span>{{ mfaSuccess }}</span>
        </div>
        <div v-if="mfaError" class="mt-4 p-3 rounded-xl bg-rose-50 text-rose-800 text-xs flex items-center gap-2">
          <i class="ri-error-warning-fill"></i>
          <span>{{ mfaError }}</span>
        </div>

        <!-- MFA Setup Modal / Inline Box -->
        <div v-if="showMfaSetup" class="mt-6 p-6 rounded-2xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/50 space-y-4">
          <h4 class="font-bold text-sm text-gray-900 dark:text-white">Scan QR Code with your Authenticator App</h4>
          <div class="flex flex-col sm:flex-row items-center gap-6">
            <div class="p-3 bg-white rounded-xl shadow-sm border border-gray-200">
              <img :src="qrCodeUrl" class="w-40 h-40 object-contain" alt="MFA QR Code" />
            </div>
            <div class="space-y-3 text-xs">
              <p class="text-gray-600 dark:text-gray-400">If you cannot scan the QR code, manually enter this secret key:</p>
              <div class="p-2.5 rounded-lg bg-white dark:bg-gray-800 font-mono font-bold text-blue-600 text-sm border border-gray-200 dark:border-gray-700 select-all">
                {{ secretKey }}
              </div>
              <div class="space-y-1 pt-1">
                <label class="block font-semibold text-gray-700 dark:text-gray-300">Enter Verification Code</label>
                <div class="flex gap-2">
                  <input
                    v-model="verificationCode"
                    type="text"
                    maxlength="6"
                    placeholder="123456"
                    class="px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 font-mono text-center tracking-widest text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                  <button
                    @click="activateMfa"
                    class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors"
                  >
                    Confirm & Enable
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex gap-3">
          <button
            v-if="!mfaEnabled && !showMfaSetup"
            @click="initMfaSetup"
            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition-all"
          >
            Setup Two-Factor Authentication
          </button>
          <button
            v-if="mfaEnabled"
            @click="deactivateMfa"
            class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs transition-all"
          >
            Disable Two-Factor Authentication
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
