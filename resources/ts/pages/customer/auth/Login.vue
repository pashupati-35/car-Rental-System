<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import MessageBox from '@/components/MessageBox.vue'
import MFAVerification from './MFAVerification.vue'
import axios from 'axios'

defineProps<{
  status?: string
}>()

const isMfaStep = ref(false)
const authType = ref<'totp' | 'email'>('totp')
const errorMessage = ref('')
const isChecking = ref(false)

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const isProcessing = computed(() => form.processing || isChecking.value)

const handleLogin = async () => {
  errorMessage.value = ''
  if (!form.email || !form.password) {
    errorMessage.value = 'Please enter both email and password.'

    return
  }

  isChecking.value = true
  try {
    const res = await axios.post('/customer/mfa/check-verification', {
      email: form.email,
      password: form.password,
    })

    if (res.data.status === 'OK' && (res.data.data?.is_mfa_enabled || res.data.data?.is_email_authentication_enabled)) {
      authType.value = res.data.data?.auth_type || (res.data.data?.is_mfa_enabled ? 'totp' : 'email')
      isMfaStep.value = true
      isChecking.value = false
    } else {
      form.post('/customer/login', {
        onFinish: () => {
          form.reset('password')
          isChecking.value = false
        },
        onError: errs => {
          errorMessage.value = (Object.values(errs)[0] as string) || 'Login failed.'
          isChecking.value = false
        },
      })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.errors || err.response?.data?.message || 'Invalid credentials or connection error.'
    isChecking.value = false
  }
}
</script>

<template>
  <AuthLayout>
    <Head title="Customer Sign In" />

    <template #title>
      <div class="flex items-center justify-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-600" />
        Customer Sign In
      </div>
    </template>
    <template #subtitle>
      Access your car bookings, rental calendar, and invoices
    </template>

    <div v-if="!isMfaStep">
      <MessageBox
        v-if="status"
        :message="status"
        type="success"
        class="mb-4"
      />

      <MessageBox
        v-if="errorMessage"
        :message="errorMessage"
        type="error"
        class="mb-4"
        @close="errorMessage = ''"
      />

      <form
        class="space-y-4"
        @submit.prevent="handleLogin"
      >
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all text-sm"
            placeholder="you@example.com"
          >
          <span
            v-if="form.errors.email"
            class="text-xs text-red-500 mt-1 block"
          >{{ form.errors.email }}</span>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">Password</label>
            <Link
              href="/customer/forgot-password"
              class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
            >
              Forgot password?
            </Link>
          </div>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all text-sm"
            placeholder="••••••••••••"
          >
          <span
            v-if="form.errors.password"
            class="text-xs text-red-500 mt-1 block"
          >{{ form.errors.password }}</span>
        </div>

        <div class="flex items-center justify-between text-sm pt-1">
          <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 cursor-pointer">
            <input
              v-model="form.remember"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            >
            Remember me
          </label>
        </div>

        <button
          type="submit"
          :disabled="isProcessing"
          class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm shadow-lg shadow-blue-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2 mt-2 cursor-pointer"
        >
          <i
            v-if="isProcessing"
            class="ri-loader-4-line animate-spin text-lg"
          />
          <span v-if="isProcessing">Signing in...</span>
          <span v-else>Sign In as Customer</span>
        </button>

        <div class="text-center pt-3">
          <p class="text-xs text-gray-500">
            Don't have an account yet?
            <Link
              href="/customer/register"
              class="text-blue-600 dark:text-blue-400 font-semibold hover:underline ms-1"
            >
              Create free account
            </Link>
          </p>
        </div>
      </form>

      <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-xs text-gray-500">
        <span>Are you a fleet car owner?</span>
        <Link
          href="/owner/login"
          class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline"
        >
          Fleet Owner Portal &rarr;
        </Link>
      </div>
    </div>

    <div v-else>
      <MFAVerification
        :email="form.email"
        :password="form.password"
        :remember="form.remember"
        :auth-type="authType"
        @back="isMfaStep = false"
      />
    </div>
  </AuthLayout>
</template>
