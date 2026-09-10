<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'

const page = usePage()
const auth = ref(page.props.auth as any)
const user = ref(auth.value?.admin || auth.value?.user || auth.value?.owner || auth.value?.customer)

const profileForm = ref({
  name: user.value?.name || '',
  email: user.value?.email || '',
  contact_number: user.value?.contact_number || '',
  address: user.value?.address || '',
  designation: user.value?.designation || 'Super Administrator',
  avatar: user.value?.avatar || '',
})

const profileLoading = ref(false)
const profileMsg = ref('')
const profileError = ref('')

const updateProfile = async () => {
  profileLoading.value = true
  profileMsg.value = ''
  profileError.value = ''
  try {
    const res = await axios.patch('/admin/profile', profileForm.value)
    if (res.data?.status === 'OK' || res.status === 200) {
      profileMsg.value = 'Administrative profile details updated successfully.'
      if (res.data?.user) {
        user.value = res.data.user
      }
    }
  } catch (err: any) {
    profileError.value = err.response?.data?.message || 'Failed to update profile.'
  } finally {
    profileLoading.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Admin Profile Information" />

    <div class="space-y-6 max-w-5xl mx-auto pb-12">
      <!-- Breadcrumb Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <Link
              href="/admin/dashboard"
              class="hover:text-indigo-600 transition-colors"
            >
              Dashboard
            </Link>
            <span>/</span>
            <span class="text-slate-600 dark:text-slate-300 font-semibold">Admin Profile</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 inline-flex items-center justify-center text-lg">
              <i class="ri-user-settings-line" />
            </span>
            Profile Information
          </h1>
          <p class="text-xs text-slate-500 mt-1">
            Manage your administrative account identity, personal contact details, and organization role.
          </p>
        </div>

        <!-- Tab Switcher (Profile Information vs Account Security) -->
        <div class="inline-flex p-1 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <Link
            href="/admin/profile"
            class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-xs transition-all flex items-center gap-1.5"
          >
            <i class="ri-user-line text-sm" />
            <span>Profile Information</span>
          </Link>
          <Link
            href="/admin/security"
            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-1.5"
          >
            <i class="ri-shield-check-line text-sm" />
            <span>Account Security & MFA</span>
          </Link>
        </div>
      </div>

      <!-- Success / Error Alert Messages -->
      <div
        v-if="profileMsg"
        class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800 flex items-center justify-between shadow-xs"
      >
        <div class="flex items-center gap-2">
          <i class="ri-checkbox-circle-fill text-lg text-emerald-500" />
          <span>{{ profileMsg }}</span>
        </div>
        <button
          type="button"
          class="text-emerald-600 hover:text-emerald-900 cursor-pointer"
          @click="profileMsg = ''"
        >
          &times;
        </button>
      </div>

      <div
        v-if="profileError"
        class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800 flex items-center justify-between shadow-xs"
      >
        <div class="flex items-center gap-2">
          <i class="ri-error-warning-fill text-lg text-rose-500" />
          <span>{{ profileError }}</span>
        </div>
        <button
          type="button"
          class="text-rose-600 hover:text-rose-900 cursor-pointer"
          @click="profileError = ''"
        >
          &times;
        </button>
      </div>

      <!-- Profile Form Card -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-6">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
            {{ (profileForm.name || 'A')[0].toUpperCase() }}
          </div>
          <div>
            <h3 class="font-black text-lg text-slate-900 dark:text-white">
              {{ profileForm.name || 'Super Administrator' }}
            </h3>
            <p class="text-xs text-slate-400 font-mono">
              {{ profileForm.email }}
            </p>
            <span class="mt-1 inline-block text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800">
              {{ profileForm.designation || 'Fleet Director' }}
            </span>
          </div>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="updateProfile"
        >
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Full Name
              </label>
              <input
                v-model="profileForm.name"
                type="text"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Email Address
              </label>
              <input
                v-model="profileForm.email"
                type="email"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Contact Number / Mobile
              </label>
              <input
                v-model="profileForm.contact_number"
                type="text"
                placeholder="+977-9841234567"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Designation / Position
              </label>
              <input
                v-model="profileForm.designation"
                type="text"
                placeholder="Super Administrator & Fleet Director"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Office / Residential Address
              </label>
              <input
                v-model="profileForm.address"
                type="text"
                placeholder="Kathmandu, Bagmati, Nepal"
                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              >
            </div>
          </div>

          <div class="pt-2 flex justify-end">
            <button
              type="submit"
              :disabled="profileLoading"
              class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <i
                v-if="profileLoading"
                class="ri-loader-4-line animate-spin text-sm"
              />
              <i
                v-else
                class="ri-save-line text-sm"
              />
              <span>{{ profileLoading ? 'Saving Profile...' : 'Save Profile Changes' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
