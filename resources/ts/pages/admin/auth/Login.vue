<script setup lang="ts">
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import MFAVerification from './MFAVerification.vue'
import axios from 'axios'

defineProps<{
  status?: string
}>()

const isMfaStep = ref(false)
const errorMessage = ref('')

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const handleLogin = async () => {
  errorMessage.value = ''
  if (!form.email || !form.password) {
    errorMessage.value = 'Please enter both email and password.'
    
    return
  }

  try {
    // API check verification for MFA
    const res = await axios.post('/admin/mfa/check-verification', {
      email: form.email,
      password: form.password,
    })

    if (res.data.status === 'OK' && res.data.data?.is_mfa_enabled) {
      isMfaStep.value = true
    } else {
      // Normal direct login
      form.post('/admin/login', {
        onFinish: () => form.reset('password'),
        onError: errs => {
          errorMessage.value = Object.values(errs)[0] as string || 'Login failed.'
        },
      })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.errors || err.response?.data?.message || 'Invalid credentials or connection error.'
  }
}
</script>

<template>
  <AuthLayout>
    <Head title="Admin Portal Login" />

    <template #title>
      <div class="flex items-center justify-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse" />
        Admin Portal
      </div>
    </template>
    <template #subtitle>
      System Administration & Super Control Dashboard
    </template>

    <div v-if="!isMfaStep">
      <div
        v-if="status"
        class="mb-4 p-3 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-200"
      >
        {{ status }}
      </div>

      <div
        v-if="errorMessage"
        class="mb-4 p-3 rounded-xl bg-red-50 text-red-700 text-xs font-medium border border-red-200 flex items-center gap-2"
      >
        <svg
          class="w-4 h-4 shrink-0 text-red-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        ><path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
        /></svg>
        <span>{{ errorMessage }}</span>
      </div>

      <form
        class="space-y-4"
        @submit.prevent="handleLogin"
      >
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Admin Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-sm"
            placeholder="admin@carrental.com"
          >
          <span
            v-if="form.errors.email"
            class="text-xs text-red-500 mt-1 block"
          >{{ form.errors.email }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Master Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-sm"
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
              class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            >
            Keep me signed in
          </label>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold text-sm shadow-lg shadow-indigo-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2 mt-2"
        >
          <span v-if="form.processing">Authenticating...</span>
          <span v-else>Access Admin Dashboard</span>
        </button>
      </form>

      <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 flex justify-between text-xs text-gray-500">
        <Link
          href="/owner/login"
          class="hover:text-indigo-600 transition-colors"
        >
          Owner Login &rarr;
        </Link>
        <Link
          href="/customer/login"
          class="hover:text-indigo-600 transition-colors"
        >
          Customer Login &rarr;
        </Link>
      </div>
    </div>

    <div v-else>
      <MFAVerification
        :email="form.email"
        :password="form.password"
        :remember="form.remember"
        @back="isMfaStep = false"
      />
    </div>
  </AuthLayout>
</template>
