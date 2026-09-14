<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { DriverItem } from './types'
import DriverTable from './components/DriverTable.vue'
import DriverFormModal from './components/DriverFormModal.vue'
import DriverDetailModal from './components/DriverDetailModal.vue'
import MessageBox from '@/components/MessageBox.vue'

const props = defineProps<{
  drivers: any
  owners?: Array<any>
  counts?: {
    all?: number
    active?: number
    inactive?: number
  }
  filters?: {
    search?: string
    owner_id?: string
    status?: string
    per_page?: number
  }
}>()

const driversList = computed<DriverItem[]>(() => {
  if (Array.isArray(props.drivers)) return props.drivers
  
  return props.drivers?.data || []
})

const ownersList = computed<Array<any>>(() => props.owners || [])
const searchQuery = ref(props.filters?.search || '')
const selectedOwnerFilter = ref(props.filters?.owner_id || '')
const selectedStatusFilter = ref(props.filters?.status || 'all')

const showAddModal = ref(false)
const showEditModal = ref(false)
const showDetailModal = ref(false)
const selectedDriver = ref<DriverItem | null>(null)
const submitting = ref(false)
const errorMessage = ref('')
const message = ref('')



let searchTimeout: any = null

const applyFilters = () => {
  router.get('/admin/drivers', {
    search: searchQuery.value || undefined,
    owner_id: selectedOwnerFilter.value || undefined,
    status: selectedStatusFilter.value || undefined,
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

const onOwnerChange = () => {
  applyFilters()
}

const setStatus = (status: string) => {
  selectedStatusFilter.value = status === 'all' ? '' : status
  applyFilters()
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedOwnerFilter.value = ''
  selectedStatusFilter.value = 'all'
  applyFilters()
}

const editingDriver = ref<DriverItem | null>(null)

const openAddModal = () => {
  editingDriver.value = null
  errorMessage.value = ''
  showAddModal.value = true
}

const openEditModal = (driver: DriverItem) => {
  editingDriver.value = driver
  errorMessage.value = ''
  showEditModal.value = true
}

const openDetailModal = (driver: DriverItem) => {
  selectedDriver.value = driver
  showDetailModal.value = true
}

const handleEditFromDetail = (driver: DriverItem) => {
  showDetailModal.value = false
  openEditModal(driver)
}

const submitNewDriver = async (formData: FormData) => {
  submitting.value = true
  errorMessage.value = ''
  try {
    const res = await axios.post('/admin/drivers', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    if (res.data?.status === 'success' || res.status === 200 || res.status === 201) {
      message.value = 'Chauffeur registered successfully!'
      showAddModal.value = false
      router.reload({ only: ['drivers'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to register driver.'
  } finally {
    submitting.value = false
  }
}

const submitEditDriver = async (formData: FormData) => {
  if (!editingDriver.value?.id) return
  submitting.value = true
  errorMessage.value = ''
  try {
    const res = await axios.post(`/admin/drivers/${editingDriver.value.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Driver details updated.'
      showEditModal.value = false
      router.reload({ only: ['drivers'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to update driver.'
  } finally {
    submitting.value = false
  }
}

const deleteDriver = async (driverId: number) => {
  if (!confirm('Are you sure you want to permanently delete this chauffeur profile?')) return
  try {
    const res = await axios.delete(`/admin/drivers/${driverId}`)
    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Driver deleted.'
      router.reload({ only: ['drivers'] })
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete driver.')
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Drivers & Chauffeurs Directory - Admin Portal" />

    <div class="space-y-6">
      <!-- Title & Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
              Staff & Chauffeurs
            </span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
              <i class="ri-steering-2-line text-indigo-600" />
              Drivers & Chauffeur Directory
            </h2>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            Manage certified drivers across the platform, link chauffeurs to fleet owners, and verify commercial licenses.
          </p>
        </div>

        <button
          type="button"
          class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2 shrink-0 self-start sm:self-auto cursor-pointer"
          @click="openAddModal"
        >
          <i class="ri-user-add-line text-sm" />
          <span>Add Chauffeur / Driver</span>
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
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex flex-wrap items-center gap-2">
          <!-- Status Tabs -->
          <button
            type="button"
            :class="selectedStatusFilter === 'all' || !selectedStatusFilter ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setStatus('all')"
          >
            <span>All Drivers</span>
            <span
              v-if="props.counts?.all !== undefined"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="(selectedStatusFilter === 'all' || !selectedStatusFilter) ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
            >
              {{ props.counts.all }}
            </span>
          </button>
          <button
            type="button"
            :class="selectedStatusFilter === 'active' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setStatus('active')"
          >
            <span>Active</span>
            <span
              v-if="props.counts?.active !== undefined"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="selectedStatusFilter === 'active' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'"
            >
              {{ props.counts.active }}
            </span>
          </button>
          <button
            type="button"
            :class="selectedStatusFilter === 'inactive' ? 'bg-slate-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setStatus('inactive')"
          >
            <span>Inactive</span>
            <span
              v-if="props.counts?.inactive !== undefined"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="selectedStatusFilter === 'inactive' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
            >
              {{ props.counts.inactive }}
            </span>
          </button>

          <!-- Owner Select -->
          <div
            v-if="ownersList.length > 0"
            class="w-full sm:w-48"
          >
            <select
              v-model="selectedOwnerFilter"
              class="w-full py-1.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 cursor-pointer"
              @change="onOwnerChange"
            >
              <option value="">
                All Fleet Owners
              </option>
              <option
                v-for="owner in ownersList"
                :key="owner.id"
                :value="owner.id"
              >
                {{ owner.full_name || owner.name }}
              </option>
            </select>
          </div>

          <button
            v-if="searchQuery || selectedOwnerFilter || (selectedStatusFilter && selectedStatusFilter !== 'all')"
            type="button"
            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs transition-colors cursor-pointer"
            @click="resetFilters"
          >
            Reset
          </button>
        </div>

        <div class="relative w-full lg:w-64">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search drivers by name, phone, license..."
            class="w-full py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-400 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            style="padding-left: 2rem; padding-right: 0.75rem"
            @input="onSearchInput"
            @keyup.enter="applyFilters"
          >
        </div>
      </div>

      <!-- Main Display Table -->
      <DriverTable
        :drivers="driversList"
        :pagination="props.drivers"
        @edit="openEditModal"
        @delete="deleteDriver"
        @view-details="openDetailModal"
      />

      <!-- Create Driver Modal -->
      <DriverFormModal
        :show="showAddModal"
        :is-editing="false"
        :driver="null"
        :owners="ownersList"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showAddModal = false"
        @save="submitNewDriver"
      />

      <!-- Edit Driver Modal -->
      <DriverFormModal
        :show="showEditModal"
        is-editing
        :driver="editingDriver"
        :owners="ownersList"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showEditModal = false"
        @save="submitEditDriver"
      />

      <!-- Driver Detail Modal -->
      <DriverDetailModal
        :show="showDetailModal"
        :driver="selectedDriver"
        @close="showDetailModal = false"
        @edit="handleEditFromDetail"
      />
    </div>
  </AdminLayout>
</template>
