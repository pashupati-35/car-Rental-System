<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { OwnerItem } from './types'
import OwnerTable from './components/OwnerTable.vue'
import OwnerFormModal from './components/OwnerFormModal.vue'
import OwnerDetailModal from './components/OwnerDetailModal.vue'
import OwnerPasswordModal from './components/OwnerPasswordModal.vue'
import MessageBox from '@/components/MessageBox.vue'

const props = defineProps<{
  owners?: any
  filters?: {
    search?: string
    per_page?: number
  }
}>()

const ownersList = computed<OwnerItem[]>(() => {
  if (Array.isArray(props.owners)) return props.owners
  return props.owners?.data || []
})

const searchQuery = ref(props.filters?.search || '')
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDetailModal = ref(false)
const editingOwner = ref<OwnerItem | null>(null)
const viewingOwner = ref<OwnerItem | null>(null)
const submitting = ref(false)
const message = ref('')
const errorMessage = ref('')
const generatedResetUrl = ref('')
const showPasswordModal = ref(false)

let searchTimeout: any = null

const applyFilters = () => {
  router.get('/admin/owners', {
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
  editingOwner.value = null
  message.value = ''
  errorMessage.value = ''
  generatedResetUrl.value = ''
  showAddModal.value = true
}

const openEditModal = (owner: OwnerItem) => {
  editingOwner.value = { ...owner }
  message.value = ''
  errorMessage.value = ''
  showDetailModal.value = false
  showEditModal.value = true
}

const openDetailModal = (owner: OwnerItem) => {
  viewingOwner.value = owner
  showDetailModal.value = true
}

const submitNewOwner = async (formData: FormData) => {
  submitting.value = true
  message.value = ''
  errorMessage.value = ''
  generatedResetUrl.value = ''

  try {
    const res = await axios.post('/admin/owners', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    if (res.data?.status === 'success' || res.status === 200 || res.status === 201) {
      message.value = 'Fleet Owner registered successfully!'
      showAddModal.value = false
      if (res.data?.reset_url) {
        generatedResetUrl.value = res.data.reset_url
        showPasswordModal.value = true
      }
      router.reload({ only: ['owners'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to register fleet owner.'
  } finally {
    submitting.value = false
  }
}

const submitEditOwner = async (formData: FormData) => {
  if (!editingOwner.value?.id) return
  submitting.value = true
  message.value = ''
  errorMessage.value = ''

  try {
    const res = await axios.post(`/admin/owners/${editingOwner.value.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Owner profile updated successfully.'
      showEditModal.value = false
      router.reload({ only: ['owners'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to update owner profile.'
  } finally {
    submitting.value = false
  }
}

const deleteOwner = async (ownerId: number) => {
  if (!confirm('Are you sure you want to delete this fleet owner and all associated data?')) return
  try {
    const res = await axios.delete(`/admin/owner/delete/${ownerId}`)
    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Owner deleted.'
      router.reload({ only: ['owners'] })
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete owner.')
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Fleet Owners Management - Admin Portal" />

    <div class="space-y-6">
      <!-- Title & Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
              Partner Hub
            </span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
              <i class="ri-user-star-line text-indigo-600" />
              Fleet Owners Management
            </h2>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            Manage registered car owners, inspect individual vehicle fleets, and assign drivers across the system.
          </p>
        </div>

        <button
          type="button"
          class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2 shrink-0 self-start sm:self-auto cursor-pointer"
          @click="openAddModal"
        >
          <i class="ri-user-add-line text-sm" />
          <span>Register Fleet Owner</span>
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

      <!-- Search & Filters Toolbar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search owners by name, email, company, phone..."
            class="w-full py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-400 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            style="padding-left: 2rem; padding-right: 0.75rem"
            @input="onSearchInput"
            @keyup.enter="applyFilters"
          >
        </div>

        <span class="text-xs font-bold text-slate-400">
          Total Registered: {{ props.owners?.total ?? ownersList.length }}
        </span>
      </div>

      <!-- Main Display Table -->
      <OwnerTable
        :owners="ownersList"
        :pagination="props.owners"
        @view="openDetailModal"
        @edit="openEditModal"
        @delete="deleteOwner"
      />

      <!-- Create Owner Modal -->
      <OwnerFormModal
        :show="showAddModal"
        :is-editing="false"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showAddModal = false"
        @save="submitNewOwner"
      />

      <!-- Edit Owner Modal -->
      <OwnerFormModal
        :owner="editingOwner"
        :show="showEditModal"
        is-editing
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showEditModal = false"
        @save="submitEditOwner"
      />

      <!-- Owner Details Overview Modal -->
      <OwnerDetailModal
        :show="showDetailModal"
        :owner="viewingOwner"
        @close="showDetailModal = false"
        @edit="openEditModal"
      />

      <!-- Password Setup Modal -->
      <OwnerPasswordModal
        :show="showPasswordModal"
        :url="generatedResetUrl"
        @close="showPasswordModal = false"
      />
    </div>
  </AdminLayout>
</template>
