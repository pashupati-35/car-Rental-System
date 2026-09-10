<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps<{
  customers?: Array<any>
}>()

const customersList = ref<any[]>(props.customers || [])
const loading = ref(false)
const showAddModal = ref(false)
const submitting = ref(false)
const message = ref('')
const errorMessage = ref('')
const generatedResetUrl = ref('')

const form = ref({
  name: '',
  email: '',
  phone_number: '',
  address: '',
  gender: 'male',
})

const fetchCustomers = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/admin/customers')
    if (res.data?.data) {
      customersList.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to fetch customers:', err)
  } finally {
    loading.value = false
  }
}

const submitNewCustomer = async () => {
  submitting.value = true
  message.value = ''
  errorMessage.value = ''
  generatedResetUrl.value = ''

  try {
    const res = await axios.post('/admin/customers', form.value)
    if (res.data?.status === 'success' || res.status === 200 || res.status === 201) {
      message.value = 'Customer registered successfully! Password setup email has been dispatched.'
      if (res.data?.reset_url) {
        generatedResetUrl.value = res.data.reset_url
      }
      form.value = { name: '', email: '', phone_number: '', address: '', gender: 'male' }
      fetchCustomers()
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to register customer.'
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  if (!customersList.value.length) {
    fetchCustomers()
  }
})
</script>

<template>
  <AppLayout>
    <Head title="Registered Customers - Admin Portal" />

    <template #header>
      Customer Directory
    </template>

    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">
            Customer Records
          </h3>
          <p class="text-xs text-gray-500 mt-0.5">
            Manage customer accounts, bookings history, and onboard new customers.
          </p>
        </div>

        <button
          class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs shadow-md shadow-purple-500/20 transition-all flex items-center gap-2"
          @click="showAddModal = true; message = ''; errorMessage = ''; generatedResetUrl = ''"
        >
          <i class="ri-user-add-line text-sm" />
          <span>Add Customer</span>
        </button>
      </div>

      <!-- Table View -->
      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div v-if="loading" class="p-8 text-center text-sm text-gray-500">
          Loading customer records...
        </div>
        <table v-else class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
            <tr>
              <th class="py-3.5 px-6">Customer Name</th>
              <th class="py-3.5 px-6">Email Address</th>
              <th class="py-3.5 px-6">Phone Number</th>
              <th class="py-3.5 px-6">Address</th>
              <th class="py-3.5 px-6">Total Bookings</th>
              <th class="py-3.5 px-6">Security (MFA)</th>
              <th class="py-3.5 px-6">Joined Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr
              v-for="customer in customersList"
              :key="customer.id"
              class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40"
            >
              <td class="py-4 px-6 font-medium text-gray-900 dark:text-white flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs">
                  {{ customer.name ? customer.name.charAt(0).toUpperCase() : 'C' }}
                </div>
                <span>{{ customer.name }}</span>
              </td>
              <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                {{ customer.email }}
              </td>
              <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                {{ customer.phone_number || customer.phone || 'N/A' }}
              </td>
              <td class="py-4 px-6 text-xs text-gray-500">
                {{ customer.address || 'N/A' }}
              </td>
              <td class="py-4 px-6">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                  {{ customer.bookings_count ?? customer.bookings?.length ?? 0 }} Trips
                </span>
              </td>
              <td class="py-4 px-6">
                <span
                  :class="customer.is_mfa_enabled ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-600'"
                  class="px-2 py-0.5 rounded text-[11px] font-semibold"
                >
                  {{ customer.is_mfa_enabled ? 'MFA Active' : 'Off' }}
                </span>
              </td>
              <td class="py-4 px-6 text-xs text-gray-500">
                {{ customer.created_at ? new Date(customer.created_at).toLocaleDateString() : 'Recent' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add Customer Modal -->
      <div
        v-if="showAddModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto"
      >
        <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-lg w-full p-6 border border-gray-100 dark:border-gray-800 shadow-2xl relative">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white">
              Add New Customer
            </h3>
            <button
              class="text-gray-400 hover:text-gray-600 text-lg"
              @click="showAddModal = false"
            >
              &times;
            </button>
          </div>

          <p class="text-xs text-gray-500 mb-6">
            When you register a customer, an invitation email with a secure password setup link will be dispatched automatically.
          </p>

          <div v-if="message" class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
            <p class="font-bold flex items-center gap-1.5"><i class="ri-checkbox-circle-fill text-sm" /> {{ message }}</p>
            <div v-if="generatedResetUrl" class="mt-2 pt-2 border-t border-emerald-200">
              <span class="text-[11px] text-gray-600 block mb-1">Direct Setup / Reset URL:</span>
              <a :href="generatedResetUrl" target="_blank" class="text-[11px] font-mono text-purple-600 hover:underline break-all">
                {{ generatedResetUrl }}
              </a>
            </div>
          </div>

          <div v-if="errorMessage" class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-1.5">
            <i class="ri-error-warning-fill text-sm" />
            <span>{{ errorMessage }}</span>
          </div>

          <form class="space-y-4" @submit.prevent="submitNewCustomer">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Customer Full Name</label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="e.g. Jane Smith"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
              <input
                v-model="form.email"
                type="email"
                required
                placeholder="customer@example.com"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                <input
                  v-model="form.phone_number"
                  type="text"
                  required
                  placeholder="+1 555 123 4567"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                <select
                  v-model="form.gender"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Residential / Delivery Address</label>
              <input
                v-model="form.address"
                type="text"
                required
                placeholder="456 Sunset Blvd, Los Angeles"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
              <button
                type="button"
                class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold text-xs"
                @click="showAddModal = false"
              >
                Close
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs shadow-md shadow-purple-500/20 disabled:opacity-50"
              >
                {{ submitting ? 'Sending Setup Email...' : 'Save & Send Reset Email' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
