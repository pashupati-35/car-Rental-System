<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.owner || auth.value?.user)

const profileForm = ref({
  full_name: user.value?.full_name || user.value?.name || '',
  email: user.value?.email || '',
  contact_number: user.value?.contact_number || '',
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
    const res = await axios.patch('/owner/profile', profileForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Fleet Owner profile updated successfully.'
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || 'Failed to update profile.'
  } finally {
    profileLoading.value = false
  }
}
</script>

<template>
  <OwnerLayout>
    <Head title="Owner Profile Settings" />

    <div class="space-y-6 max-w-5xl mx-auto">
      <!-- Top Header & Tabs -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
          <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-base">
              <i class="ri-user-settings-line" />
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              Owner Profile Settings
            </h1>
          </div>
          <p class="text-xs text-slate-500 mt-1">
            Manage your fleet owner identity, contact details, and business coordinates.
          </p>
        </div>

        <!-- Tab Navigation -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl shrink-0 self-start sm:self-auto">
          <Link
            href="/owner/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-300 shadow-xs"
          >
            Profile Details
          </Link>
          <Link
            href="/owner/security"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
          >
            Security & MFA
          </Link>
        </div>
      </div>

      <!-- Feedback Alerts -->
      <MessageBox
        v-model="profileMsg"
        type="success"
      />
      <MessageBox
        v-model="profileError"
        type="error"
      />

      <!-- Profile Information Card -->
      <div class="p-6 sm:p-7 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white font-black text-2xl flex items-center justify-center shadow-md shadow-emerald-600/20">
            {{ (profileForm.full_name || 'O')[0].toUpperCase() }}
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
              {{ profileForm.full_name || 'Fleet Partner' }}
            </h3>
            <p class="text-xs text-slate-500 font-mono">
              {{ profileForm.email }}
            </p>
            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
              Verified Fleet Partner
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
                v-model="profileForm.full_name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address</label>
              <input
                v-model="profileForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Number</label>
              <input
                v-model="profileForm.contact_number"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Address / Headquarters</label>
              <input
                v-model="profileForm.address"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
              >
            </div>
          </div>

          <div class="pt-2 flex justify-end">
            <button
              type="submit"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/25 transition-all flex items-center justify-center gap-2 cursor-pointer"
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
  </OwnerLayout>
</template>
