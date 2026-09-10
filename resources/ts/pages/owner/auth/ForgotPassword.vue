<script setup lang="ts">
import { useForm, Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'

defineProps<{
  status?: string
}>()

const form = useForm({
  email: '',
})

const submit = () => {
  form.post('/owner/forgot-password')
}
</script>

<template>
  <AuthLayout>
    <Head title="Fleet Owner Password Recovery" />

    <template #title>
      <div class="flex items-center justify-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
        Owner Password Reset
      </div>
    </template>
    <template #subtitle>
      Enter your fleet owner account email to receive a password reset link
    </template>

    <div class="space-y-4">
      <div v-if="status" class="p-3.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
        {{ status }}
      </div>

      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Fleet Owner Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
            placeholder="owner@fleet.com"
          />
          <span v-if="form.errors.email" class="text-xs text-red-500 mt-1 block">{{ form.errors.email }}</span>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2"
        >
          <span v-if="form.processing">Sending Reset Link...</span>
          <span v-else>Email Reset Link</span>
        </button>

        <div class="text-center pt-2">
          <Link href="/owner/login" class="text-xs text-gray-500 hover:text-emerald-600 dark:hover:text-gray-300 font-semibold transition-colors">
            &larr; Back to Owner Portal Sign In
          </Link>
        </div>
      </form>
    </div>
  </AuthLayout>
</template>
