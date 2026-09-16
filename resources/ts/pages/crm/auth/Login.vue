<script setup lang="ts">
import { ref } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
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
const showPassword = ref(false)

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
    // API check verification for MFA or Email Auth on CRM portal
    const res = await axios.post('/crm/mfa/check-verification', {
      email: form.email,
      password: form.password,
    })

    if (res.data.status === 'OK' && (res.data.data?.is_mfa_enabled || res.data.data?.is_email_authentication_enabled)) {
      authType.value = res.data.data?.is_mfa_enabled ? 'totp' : 'email'
      isMfaStep.value = true
    } else {
      // Normal direct CRM login
      form.post('/crm/login', {
        onFinish: () => form.reset('password'),
        onError: errs => {
          errorMessage.value = Object.values(errs)[0] as string || 'CRM Login failed.'
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
    <Head title="CRM Enterprise Portal Login" />

    <template #title>
      <div class="flex items-center justify-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" />
        CRM Enterprise Portal
      </div>
    </template>
    <template #subtitle>
      Customer Relationship & Fleet Intelligence Operations
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
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
            Staff / Manager Email
          </label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all text-sm"
            placeholder="crm@crmcarrental.com"
          >
          <span
            v-if="form.errors.email"
            class="text-xs text-red-500 mt-1 block"
          >{{ form.errors.email }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
            Password
          </label>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full px-4 py-2.5 pe-11 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all text-sm"
              placeholder="••••••••••••"
            >
            <button
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg focus:outline-none cursor-pointer transition-colors"
              :title="showPassword ? 'Hide password' : 'Show password'"
              @click="showPassword = !showPassword"
            >
              <i
                :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'"
                class="text-lg leading-none block"
              />
            </button>
          </div>
          <span
            v-if="form.errors.password"
            class="text-xs text-red-500 mt-1 block"
          >{{ form.errors.password }}</span>
        </div>

        <div class="flex items-center justify-between text-sm pt-1">
          <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
            <input
              v-model="form.remember"
              type="checkbox"
              class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
            >
            Keep me signed in to CRM
          </label>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-semibold text-sm shadow-lg shadow-emerald-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2 mt-2 cursor-pointer"
        >
          <span v-if="form.processing">Authenticating...</span>
          <span v-else class="flex items-center gap-1.5">
            <span>Enter CRM Operations</span>
            <i class="ri-arrow-right-line" />
          </span>
        </button>
      </form>
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
