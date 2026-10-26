<script setup lang="ts">
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import MessageBox from '@/components/MessageBox.vue'

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const registerForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  registerForm.post('/admin/register', {
    onFinish: () => registerForm.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <AuthLayout>
    <Head title="Admin Registration" />

    <template #title>
      <div class="flex items-center justify-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-indigo-500" />
        Admin Account Registration
      </div>
    </template>
    <template #subtitle>
      Create a platform administrator credential to manage fleet, bookings & settings
    </template>

    <div class="space-y-4">
      <MessageBox
        v-if="registerForm.hasErrors"
        :message="Object.values(registerForm.errors)[0] as string"
        type="error"
        class="mb-4"
      />

      <form
        class="space-y-4"
        @submit.prevent="submit"
      >
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
            Full Name *
          </label>
          <input
            v-model="registerForm.name"
            type="text"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-sm"
            placeholder="John Doe"
          >
          <span
            v-if="registerForm.errors.name"
            class="text-xs text-red-500 mt-1 block"
          >{{ registerForm.errors.name }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
            Email Address *
          </label>
          <input
            v-model="registerForm.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-sm"
            placeholder="admin@carrental.com"
          >
          <span
            v-if="registerForm.errors.email"
            class="text-xs text-red-500 mt-1 block"
          >{{ registerForm.errors.email }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
            Password *
          </label>
          <div class="relative">
            <input
              v-model="registerForm.password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-sm pe-10"
              placeholder="••••••••"
            >
            <button
              type="button"
              class="absolute inset-y-0 right-0 pe-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
              @click="showPassword = !showPassword"
            >
              <i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'" />
            </button>
          </div>
          <span
            v-if="registerForm.errors.password"
            class="text-xs text-red-500 mt-1 block"
          >{{ registerForm.errors.password }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
            Confirm Password *
          </label>
          <div class="relative">
            <input
              v-model="registerForm.password_confirmation"
              :type="showConfirmPassword ? 'text' : 'password'"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-sm pe-10"
              placeholder="••••••••"
            >
            <button
              type="button"
              class="absolute inset-y-0 right-0 pe-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
              @click="showConfirmPassword = !showConfirmPassword"
            >
              <i :class="showConfirmPassword ? 'ri-eye-off-line' : 'ri-eye-line'" />
            </button>
          </div>
          <span
            v-if="registerForm.errors.password_confirmation"
            class="text-xs text-red-500 mt-1 block"
          >{{ registerForm.errors.password_confirmation }}</span>
        </div>

        <button
          type="submit"
          :disabled="registerForm.processing"
          class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold text-sm shadow-md shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
        >
          <i
            v-if="registerForm.processing"
            class="ri-loader-4-line animate-spin"
          />
          <span>{{ registerForm.processing ? 'Creating Account...' : 'Create Account' }}</span>
        </button>
      </form>

      <div class="text-center pt-2">
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Already have an account?
          <Link
            href="/admin/login"
            class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline"
          >
            Sign In
          </Link>
        </p>
      </div>
    </div>
  </AuthLayout>
</template>
