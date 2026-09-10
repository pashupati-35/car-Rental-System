<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'

const props = defineProps<{
  token: string
  email?: string
}>()

const form = useForm({
  token: props.token,
  email: props.email || '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post('/customer/reset-password', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <AuthLayout>
    <Head title="Set New Password" />

    <template #title>
      <div class="flex items-center justify-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-600"></span>
        Set New Password
      </div>
    </template>
    <template #subtitle>
      Enter your new password to restore access to your customer account
    </template>

    <div class="space-y-4">
      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all"
            placeholder="you@example.com"
          />
          <span v-if="form.errors.email" class="text-xs text-red-500 mt-1 block">{{ form.errors.email }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all"
            placeholder="••••••••••••"
          />
          <span v-if="form.errors.password" class="text-xs text-red-500 mt-1 block">{{ form.errors.password }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Confirm New Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all"
            placeholder="••••••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-lg shadow-blue-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2"
        >
          <span v-if="form.processing">Updating Password...</span>
          <span v-else>Update & Sign In</span>
        </button>

        <div class="text-center pt-2">
          <Link href="/customer/login" class="text-xs text-gray-500 hover:text-blue-600 dark:hover:text-gray-300 font-semibold transition-colors">
            &larr; Return to Sign In
          </Link>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>
