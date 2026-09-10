<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { CustomerItem } from './types'
import CustomerTable from './components/CustomerTable.vue'
import CustomerFormModal from './components/CustomerFormModal.vue'
import CustomerDetailModal from './components/CustomerDetailModal.vue'
import CustomerPasswordModal from './components/CustomerPasswordModal.vue'
import MessageBox from '@/components/MessageBox.vue'

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
const showDetailModal = ref(false)
const editingCustomer = ref<CustomerItem | null>(null)
const viewingCustomer = ref<CustomerItem | null>(null)
const submitting = ref(false)
const message = ref('')
const errorMessage = ref('')
const generatedResetUrl = ref('')
const showPasswordModal = ref(false)

let searchTimeout: any = null

const applyFilters = () => {
  router.get('/admin/customers', {
    search: searchQuery.value || undefined,
    per_page: props.filters?.per_page || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const onSearchInput = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 350)
}

const openAddModal = () => {
  editingCustomer.value = null
  message.value = ''
  errorMessage.value = ''
  generatedResetUrl.value = ''
  showAddModal.value = true
}

const openEditModal = (customer: CustomerItem) => {
  editingCustomer.value = { ...customer }
  message.value = ''
  errorMessage.value = ''
  showDetailModal.value = false
  showEditModal.value = true
}

const openDetailModal = (customer: CustomerItem) => {
  viewingCustomer.value = customer
  showDetailModal.value = true
}

const submitNewCustomer = async (formData: FormData) => {
  submitting.value = true
  message.value = ''
  errorMessage.value = ''
  generatedResetUrl.value = ''

  try {
    const res = await axios.post('/admin/customers', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    if (res.data?.status === 'success' || res.status === 200 || res.status === 201) {
      message.value = 'Customer profile created successfully!'
      showAddModal.value = false
      if (res.data?.reset_url) {
        generatedResetUrl.value = res.data.reset_url
        showPasswordModal.value = true
      }
      router.reload({ only: ['customers'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to create customer.'
  } finally {
    submitting.value = false
  }
}

const submitEditCustomer = async (formData: FormData) => {
  if (!editingCustomer.value?.id) return
  submitting.value = true
  message.value = ''
  errorMessage.value = ''

  try {
    const res = await axios.post(`/admin/customers/${editingCustomer.value.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

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
      <MessageBox
        v-model="message"
        type="success"
      />
      <MessageBox
        v-model="errorMessage"
        type="error"
      />

      <!-- Search & Toolbar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search customers by name, email, phone..."
            class="w-full py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-400 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            style="padding-left: 2rem; padding-right: 0.75rem"
            @input="onSearchInput"
            @keyup.enter="applyFilters"
          >
        </div>

        <span class="text-xs font-bold text-slate-400">
          Total Customers: {{ props.customers?.total ?? customersList.length }}
        </span>
      </div>

      <!-- Main Display Table -->
      <CustomerTable
        :customers="customersList"
        :pagination="props.customers"
        @view="openDetailModal"
        @edit="openEditModal"
        @delete="deleteCustomer"
      />

      <!-- Create Customer Modal -->
      <CustomerFormModal
        :show="showAddModal"
        :is-editing="false"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showAddModal = false"
        @save="submitNewCustomer"
      />

      <!-- Edit Customer Modal -->
      <CustomerFormModal
        :customer="editingCustomer"
        :show="showEditModal"
        is-editing
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showEditModal = false"
        @save="submitEditCustomer"
      />

      <!-- Customer Details Overview Modal -->
      <CustomerDetailModal
        :show="showDetailModal"
        :customer="viewingCustomer"
        @close="showDetailModal = false"
        @edit="openEditModal"
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
