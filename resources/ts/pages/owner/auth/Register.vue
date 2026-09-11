<script setup lang="ts">
import { ref } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'

const form = useForm({
  full_name: '',
  email: '',
  contact_number: '',
  address: '',
  gender: 'male',
  password: '',
  password_confirmation: '',
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const submit = () => {
  form.post('/owner/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <AuthLayout>
    <Head title="Register as Fleet Car Owner" />

    <template #title>
      <div class="flex items-center justify-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500" />
        Fleet Owner Registration
      </div>
    </template>
    <template #subtitle>
      Join our platform as a verified car owner to monetize your vehicles and driver rosters
    </template>

    <div class="space-y-4">
      <form
        class="space-y-3.5"
        @submit.prevent="submit"
      >
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Owner / Company Name *</label>
          <input
            v-model="form.full_name"
            type="text"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
            placeholder="e.g. Apex Travel & Car Rentals"
          >
          <span
            v-if="form.errors.full_name"
            class="text-xs text-red-500 mt-1 block"
          >{{ form.errors.full_name }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Owner Email *</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
              placeholder="owner@fleet.com"
            >
            <span
              v-if="form.errors.email"
              class="text-xs text-red-500 mt-1 block"
            >{{ form.errors.email }}</span>
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Contact Phone *</label>
            <input
              v-model="form.contact_number"
              type="text"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all font-mono"
              placeholder="+977 9841000000"
            >
            <span
              v-if="form.errors.contact_number"
              class="text-xs text-red-500 mt-1 block"
            >{{ form.errors.contact_number }}</span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Business Address *</label>
            <input
              v-model="form.address"
              type="text"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
              placeholder="Kathmandu Hub, Nepal"
            >
            <span
              v-if="form.errors.address"
              class="text-xs text-red-500 mt-1 block"
            >{{ form.errors.address }}</span>
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Gender / Type</label>
            <select
              v-model="form.gender"
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
            >
              <option value="male">
                Male
              </option>
              <option value="female">
                Female
              </option>
              <option value="other">
                Company / Organization
              </option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Password *</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="w-full px-4 py-2.5 pe-11 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
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
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Confirm Password *</label>
            <div class="relative">
              <input
                v-model="form.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                class="w-full px-4 py-2.5 pe-11 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
                placeholder="••••••••••••"
              >
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg focus:outline-none cursor-pointer transition-colors"
                :title="showConfirmPassword ? 'Hide password' : 'Show password'"
                @click="showConfirmPassword = !showConfirmPassword"
              >
                <i
                  :class="showConfirmPassword ? 'ri-eye-off-line' : 'ri-eye-line'"
                  class="text-lg leading-none block"
                />
              </button>
            </div>
          </div>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 disabled:opacity-50 transition-all flex items-center justify-center gap-2 mt-2 cursor-pointer"
        >
          <i
            v-if="form.processing"
            class="ri-loader-4-line animate-spin text-lg"
          />
          <span v-if="form.processing">Registering Fleet Partner...</span>
          <span v-else>Register as Fleet Owner</span>
        </button>

        <div class="text-center pt-2">
          <p class="text-xs text-gray-500">
            Already registered as an owner?
            <Link
              href="/owner/login"
              class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline ms-1"
            >
              Sign In to Owner Portal
            </Link>
          </p>
        </div>
      </form>

      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-between text-xs text-gray-500">
        <Link
          href="/customer/register"
          class="hover:text-blue-600 transition-colors"
        >
          Register as Customer &rarr;
        </Link>
        <Link
          href="/"
          class="hover:text-emerald-600 transition-colors"
        >
          &larr; Back to Showroom
        </Link>
      </div>
    </div>
  </AuthLayout>
</template>
