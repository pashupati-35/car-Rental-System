<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps<{
  guard: string
  email: string
  password: string
}>()

const emit = defineEmits(['back'])

const verificationCode = ref('')
const loading = ref(false)
const errorMessage = ref('')

const verifyCode = async () => {
  if (!verificationCode.value || verificationCode.value.length < 6) {
    errorMessage.value = 'Please enter the full 6-digit code.'

    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const res = await axios.post('/api/auth/mfa/verify', {
      guard: props.guard,
      email: props.email,
      password: props.password,
      verification_code: verificationCode.value,
    })

    if (res.data.status === 'OK') {
      const redirectUrl = props.guard === 'admin' 
        ? '/admin/dashboard' 
        : props.guard === 'owner' 
          ? '/owner/dashboard' 
          : '/customer/dashboard'

      router.visit(redirectUrl)
    } else {
      errorMessage.value = res.data.errors || 'Verification failed.'
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || err.response?.data?.errors || 'Invalid verification code.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <div class="text-center mb-4">
      <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
        <i class="ri-shield-keyhole-line" />
      </div>
      <h3 class="font-bold text-lg text-gray-900 dark:text-white">
        Two-Factor Authentication
      </h3>
      <p class="text-xs text-gray-500 mt-1">
        Enter the 6-digit code from your Authenticator app
      </p>
    </div>

    <div
      v-if="errorMessage"
      class="p-3 rounded-xl bg-red-50 text-red-700 text-xs flex items-center gap-2"
    >
      <i class="ri-error-warning-fill" />
      <span>{{ errorMessage }}</span>
    </div>

    <div>
      <input
        v-model="verificationCode"
        type="text"
        maxlength="6"
        placeholder="123456"
        class="w-full text-center tracking-widest text-2xl font-mono py-3 px-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        @keyup.enter="verifyCode"
      >
    </div>

    <button
      :disabled="loading"
      class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 disabled:opacity-50 transition-all flex items-center justify-center gap-2"
      @click="verifyCode"
    >
      <span v-if="loading">Verifying...</span>
      <span v-else>Verify & Continue</span>
    </button>

    <button
      type="button"
      class="w-full py-2 text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
      @click="emit('back')"
    >
      &larr; Back to login
    </button>
  </div>
</template>
