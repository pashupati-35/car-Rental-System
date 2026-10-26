<script setup lang="ts">
import { ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  accounts: any
  filters: {
    search?: string
    status?: string
  }
}>()

const searchQuery = ref(props.filters.search || '')
const selectedStatus = ref(props.filters.status || 'all')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const selectedAccountForEdit = ref<any>(null)

const accountForm = useForm({
  company_name: '',
  business_reg_number: '',
  tax_id: '',
  contact_person: '',
  email: '',
  phone: '',
  address: '',
  credit_limit: 5000,
  contract_discount_percent: 10,
  payment_terms: 'Net 30',
  status: 'active',
  notes: '',
})

const editForm = useForm({
  company_name: '',
  business_reg_number: '',
  tax_id: '',
  contact_person: '',
  email: '',
  phone: '',
  address: '',
  credit_limit: 0,
  contract_discount_percent: 0,
  payment_terms: 'Net 30',
  status: 'active',
  notes: '',
})

const applyFilters = () => {
  router.get('/admin/crm/corporate-accounts', {
    search: searchQuery.value || undefined,
    status: selectedStatus.value === 'all' ? undefined : selectedStatus.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

let searchTimer: any = null

const onSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    applyFilters()
  }, 350)
}

const openCreateModal = () => {
  accountForm.reset()
  showCreateModal.value = true
}

const submitCreate = () => {
  accountForm.post('/admin/crm/corporate-accounts', {
    onSuccess: () => {
      showCreateModal.value = false
      accountForm.reset()
    },
  })
}

const openEditModal = (acc: any) => {
  selectedAccountForEdit.value = acc
  editForm.company_name = acc.company_name
  editForm.business_reg_number = acc.business_reg_number || ''
  editForm.tax_id = acc.tax_id || ''
  editForm.contact_person = acc.contact_person
  editForm.email = acc.email
  editForm.phone = acc.phone || ''
  editForm.address = acc.address || ''
  editForm.credit_limit = acc.credit_limit || 0
  editForm.contract_discount_percent = acc.contract_discount_percent || 0
  editForm.payment_terms = acc.payment_terms || 'Net 30'
  editForm.status = acc.status
  editForm.notes = acc.notes || ''
  showEditModal.value = true
}

const submitEdit = () => {
  if (!selectedAccountForEdit.value) return
  editForm.put(`/admin/crm/corporate-accounts/${selectedAccountForEdit.value.id}`, {
    onSuccess: () => {
      showEditModal.value = false
      selectedAccountForEdit.value = null
    },
  })
}

const deleteAccount = (acc: any) => {
  if (confirm(`Remove corporate account "${acc.company_name}"?`)) {
    router.delete(`/admin/crm/corporate-accounts/${acc.id}`, {
      preserveScroll: true,
    })
  }
}
</script>

