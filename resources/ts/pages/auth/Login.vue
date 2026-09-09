<script setup lang="ts">
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import MultiAuthenticateVerification from './MultiAuthenticateVerification.vue'
import axios from 'axios'

const props = defineProps<{
  guard?: string
  status?: string
}>()

const activeGuard = ref(props.guard || 'admin')
const isMfaStep = ref(false)
const mfaUser = ref<any>(null)

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  // Check if MFA is enabled
  axios.post('/api/auth/check-verification', {
    email: form.email,
    password: form.password,
    guard: activeGuard.value,
  }).then(res => {
    if (res.data.status === 'OK' && res.data.data?.is_mfa_enabled) {
      mfaUser.value = res.data.data
      isMfaStep.value = true
    } else {
      performLogin()
    }
  }).catch(() => {
    performLogin()
  })
}

const performLogin = () => {
  const loginUrl = activeGuard.value === 'admin' 
    ? '/admin/login' 
    : activeGuard.value === 'owner' 
      ? '/owner/login' 
      : '/customer/login'

  form.post(loginUrl, {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <AuthLayout>
    <Head title="Sign In" />

    <template #title>
      Sign In
    </template>
    <template #subtitle>
      Select your portal and enter your credentials
    </template>

    <div v-if="!isMfaStep">
      <!-- Role Tabs -->
      <div class="flex rounded-xl bg-gray-100 dark:bg-gray-800 p-1 mb-6">
        <button
          type="button"
          @click="activeGuard = 'admin'"
          :class="activeGuard === 'admin' ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
          class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all"
        >
          Admin
        </button>
        <button
          type="button"
          @click="activeGuard = 'owner'"
          :class="activeGuard === 'owner' ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
          class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all"
        >
          Car Owner
        </button>
        <button
          type="button"
          @click="activeGuard = 'customer'"
          :class="activeGuard === 'customer' ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-sm' : 'text-gray-600 dark:text-gray-400'"
          class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all"
        >
          Customer
        </button>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all text-sm"
            placeholder="you@example.com"
          />
          <span v-if="form.errors.email" class="text-xs text-red-500 mt-1 block">{{ form.errors.email }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all text-sm"
            placeholder="••••••••"
          />
          <span v-if="form.errors.password" class="text-xs text-red-500 mt-1 block">{{ form.errors.password }}</span>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
            Remember me
          </label>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 disabled:opacity-50 transition-all flex items-center justify-center gap-2"
        >
          <span v-if="form.processing">Signing in...</span>
          <span v-else>Sign In to {{ activeGuard.toUpperCase() }}</span>
        </button>

        <div v-if="activeGuard !== 'admin'" class="text-center pt-2">
          <p class="text-xs text-gray-500">
            Don't have an account?
            <Link :href="activeGuard === 'owner' ? '/owner/register' : '/customer/register'" class="text-blue-600 hover:underline font-semibold ml-1">
              Create one
            </Link>
          </p>
        </div>
      </form>
    </div>

    <div v-else>
      <MultiAuthenticateVerification
        :guard="activeGuard"
        :email="form.email"
        :password="form.password"
        @back="isMfaStep = false"
      />
    </div>
  </AuthLayout>
</template>
