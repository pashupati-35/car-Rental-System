<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { CustomerItem } from './types'
import CustomerTable from './components/CustomerTable.vue'
import CustomerFormModal from './components/CustomerFormModal.vue'
import CustomerPasswordModal from './components/CustomerPasswordModal.vue'

const props = defineProps<{
  customers?: any
  filters?: {
    search?: string
    per_page?: number
  }
}>()

const customersList = computed<CustomerItem[]>(() => {
  if (Array.isArray(props.customers)) return props.customers
  return props.customers?.data || []
})

const searchQuery = ref(props.filters?.search || '')
const showAddModal = ref(false)
const showEditModal = ref(false)
const submitting = ref(false)
const message = ref('')
const errorMessage = ref('')
const generatedResetUrl = ref('')
const showPasswordModal = ref(false)

const form = ref<Partial<CustomerItem>>({
  id: 0,
  name: '',
  email: '',
  phone_number: '',
  address: '',
  gender: 'male',
})

const filteredCustomers = computed<CustomerItem[]>(() => {
  if (!searchQuery.value.trim() || props.filters?.search) return customersList.value
  const q = searchQuery.value.toLowerCase()
  return customersList.value.filter((c: CustomerItem) => {
    const name = c.name || c.full_name || ''
    const email = c.email || ''
    const phone = c.phone_number || c.phone || ''
    const addr = c.address || ''
    return name.toLowerCase().includes(q) || email.toLowerCase().includes(q) || phone.toLowerCase().includes(q) || addr.toLowerCase().includes(q)
  })
})

const handleSearch = () => {
  router.get('/admin/customers', {
    search: searchQuery.value,
  }, { preserveState: true, preserveScroll: true })
}

const openAddModal = () => {
  form.value = {
    id: 0,
    name: '',
    email: '',
    phone_number: '',
    address: '',
    gender: 'male',
  }
  message.value = ''
  errorMessage.value = ''
  generatedResetUrl.value = ''
  showAddModal.value = true
}

const openEditModal = (customer: CustomerItem) => {
  form.value = {
    id: customer.id,
    name: customer.name || customer.full_name || '',
    email: customer.email || '',
    phone_number: customer.phone_number || customer.phone || '',
    address: customer.address || '',
    gender: customer.gender || 'male',
  }
  message.value = ''
  errorMessage.value = ''
  showEditModal.value = true
}

const submitNewCustomer = async () => {
  submitting.value = true
  message.value = ''
  errorMessage.value = ''
  generatedResetUrl.value = ''

  try {
    const res = await axios.post('/admin/customers', form.value)
    if (res.data?.status === 'success' || res.status === 200 || res.status === 201) {
      message.value = 'Customer profile created successfully!'
      showAddModal.value = false
      if (res.data?.reset_url) {
        generatedResetUrl.value = res.data.reset_url
        showPasswordModal.value = true
      }
      form.value = { id: 0, name: '', email: '', phone_number: '', address: '', gender: 'male' }
      router.reload({ only: ['customers'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to create customer.'
  } finally {
    submitting.value = false
  }
}

const submitEditCustomer = async () => {
  submitting.value = true
  message.value = ''
  errorMessage.value = ''

  try {
    const res = await axios.patch(`/admin/customers/${form.value.id}`, form.value)
    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Customer profile updated successfully.'
      showEditModal.value = false
      router.reload({ only: ['customers'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to update customer.'
  } finally {
    submitting.value = false
  }
}

const deleteCustomer = async (customerId: number) => {
  if (!confirm('Are you sure you want to permanently delete this customer account?')) return
  try {
    const res = await axios.delete(`/admin/customers/${customerId}`)
    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Customer deleted.'
      router.reload({ only: ['customers'] })
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete customer.')
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Customer Directory - Admin Portal" />

    <div class="space-y-6">
      <!-- Title & Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
              Customer Management
            </span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
              <i class="ri-user-heart-line text-indigo-600" />
              Customer Accounts Directory
            </h2>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            View registered rental clients, update accounts, issue access credentials, and review booking histories.
          </p>
        </div>

        <button
          type="button"
          class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2 shrink-0 self-start sm:self-auto cursor-pointer"
          @click="openAddModal"
        >
          <i class="ri-user-add-line text-sm" />
          <span>Add New Customer</span>
        </button>
      </div>

      <!-- Flash Notification -->
      <div v-if="message" class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
        <i class="ri-checkbox-circle-fill text-emerald-600 text-base shrink-0" />
        <span>{{ message }}</span>
      </div>

      <!-- Search & Toolbar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search customers by name, email, phone..."
            class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            @keyup.enter="handleSearch"
          />
        </div>

        <span class="text-xs font-bold text-slate-400">
          Total Customers: {{ props.customers?.total ?? filteredCustomers.length }}
        </span>
      </div>

      <!-- Main Display Table -->
      <CustomerTable
        :customers="filteredCustomers"
        :pagination="props.customers"
        @edit="openEditModal"
        @delete="deleteCustomer"
      />

      <!-- Create Customer Modal -->
      <CustomerFormModal
        :show="showAddModal"
        :is-editing="false"
        :form="form"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showAddModal = false"
        @save="submitNewCustomer"
      />

      <!-- Edit Customer Modal -->
      <CustomerFormModal
        :show="showEditModal"
        :is-editing="true"
        :form="form"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showEditModal = false"
        @save="submitEditCustomer"
      />

      <!-- Password Setup Modal -->
      <CustomerPasswordModal
        :show="showPasswordModal"
        :url="generatedResetUrl"
        @close="showPasswordModal = false"
      />
    </div>
  </AdminLayout>
</template>
