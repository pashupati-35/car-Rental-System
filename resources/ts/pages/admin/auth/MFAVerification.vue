<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps<{
  email: string
  password: string
  remember?: boolean
}>()

const emit = defineEmits(['back'])

const verificationCode = ref('')
const loading = ref(false)
const errorMessage = ref('')

const verifyCode = async () => {
  if (!verificationCode.value || verificationCode.value.length < 6) {
    errorMessage.value = 'Please enter the full 6-digit verification code.'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const res = await axios.post('/admin/mfa/verify-code', {
      email: props.email,
      password: props.password,
      verification_code: verificationCode.value,
      remember: props.remember,
    })

    if (res.data.status === 'OK') {
      router.visit(res.data.redirect || '/admin/dashboard')
    } else {
      errorMessage.value = res.data.errors || 'Verification failed.'
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.errors || err.response?.data?.message || 'Invalid 2FA code. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-5">
    <div class="text-center mb-4">
      <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl border border-indigo-100 dark:border-indigo-900/50 shadow-inner">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <h3 class="font-bold text-lg text-gray-900 dark:text-white">
        Admin Security Check
      </h3>
      <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
        Two-Factor Authentication is active for this admin account. Enter the 6-digit code from Google Authenticator.
      </p>
    </div>

    <div
      v-if="errorMessage"
      class="p-3 rounded-xl bg-red-50 text-red-700 text-xs flex items-center gap-2 border border-red-200"
    >
      <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span>{{ errorMessage }}</span>
    </div>

    <div>
      <input
        v-model="verificationCode"
        type="text"
        maxlength="6"
        placeholder="000000"
        autofocus
        class="w-full text-center tracking-[0.4em] text-2xl font-mono py-3.5 px-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm"
        @keyup.enter="verifyCode"
      />
    </div>

    <button
      :disabled="loading"
      class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-lg shadow-indigo-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2"
      @click="verifyCode"
    >
      <span v-if="loading">Verifying Security Code...</span>
      <span v-else>Verify & Enter Portal</span>
    </button>

    <button
      type="button"
      class="w-full py-2 text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors flex items-center justify-center gap-1"
      @click="emit('back')"
    >
      &larr; Back to login
    </button>
  </div>
</template>