<template>
  <CrmLayout>
    <Head title="Corporate B2B Accounts - CRM" />

    <div class="space-y-6 pb-12">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            Corporate B2B Fleet Accounts
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Manage corporate contracts, dedicated fleet discounts, credit limits, and Net-term billing.
          </p>
        </div>

        <button
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-sm"
          @click="openCreateModal"
        >
          <i class="ri-building-line text-base" />
          <span>New Corporate Account</span>
        </button>
      </div>

      <!-- Toolbar -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search company, contact, tax ID..."
            class="w-full ps-10 pe-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @input="onSearch"
          >
        </div>

        <select
          v-model="selectedStatus"
          class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium"
          @change="applyFilters"
        >
          <option value="all">
            All Statuses
          </option>
          <option value="active">
            Active
          </option>
          <option value="pending">
            Pending Approval
          </option>
          <option value="suspended">
            Suspended
          </option>
        </select>
      </div>

      <!-- Accounts Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-5 py-3.5">
                  Company Profile
                </th>
                <th class="px-5 py-3.5">
                  Contact Person
                </th>
                <th class="px-5 py-3.5">
                  Contract Discount
                </th>
                <th class="px-5 py-3.5">
                  Credit Limit
                </th>
                <th class="px-5 py-3.5">
                  Payment Terms
                </th>
                <th class="px-5 py-3.5">
                  Status
                </th>
                <th class="px-5 py-3.5 text-right">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-if="!accounts?.data?.length">
                <td
                  colspan="7"
                  class="text-center py-10 text-slate-400 text-sm"
                >
                  No corporate accounts registered yet.
                </td>
              </tr>
              <tr
                v-for="acc in accounts?.data || []"
                :key="acc.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition"
              >
                <!-- Company -->
                <td class="px-5 py-4">
                  <div class="font-bold text-slate-900 dark:text-white">
                    {{ acc.company_name }}
                  </div>
                  <div class="text-[11px] text-slate-400">
                    Reg: {{ acc.business_reg_number || 'N/A' }} &bull; Tax ID: {{ acc.tax_id || 'N/A' }}
                  </div>
                </td>

                <!-- Contact -->
                <td class="px-5 py-4 text-xs">
                  <div class="font-semibold text-slate-800 dark:text-slate-200">
                    {{ acc.contact_person }}
                  </div>
                  <div class="text-slate-500">
                    {{ acc.email }}
                  </div>
                  <div
                    v-if="acc.phone"
                    class="text-slate-400"
                  >
                    {{ acc.phone }}
                  </div>
                </td>

                <!-- Discount -->
                <td class="px-5 py-4">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300">
                    {{ acc.contract_discount_percent }}% OFF
                  </span>
                </td>

                <!-- Credit -->
                <td class="px-5 py-4 font-bold text-slate-900 dark:text-white text-xs">
                  ${{ Number(acc.credit_limit || 0).toLocaleString() }}
                </td>

                <!-- Terms -->
                <td class="px-5 py-4 text-xs font-medium">
                  {{ acc.payment_terms }}
                </td>

                <!-- Status -->
                <td class="px-5 py-4">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                    :class="{
                      'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300': acc.status === 'active',
                      'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300': acc.status === 'pending',
                      'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300': acc.status === 'suspended',
                    }"
                  >
                    {{ acc.status }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                      title="Edit Account"
                      @click="openEditModal(acc)"
                    >
                      <i class="ri-edit-line" />
                    </button>
                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                      title="Delete Account"
                      @click="deleteAccount(acc)"
                    >
                      <i class="ri-delete-bin-line" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Account Modal -->
      <div
        v-if="showCreateModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              Register Corporate Account
            </h3>
            <button
              class="text-slate-400 hover:text-slate-600"
              @click="showCreateModal = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <form
            class="space-y-4"
            @submit.prevent="submitCreate"
          >
            <div>
              <label class="block text-xs font-semibold mb-1">Company Name *</label>
              <input
                v-model="accountForm.company_name"
                required
                type="text"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Business Reg Number</label>
                <input
                  v-model="accountForm.business_reg_number"
                  type="text"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Tax / VAT ID</label>
                <input
                  v-model="accountForm.tax_id"
                  type="text"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Contact Person *</label>
                <input
                  v-model="accountForm.contact_person"
                  required
                  type="text"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Contact Email *</label>
                <input
                  v-model="accountForm.email"
                  required
                  type="email"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Phone Number</label>
                <input
                  v-model="accountForm.phone"
                  type="tel"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Billing Terms</label>
                <select
                  v-model="accountForm.payment_terms"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="Net 15">
                    Net 15 Days
                  </option>
                  <option value="Net 30">
                    Net 30 Days
                  </option>
                  <option value="Net 60">
                    Net 60 Days
                  </option>
                  <option value="Due on Receipt">
                    Due on Receipt
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Credit Limit ($)</label>
                <input
                  v-model="accountForm.credit_limit"
                  type="number"
                  step="0.01"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Contract Discount (%)</label>
                <input
                  v-model="accountForm.contract_discount_percent"
                  type="number"
                  step="0.01"
                  max="100"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Office Address</label>
              <input
                v-model="accountForm.address"
                type="text"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
              <button
                type="button"
                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
                @click="showCreateModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="accountForm.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm"
              >
                {{ accountForm.processing ? 'Registering...' : 'Register Corporate Account' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Edit Modal -->
      <div
        v-if="showEditModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              Edit Corporate Account
            </h3>
            <button
              class="text-slate-400 hover:text-slate-600"
              @click="showEditModal = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <form
            class="space-y-4"
            @submit.prevent="submitEdit"
          >
            <div>
              <label class="block text-xs font-semibold mb-1">Company Name</label>
              <input
                v-model="editForm.company_name"
                required
                type="text"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Contact Person</label>
                <input
                  v-model="editForm.contact_person"
                  required
                  type="text"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Contact Email</label>
                <input
                  v-model="editForm.email"
                  required
                  type="email"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Credit Limit ($)</label>
                <input
                  v-model="editForm.credit_limit"
                  type="number"
                  step="0.01"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Discount (%)</label>
                <input
                  v-model="editForm.contract_discount_percent"
                  type="number"
                  step="0.01"
                  max="100"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Status</label>
                <select
                  v-model="editForm.status"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="active">
                    Active
                  </option>
                  <option value="pending">
                    Pending
                  </option>
                  <option value="suspended">
                    Suspended
                  </option>
                </select>
              </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
              <button
                type="button"
                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
                @click="showEditModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="editForm.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm"
              >
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
