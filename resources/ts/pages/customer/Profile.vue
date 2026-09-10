<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.customer || auth.value?.user)

const profileForm = ref({
  name: user.value?.name || '',
  email: user.value?.email || '',
  phone_number: user.value?.phone_number || user.value?.phone || '',
  address: user.value?.address || '',
})

const profileLoading = ref(false)
const profileMsg = ref('')
const profileError = ref('')

const updateProfile = async () => {
  profileLoading.value = true
  profileMsg.value = ''
  profileError.value = ''
  try {
    const res = await axios.patch('/customer/profile', profileForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Customer profile updated successfully.'
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || 'Failed to update profile.'
  } finally {
    profileLoading.value = false
  }
}
</script>

<template>
  <AppLayout>
    <Head title="Customer Profile Settings" />

    <div class="space-y-6 max-w-5xl mx-auto">
      <!-- Top Header & Tabs -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-base">
              <i class="ri-user-settings-line" />
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Customer Profile Settings
            </h1>
          </div>
          <p class="text-xs text-slate-500 mt-1">
            Manage your account identity, personal contact info, and delivery address.
          </p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl shrink-0 self-start sm:self-auto">
          <Link
            href="/customer/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-300 shadow-xs"
          >
            Profile Details
          </Link>
          <Link
            href="/customer/security"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
          >
            Security & MFA
          </Link>
        </div>
      </div>

      <!-- Feedback Alerts -->
      <div
        v-if="profileMsg"
        class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between"
      >
        <div class="flex items-center gap-2">
          <i class="ri-checkbox-circle-fill text-lg text-emerald-600" />
          <span class="font-semibold">{{ profileMsg }}</span>
        </div>
        <button
          class="text-emerald-700 hover:underline cursor-pointer"
          @click="profileMsg = ''"
        >
          <i class="ri-close-line" />
        </button>
      </div>

      <div
        v-if="profileError"
        class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs flex items-center justify-between"
      >
        <div class="flex items-center gap-2">
          <i class="ri-error-warning-fill text-lg text-rose-600" />
          <span class="font-semibold">{{ profileError }}</span>
        </div>
        <button
          class="text-rose-700 hover:underline cursor-pointer"
          @click="profileError = ''"
        >
          <i class="ri-close-line" />
        </button>
      </div>

      <!-- Profile Information Card -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-2xl flex items-center justify-center shadow-md shadow-blue-600/20">
            {{ (profileForm.name || 'C')[0].toUpperCase() }}
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
              {{ profileForm.name || 'Valued Customer' }}
            </h3>
            <p class="text-xs text-slate-500 font-mono">
              {{ profileForm.email }}
            </p>
            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
              Verified Rental Client
            </span>
          </div>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="updateProfile"
        >
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Full Name</label>
              <input
                v-model="profileForm.name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address</label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Phone Number</label>
              <input
                v-model="profileForm.phone_number"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Residential Address</label>
              <input
                v-model="profileForm.address"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>

          <div class="pt-2 flex justify-end">
            <button
              type="submit"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/25 transition-all flex items-center justify-center gap-2 cursor-pointer"
              :disabled="profileLoading"
            >
              <i
                v-if="profileLoading"
                class="ri-loader-4-line animate-spin"
              />
              <span>{{ profileLoading ? 'Saving Profile...' : 'Save Profile Changes' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
