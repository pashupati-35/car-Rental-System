<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { DriverItem } from './types'
import DriverTable from './components/DriverTable.vue'
import DriverFormModal from './components/DriverFormModal.vue'

const props = defineProps<{
  drivers: any
  owners?: Array<any>
  filters?: {
    search?: string
    per_page?: number
  }
}>()

const driversList = computed<DriverItem[]>(() => {
  if (Array.isArray(props.drivers)) return props.drivers
  
  return props.drivers?.data || []
})

const ownersList = ref<Array<any>>(props.owners || [])
const searchQuery = ref(props.filters?.search || '')
const selectedOwnerFilter = ref('')

const showAddModal = ref(false)
const showEditModal = ref(false)
const submitting = ref(false)
const errorMessage = ref('')
const message = ref('')

const driverForm = ref<Partial<DriverItem>>({
  id: 0,
  name: '',
  phone: '',
  email: '',
  license_number: '',
  experience_years: 1,
  status: 'active',
  owner_id: '',
  address: '',
})

const filteredDrivers = computed<DriverItem[]>(() => {
  let list = driversList.value

  if (selectedOwnerFilter.value) {
    list = list.filter((d: DriverItem) => String(d.owner_id) === String(selectedOwnerFilter.value))
  }

  if (searchQuery.value.trim() && !props.filters?.search) {
    const q = searchQuery.value.toLowerCase()

    list = list.filter((d: DriverItem) => {
      const name = d.name || ''
      const phone = d.phone || ''
      const license = d.license_number || ''
      const owner = d.owner?.full_name || d.owner?.name || ''
      
      return name.toLowerCase().includes(q) || phone.toLowerCase().includes(q) || license.toLowerCase().includes(q) || owner.toLowerCase().includes(q)
    })
  }

  return list
})

const handleSearch = () => {
  router.get('/admin/drivers', {
    search: searchQuery.value,
  }, { preserveState: true, preserveScroll: true })
}

const openAddModal = () => {
  driverForm.value = {
    id: 0,
    name: '',
    phone: '',
    email: '',
    license_number: '',
    experience_years: 1,
    status: 'active',
    owner_id: '',
    address: '',
  }
  errorMessage.value = ''
  showAddModal.value = true
}

const openEditModal = (driver: DriverItem) => {
  driverForm.value = {
    id: driver.id,
    name: driver.name,
    phone: driver.phone,
    email: driver.email || '',
    license_number: driver.license_number || '',
    experience_years: driver.experience_years || 1,
    status: driver.status || 'active',
    owner_id: driver.owner_id || '',
    address: driver.address || '',
  }
  errorMessage.value = ''
  showEditModal.value = true
}

const submitNewDriver = async () => {
  submitting.value = true
  errorMessage.value = ''
  try {
    const res = await axios.post('/admin/drivers', driverForm.value)
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

const submitEditDriver = async () => {
  submitting.value = true
  errorMessage.value = ''
  try {
    const res = await axios.patch(`/admin/drivers/${driverForm.value.id}`, driverForm.value)
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
      <div
        v-if="message"
        class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs"
      >
        <i class="ri-checkbox-circle-fill text-emerald-600 text-base shrink-0" />
        <span>{{ message }}</span>
      </div>

      <!-- Search & Filters Toolbar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
          <div class="relative w-full sm:w-72">
            <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search drivers by name, phone, license..."
              class="w-full py-2 rounded-xl"
              style="padding-left: 2rem; padding-right: 0.75rem border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              @keyup.enter="handleSearch"
            >
          </div>

          <div
            v-if="ownersList.length > 0"
            class="w-full sm:w-60"
          >
            <select
              v-model="selectedOwnerFilter"
              class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
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
        </div>

        <span class="text-xs font-bold text-slate-400">
          Total Chauffeurs: {{ props.drivers?.total ?? filteredDrivers.length }}
        </span>
      </div>

      <!-- Main Display Table -->
      <DriverTable
        :drivers="filteredDrivers"
        :pagination="props.drivers"
        @edit="openEditModal"
        @delete="deleteDriver"
      />

      <!-- Create Driver Modal -->
      <DriverFormModal
        v-model:form="driverForm"
        :show="showAddModal"
        :is-editing="false"
        :owners="ownersList"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showAddModal = false"
        @save="submitNewDriver"
      />

      <!-- Edit Driver Modal -->
      <DriverFormModal
        v-model:form="driverForm"
        :show="showEditModal"
        is-editing
        :owners="ownersList"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showEditModal = false"
        @save="submitEditDriver"
      />
    </div>
  </AdminLayout>
</template>
