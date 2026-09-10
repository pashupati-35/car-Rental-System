<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import { resolveMediaUrl } from '@/utils/helpers'
import type { CustomerItem } from '../types'

const props = defineProps<{
  show: boolean
  customer: CustomerItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'edit', customer: CustomerItem): void
}>()

const imgError = ref(false)
const loggingIn = ref(false)

watch(() => props.customer, () => {
  imgError.value = false
})

const loginAsCustomer = async () => {
  if (!props.customer) return
  if (!confirm(`Are you sure you want to log in as customer "${props.customer.name || props.customer.full_name || props.customer.email}"? You will be redirected to the customer portal dashboard.`)) return
  
  try {
    loggingIn.value = true

    const res = await axios.post(`/admin/customers/${props.customer.id}/login-as`)
    if (res.data?.redirect_url) {
      window.location.href = res.data.redirect_url
    } else {
      window.location.href = '/customer/dashboard'
    }
  } catch (err: any) {
    alert(err?.response?.data?.message || 'Failed to authenticate as customer.')
    loggingIn.value = false
  }
}

const avatarUrl = computed(() => {
  if (!props.customer) return ''
  
  return resolveMediaUrl(props.customer.image, props.customer.image_path, 'customer')
})

const formatDate = (dateStr?: string) => {
  if (!dateStr) return 'N/A'
  try {
    return new Date(dateStr).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch {
    return dateStr
  }
}

const formatDateTime = (dateStr?: string) => {
  if (!dateStr) return 'N/A'
  try {
    return new Date(dateStr).toLocaleString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return dateStr
  }
}
</script>

<template>
  <div
    v-if="show && customer"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-4xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
      <!-- Modal Header -->
      <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40 shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
            <i class="ri-user-heart-line" />
          </div>
          <div>
            <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
              Customer Profile Overview
            </h3>
            <p class="text-xs text-slate-500">
              UID: #{{ customer.unique_identifier || `CUS-${customer.id}` }} &bull; Complete profile information & database fields
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            :disabled="loggingIn"
            class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold text-xs cursor-pointer flex items-center gap-1.5 transition-colors disabled:opacity-50"
            title="Log in to customer portal dashboard as this user"
            @click="loginAsCustomer"
          >
            <i
              v-if="loggingIn"
              class="ri-loader-4-line animate-spin text-sm"
            />
            <i
              v-else
              class="ri-login-box-line text-sm text-emerald-600"
            />
            <span>Login as Customer</span>
          </button>
          <button
            type="button"
            class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-xs cursor-pointer flex items-center gap-1.5 transition-colors"
            @click="emit('edit', customer)"
          >
            <i class="ri-edit-line text-sm" />
            <span>Edit Profile</span>
          </button>
          <button
            type="button"
            class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center transition-colors cursor-pointer"
            @click="emit('close')"
          >
            <i class="ri-close-line text-lg" />
          </button>
        </div>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 overflow-y-auto space-y-6 text-xs text-slate-700 dark:text-slate-300">
        <!-- Top Identity Banner Card -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 p-5 rounded-2xl bg-gradient-to-br from-indigo-50/60 via-slate-50/40 to-blue-50/40 dark:from-indigo-950/30 dark:via-slate-900/40 dark:to-blue-950/20 border border-indigo-100/80 dark:border-indigo-900/40">
          <div class="relative w-20 h-20 rounded-2xl overflow-hidden bg-white dark:bg-slate-800 border-2 border-indigo-200 dark:border-indigo-800 shadow-sm shrink-0 flex items-center justify-center">
            <img
              v-if="avatarUrl && !imgError"
              :src="avatarUrl"
              :alt="customer.name || customer.full_name"
              class="w-full h-full object-cover"
              @error="imgError = true"
            >
            <span
              v-else
              class="text-2xl font-black text-indigo-600 dark:text-indigo-400"
            >
              {{ (customer.name || customer.full_name || 'C').charAt(0).toUpperCase() }}
            </span>
          </div>

          <div class="flex-1 text-center sm:text-left space-y-2">
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
              <h4 class="text-lg font-black text-slate-900 dark:text-white">
                {{ customer.name || customer.full_name || 'Unnamed Customer' }}
              </h4>
              <span
                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                :class="customer.is_active !== false ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300'"
              >
                {{ customer.is_active !== false ? 'Active Account' : 'Suspended' }}
              </span>
              <span
                v-if="customer.approval_status"
                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300"
              >
                {{ customer.approval_status }}
              </span>
            </div>

            <p class="text-slate-500 dark:text-slate-400 flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-1">
              <span class="flex items-center gap-1 font-mono text-slate-600 dark:text-slate-300">
                <i class="ri-mail-line text-indigo-500" />
                {{ customer.email || 'No email' }}
              </span>
              <span class="flex items-center gap-1">
                <i class="ri-phone-line text-indigo-500" />
                {{ customer.phone_number || customer.phone || customer.mobile || 'No phone number' }}
              </span>
              <span
                v-if="customer.username"
                class="flex items-center gap-1 font-mono"
              >
                <i class="ri-user-line text-indigo-500" />
                @{{ customer.username }}
              </span>
            </p>

            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
              <span class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px] font-bold text-slate-700 dark:text-slate-300">
                <i class="ri-calendar-check-line text-indigo-500 me-1" />
                {{ customer.bookings_count ?? 0 }} Total Bookings
              </span>
              <span
                v-if="customer.is_mfa_enabled"
                class="px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-[10px]"
              >
                <i class="ri-shield-keyhole-line me-0.5" /> MFA Enabled
              </span>
            </div>
          </div>
        </div>

        <!-- Details Grid Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Personal & Identity Information -->
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3">
            <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-200/60 dark:border-slate-700 pb-2">
              <i class="ri-user-3-line text-indigo-500" />
              Personal & Legal Identity
            </h5>
            <div class="grid grid-cols-2 gap-2.5">
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">First Name</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.first_name || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Middle Name</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.middle_name || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Last Name</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.last_name || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Gender</span>
                <span class="capitalize font-semibold text-slate-800 dark:text-slate-200">{{ customer.gender || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Date of Birth</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ formatDate(customer.date_of_birth) }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Marital Status</span>
                <span class="capitalize font-semibold text-slate-800 dark:text-slate-200">{{ customer.marital_status || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Nationality</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.nationality || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Citizenship No.</span>
                <span class="font-semibold font-mono text-slate-800 dark:text-slate-200">{{ customer.citizenship_number || 'N/A' }}</span>
              </div>
              <div class="col-span-2">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Passport Number</span>
                <span class="font-semibold font-mono text-slate-800 dark:text-slate-200">{{ customer.passport_number || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Contact, Profession & Address -->
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3">
            <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-200/60 dark:border-slate-700 pb-2">
              <i class="ri-map-pin-user-line text-indigo-500" />
              Contact & Location Details
            </h5>
            <div class="grid grid-cols-2 gap-2.5">
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Primary Phone</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.phone_number || customer.phone || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Mobile</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.mobile || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Position</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.position || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Designation</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.designation || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">User Type</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.user_type || 'Customer' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Access Type</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.access_type || 'Customer Portal' }}</span>
              </div>
              <div class="col-span-2">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Residential Address</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.address || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- Emergency Contacts & Relations -->
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3">
            <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-200/60 dark:border-slate-700 pb-2">
              <i class="ri-alarm-warning-line text-rose-500" />
              Emergency Contact & Relations
            </h5>
            <div class="grid grid-cols-2 gap-2.5">
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Emergency Phone</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.emergency_contact || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Contact Person</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.contact_person_name || 'N/A' }}</span>
              </div>
              <div class="col-span-2">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Relationship</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.contact_relationship || 'N/A' }}</span>
              </div>
            </div>
          </div>

          <!-- System Security & Account Info -->
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3">
            <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-200/60 dark:border-slate-700 pb-2">
              <i class="ri-shield-check-line text-emerald-500" />
              System Security & Timestamps
            </h5>
            <div class="grid grid-cols-2 gap-2.5">
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">MFA 2FA Enabled</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.is_mfa_enabled ? 'Yes (Enforced)' : 'No' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Email 2FA</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.is_email_authentication_enabled ? 'Yes' : 'No' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Last Logged In</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ formatDateTime(customer.last_logged_in) }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Theme Style</span>
                <span class="capitalize font-semibold text-slate-800 dark:text-slate-200">{{ customer.theme_style || 'Default' }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Registered At</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ formatDateTime(customer.created_at) }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Last Updated</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ formatDateTime(customer.updated_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
          <button
            type="button"
            :disabled="loggingIn"
            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
            title="Log in to customer portal dashboard as this user"
            @click="loginAsCustomer"
          >
            <i
              v-if="loggingIn"
              class="ri-loader-4-line animate-spin"
            />
            <i
              v-else
              class="ri-login-box-line"
            />
            <span>Login as Customer</span>
          </button>

          <Link
            :href="`/admin/customers/${customer.id}`"
            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5"
          >
            <span>Open Full Customer Profile</span>
            <i class="ri-external-link-line" />
          </Link>
        </div>

        <button
          type="button"
          class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer"
          @click="emit('close')"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>
