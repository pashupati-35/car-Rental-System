<script setup lang="ts">
import { ref } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.owner || auth.value?.user)

const profileForm = ref({
  full_name: user.value?.full_name || user.value?.name || '',
  email: user.value?.email || '',
  contact_number: user.value?.contact_number || '',
  address: user.value?.address || '',
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

const updatePassword = async () => {
  passwordLoading.value = true
  passwordMsg.value = ''
  passwordError.value = ''
  try {
    const res = await axios.patch('/owner/password', passwordForm.value)
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

const initMfaSetup = async () => {
  mfaError.value = ''
  mfaSuccess.value = ''
  mfaLoading.value = true
  try {
    const res = await axios.post('/owner/mfa/generate')
    if (res.data.status === 'OK') {
      secretKey.value = res.data.secret_key
      qrCodeUrl.value = res.data.qr_code_url
      showMfaSetup.value = true
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Failed to generate MFA secret.'
  } finally {
    mfaLoading.value = false
  }
}

const activateMfa = async () => {
  mfaError.value = ''
  mfaSuccess.value = ''
  mfaLoading.value = true
  try {
    const res = await axios.post('/owner/mfa/activate', {
      secret_key: secretKey.value,
      verification_code: verificationCode.value,
    })

    if (res.data.status === 'OK') {
      mfaEnabled.value = true
      showMfaSetup.value = false
      mfaSuccess.value = 'Two-Factor Authentication is now active on your Owner account!'
    } else {
      mfaError.value = res.data.message || 'Verification failed.'
    }
  } catch (err: any) {
    mfaError.value = err.response?.data?.message || 'Invalid verification code.'
  } finally {
    mfaLoading.value = false
  }
}

const deactivateMfa = async () => {
  if (!confirm('Are you sure you want to disable Multi-Factor Authentication?')) return
  mfaLoading.value = true
  try {
    const res = await axios.post('/owner/mfa/deactivate')
    if (res.data.status === 'OK') {
      mfaEnabled.value = false
      mfaSuccess.value = 'MFA has been disabled.'
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
    <Head title="Owner Profile & Security" />

    <template #header>
      Owner Profile & Security
    </template>

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Profile Information -->
      <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h3 class="font-bold text-lg text-gray-900 dark:text-white">
          Fleet Owner Details
        </h3>
        <p class="text-xs text-gray-500 mt-0.5">
          Manage your contact information and business address
        </p>

        <div v-if="profileMsg" class="mt-4 p-3 rounded-xl bg-emerald-50 text-emerald-800 text-xs flex items-center gap-2">
          <i class="ri-checkbox-circle-fill" /> {{ profileMsg }}
        </div>
        <div v-if="profileError" class="mt-4 p-3 rounded-xl bg-rose-50 text-rose-800 text-xs flex items-center gap-2">
          <i class="ri-error-warning-fill" /> {{ profileError }}
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="updateProfile">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
              <input
                v-model="profileForm.full_name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Contact Number</label>
              <input
                v-model="profileForm.contact_number"
                type="text"
                placeholder="+1 234 567 890"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Address</label>
              <input
                v-model="profileForm.address"
                type="text"
                placeholder="123 Fleet Way"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>

          <div class="flex justify-end">
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

      <!-- Change Password -->
      <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h3 class="font-bold text-lg text-gray-900 dark:text-white">
          Change Password
        </h3>
        <p class="text-xs text-gray-500 mt-0.5">
          Ensure your owner portal account is secure
        </p>

        <div v-if="passwordMsg" class="mt-4 p-3 rounded-xl bg-emerald-50 text-emerald-800 text-xs flex items-center gap-2">
          <i class="ri-checkbox-circle-fill" /> {{ passwordMsg }}
        </div>
        <div v-if="passwordError" class="mt-4 p-3 rounded-xl bg-rose-50 text-rose-800 text-xs flex items-center gap-2">
          <i class="ri-error-warning-fill" /> {{ passwordError }}
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="updatePassword">
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
            <input
              v-model="passwordForm.current_password"
              type="password"
              required
              class="w-full max-w-md px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">New Password</label>
              <input
                v-model="passwordForm.password"
                type="password"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
              <input
                v-model="passwordForm.password_confirmation"
                type="password"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="passwordLoading"
              class="px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-black dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-semibold text-xs shadow-md disabled:opacity-50"
            >
              {{ passwordLoading ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Multi-Factor Authentication (MFA) Section -->
      <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-start justify-between">
          <div>
            <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
              <i class="ri-shield-keyhole-line text-blue-600" />
              Two-Factor Authentication (MFA)
            </h3>
            <p class="text-xs text-gray-500 mt-1 max-w-xl">
              Enable two-factor authentication to safeguard your fleet management operations.
            </p>
          </div>

          <div>
            <span
              :class="mfaEnabled ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-600'"
              class="px-3 py-1 rounded-full text-xs font-semibold"
            >
              {{ mfaEnabled ? 'MFA Enabled' : 'MFA Disabled' }}
            </span>
          </div>
        </div>

        <div
          v-if="mfaSuccess"
          class="mt-4 p-3 rounded-xl bg-emerald-50 text-emerald-800 text-xs flex items-center gap-2"
        >
          <i class="ri-checkbox-circle-fill" />
          <span>{{ mfaSuccess }}</span>
        </div>
        <div
          v-if="mfaError"
          class="mt-4 p-3 rounded-xl bg-rose-50 text-rose-800 text-xs flex items-center gap-2"
        >
          <i class="ri-error-warning-fill" />
          <span>{{ mfaError }}</span>
        </div>

        <!-- MFA Setup Box -->
        <div
          v-if="showMfaSetup"
          class="mt-6 p-6 rounded-2xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/50 space-y-4"
        >
          <h4 class="font-bold text-sm text-gray-900 dark:text-white">
            1. Scan QR Code in Authenticator App
          </h4>
          <div class="flex flex-col sm:flex-row items-center gap-6">
            <div class="p-3 bg-white rounded-xl shadow-sm border border-gray-200">
              <img
                :src="qrCodeUrl"
                class="w-44 h-44 object-contain"
                alt="MFA QR Code"
              >
            </div>
            <div class="space-y-3 text-xs flex-1">
              <p class="text-gray-600 dark:text-gray-400">
                Or enter this setup key manually into your authenticator app:
              </p>
              <div class="p-2.5 rounded-lg bg-white dark:bg-gray-800 font-mono font-bold text-blue-600 text-sm border border-gray-200 dark:border-gray-700 select-all">
                {{ secretKey }}
              </div>
              <div class="space-y-1.5 pt-2">
                <label class="block font-semibold text-gray-700 dark:text-gray-300">2. Enter 6-digit TOTP Code</label>
                <div class="flex gap-2">
                  <input
                    v-model="verificationCode"
                    type="text"
                    maxlength="6"
                    placeholder="123456"
                    class="px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 font-mono text-center tracking-widest text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                  <button
                    :disabled="mfaLoading"
                    class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors disabled:opacity-50"
                    @click="activateMfa"
                  >
                    {{ mfaLoading ? 'Verifying...' : 'Verify & Enable MFA' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex gap-3">
          <button
            v-if="!mfaEnabled && !showMfaSetup"
            :disabled="mfaLoading"
            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition-all disabled:opacity-50"
            @click="initMfaSetup"
          >
            {{ mfaLoading ? 'Generating...' : 'Enable Multi-Factor Authentication (MFA)' }}
          </button>
          <button
            v-if="mfaEnabled"
            :disabled="mfaLoading"
            class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs transition-all disabled:opacity-50"
            @click="deactivateMfa"
          >
            Disable Two-Factor Authentication
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
