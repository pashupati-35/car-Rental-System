<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = withDefaults(
  defineProps<{
    email: string
    password: string
    remember?: boolean
    authType?: 'totp' | 'email'
  }>(),
  {
    remember: false,
    authType: 'totp',
  },
)

const emit = defineEmits<{
  (e: 'back'): void
}>()

const verificationCode = ref('')
const loading = ref(false)
const resendLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const verifyCode = async () => {
  if (!verificationCode.value || verificationCode.value.length < 6) {
    errorMessage.value = 'Please enter the full 6-digit verification code.'

    return
  }

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

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
    errorMessage.value = err.response?.data?.errors || err.response?.data?.message || 'Invalid verification code. Please try again.'
  } finally {
    loading.value = false
  }
}

const resendCode = async () => {
  resendLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''
  try {
    const res = await axios.post('/admin/mfa/resend-code', {
      email: props.email,
      password: props.password,
    })

    if (res.data?.status === 'OK') {
      successMessage.value = res.data.message || 'A new 6-digit verification code has been sent to your email.'
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.errors || err.response?.data?.message || 'Failed to resend code.'
  } finally {
    resendLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-5">
    <div class="text-center mb-4">
      <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl border border-indigo-100 dark:border-indigo-900/50 shadow-inner">
        <i
          v-if="authType === 'email'"
          class="ri-mail-check-line text-2xl"
        />
        <i
          v-else
          class="ri-shield-keyhole-line text-2xl"
        />
      </div>
      <h3 class="font-bold text-lg text-gray-900 dark:text-white">
        {{ authType === 'email' ? 'Email Security Verification' : 'Admin Security Check' }}
      </h3>
      <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
        <span v-if="authType === 'email'">
          A 6-digit security code has been sent to <b class="text-indigo-600 dark:text-indigo-400">{{ email }}</b>.
        </span>
        <span v-else>
          Two-Factor Authentication is active. Enter the 6-digit code from your Authenticator app.
        </span>
      </p>
    </div>

    <!-- Success Feedback Alert -->
    <div
      v-if="successMessage"
      class="p-3 rounded-xl bg-emerald-50 text-emerald-700 text-xs flex items-center gap-2 border border-emerald-200"
    >
      <i class="ri-checkbox-circle-fill text-base text-emerald-500 shrink-0" />
      <span>{{ successMessage }}</span>
    </div>

    <!-- Error Alert -->
    <div
      v-if="errorMessage"
      class="p-3 rounded-xl bg-red-50 text-red-700 text-xs flex items-center gap-2 border border-red-200"
    >
      <i class="ri-error-warning-fill text-base text-red-500 shrink-0" />
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
      >
    </div>

    <button
      :disabled="loading"
      class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-lg shadow-indigo-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2 cursor-pointer"
      @click="verifyCode"
    >
      <i
        v-if="loading"
        class="ri-loader-4-line animate-spin text-sm"
      />
      <i
        v-else
        class="ri-login-box-line text-sm"
      />
      <span>{{ loading ? 'Verifying Security Code...' : 'Verify & Enter Portal' }}</span>
    </button>

    <!-- Resend Email Code Button if email auth -->
    <div
      v-if="authType === 'email'"
      class="text-center pt-1"
    >
      <button
        type="button"
        :disabled="resendLoading"
        class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline disabled:opacity-50 cursor-pointer"
        @click="resendCode"
      >
        {{ resendLoading ? 'Sending new code...' : 'Didn’t receive code? Resend Code' }}
      </button>
    </div>

    <button
      type="button"
      class="w-full py-2 text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors flex items-center justify-center gap-1 cursor-pointer"
      @click="emit('back')"
    >
      &larr; Back to login
    </button>
  </div>
</template>
