<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
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
const showPassword = ref(false)
const redirectPortal = ref<{ url: string; label: string } | null>(null)

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

onMounted(() => {
  if (typeof window !== 'undefined') {
    const params = new URLSearchParams(window.location.search)
    const emailParam = params.get('email')
    if (emailParam) {
      form.email = emailParam
    }
  }
})

const isProcessing = computed(() => form.processing || isChecking.value)

const handleLogin = async () => {
  errorMessage.value = ''
  redirectPortal.value = null
  if (!form.email || !form.password) {
    errorMessage.value = 'Please enter both email and password.'

    return
  }

  isChecking.value = true
  try {
    const res = await axios.post('/owner/mfa/check-verification', {
      email: form.email,
      password: form.password,
    })

    if (res.data.status === 'OK' && (res.data.data?.is_mfa_enabled || res.data.data?.is_email_authentication_enabled)) {
      authType.value = res.data.data?.auth_type || (res.data.data?.is_mfa_enabled ? 'totp' : 'email')
      isMfaStep.value = true
      isChecking.value = false
    } else {
      form.post('/owner/login', {
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
    const data = err.response?.data
    errorMessage.value = data?.errors || data?.message || 'Invalid credentials or connection error.'
    if (data?.redirect_portal) {
      redirectPortal.value = {
        url: data.redirect_portal,
        label: data.portal_name || 'Switch Portal',
      }
    }
    isChecking.value = false
  }
}
</script>

<template>
  <AuthLayout>
    <Head title="Car Owner Portal Sign In" />

    <template #title>
      <div class="flex items-center justify-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500" />
        Owner Portal
      </div>
    </template>
    <template #subtitle>
      Manage your fleet, track driver rosters, and view rental earnings
    </template>

    <div v-if="!isMfaStep">
      <MessageBox
        v-if="status"
        :message="status"
        type="success"
        class="mb-4"
      />

      <div
        v-if="errorMessage"
        class="mb-4 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs sm:text-sm flex flex-col gap-2.5 animate-in fade-in duration-200"
      >
        <div class="flex items-start justify-between gap-2">
          <div class="flex items-start gap-2">
            <i class="ri-error-warning-fill text-rose-500 text-base shrink-0 mt-0.5" />
            <span class="font-medium leading-snug">{{ errorMessage }}</span>
          </div>
          <button
            type="button"
            class="text-rose-400 hover:text-rose-700 dark:hover:text-rose-100 p-0.5 cursor-pointer"
            aria-label="Dismiss message"
            @click="errorMessage = ''; redirectPortal = null"
          >
            <i class="ri-close-line text-lg" />
          </button>
        </div>

        <div
          v-if="redirectPortal"
          class="pt-1"
        >
          <a
            :href="redirectPortal.url"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors"
          >
            <i class="ri-login-box-line" />
            <span>Go to {{ redirectPortal.label }}</span>
            <i class="ri-arrow-right-line" />
          </a>
        </div>
      </div>

      <form
        class="space-y-4"
        @submit.prevent="handleLogin"
      >
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Owner Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all text-sm"
            placeholder="owner@fleet.com"
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
              href="/owner/forgot-password"
              class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline"
            >
              Forgot password?
            </Link>
          </div>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full px-4 py-2.5 pe-11 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all text-sm"
              placeholder="••••••••••••"
            >
            <button
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg focus:outline-none cursor-pointer transition-colors"
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
          <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 cursor-pointer">
            <input
              v-model="form.remember"
              type="checkbox"
              class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
            >
            Remember me
          </label>
        </div>

        <button
          type="submit"
          :disabled="isProcessing"
          class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold text-sm shadow-lg shadow-emerald-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2 mt-2 cursor-pointer"
        >
          <i
            v-if="isProcessing"
            class="ri-loader-4-line animate-spin text-lg"
          />
          <span v-if="isProcessing">Signing in...</span>
          <span v-else>Sign In to Owner Portal</span>
        </button>

        <div class="text-center pt-3">
          <p class="text-xs text-gray-500">
            Want to list your cars with us?
            <Link
              href="/owner/register"
              class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline ms-1"
            >
              Register as Owner
            </Link>
          </p>
        </div>
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
