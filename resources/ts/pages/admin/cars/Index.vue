<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { CarItem } from './types'
import CarTable from './components/CarTable.vue'
import CarDetailModal from './components/CarDetailModal.vue'
import CarEditModal from './components/CarEditModal.vue'
import MessageBox from '@/components/MessageBox.vue'

const props = defineProps<{
  cars: any
  counts?: {
    all?: number
    pending?: number
    verified?: number
    rejected?: number
  }
  owners?: Array<any>
  drivers?: Array<any>
  filters?: {
    status?: string
    search?: string
    per_page?: number
  }
}>()

const activeTab = ref<'all' | 'pending' | 'verified' | 'rejected'>(
  (props.filters?.status as any) || 'all',
)

const searchQuery = ref(props.filters?.search || '')
const selectedCar = ref<CarItem | null>(null)
const editingCar = ref<CarItem | null>(null)
const showDetailModal = ref(false)
const showEditModal = ref(false)
const submitting = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const carsList = computed<CarItem[]>(() => {
  if (Array.isArray(props.cars)) return props.cars
  
  return props.cars?.data || []
})

let searchTimeout: any = null

const applyFilters = () => {
  router.get('/admin/cars', {
    status: activeTab.value === 'all' ? undefined : activeTab.value,
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

const setTab = (tab: 'all' | 'pending' | 'verified' | 'rejected') => {
  activeTab.value = tab
  applyFilters()
}

const verifyCar = (carId: number) => {
  router.patch(`/admin/cars/${carId}/verify`, {}, {
    preserveScroll: true,
  })
}

const rejectCar = (carId: number) => {
  router.patch(`/admin/cars/${carId}/reject`, {}, {
    preserveScroll: true,
  })
}

const deleteCar = (carId: number) => {
  if (!confirm('Are you sure you want to permanently delete this car from the fleet?')) return
  router.delete(`/admin/cars/${carId}`, {
    preserveScroll: true,
  })
}

const openDetails = (car: CarItem) => {
  selectedCar.value = car
  showDetailModal.value = true
}

const openEdit = (car: CarItem) => {
  editingCar.value = car
  errorMessage.value = ''
  showEditModal.value = true
}

const handleSaveCar = async (formData: FormData) => {
  if (!editingCar.value?.id) return

  submitting.value = true
  errorMessage.value = ''

  try {
    const res = await axios.post(`/admin/cars/${editingCar.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    if (res.data?.status === 'success' || res.status === 200) {
      showEditModal.value = false
      successMessage.value = 'Vehicle specifications updated successfully!'
      setTimeout(() => {
        successMessage.value = ''
      }, 4000)
      router.reload({ only: ['cars', 'counts'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to update vehicle details.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Fleet Vehicles & Approval - Admin Portal" />

    <div class="space-y-6">
      <!-- Title Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
              Fleet Management
            </span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
              <i class="ri-car-line text-indigo-600" />
              Fleet Vehicles & Approvals
            </h2>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            Audit newly submitted owner vehicles, verify documentation, approve for customer booking, or manage active inventory.
          </p>
        </div>
      </div>

      <!-- Notifications -->
      <MessageBox
        v-model="successMessage"
        type="success"
      />
      <MessageBox
        v-model="errorMessage"
        type="error"
      />

      <!-- Filter Tabs & Search Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
          <button
            type="button"
            :class="activeTab === 'all' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setTab('all')"
          >
            <span>All Cars</span>
            <span
              v-if="props.counts?.all !== undefined"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
            >
              {{ props.counts.all }}
            </span>
          </button>
          <button
            type="button"
            :class="activeTab === 'pending' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setTab('pending')"
          >
            <span>Pending Review</span>
            <span
              v-if="props.counts?.pending !== undefined"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="activeTab === 'pending' ? 'bg-white/25 text-white' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300'"
            >
              {{ props.counts.pending }}
            </span>
          </button>
          <button
            type="button"
            :class="activeTab === 'verified' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setTab('verified')"
          >
            <span>Active & Verified</span>
            <span
              v-if="props.counts?.verified !== undefined"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="activeTab === 'verified' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'"
            >
              {{ props.counts.verified }}
            </span>
          </button>
          <button
            type="button"
            :class="activeTab === 'rejected' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1.5"
            @click="setTab('rejected')"
          >
            <span>Rejected</span>
            <span
              v-if="props.counts?.rejected !== undefined"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold"
              :class="activeTab === 'rejected' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300'"
            >
              {{ props.counts.rejected }}
            </span>
          </button>
        </div>

        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search cars by name, model, plate, owner..."
            class="w-full py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-400 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            style="padding-left: 2rem; padding-right: 0.75rem"
            @input="onSearchInput"
            @keyup.enter="applyFilters"
          >
        </div>
      </div>

      <!-- Main Display Table -->
      <CarTable
        :cars="carsList"
        :pagination="props.cars"
        @view="openDetails"
        @edit="openEdit"
        @verify="verifyCar"
        @reject="rejectCar"
        @delete="deleteCar"
      />

      <!-- Inspect Car Modal -->
      <CarDetailModal
        :show="showDetailModal"
        :car="selectedCar"
        @close="showDetailModal = false"
        @edit="openEdit"
        @verify="verifyCar"
        @reject="rejectCar"
        @delete="deleteCar"
      />

      <!-- Edit Car Modal -->
      <CarEditModal
        :show="showEditModal"
        :car="editingCar"
        :owners="props.owners"
        :drivers="props.drivers"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showEditModal = false"
        @save="handleSaveCar"
      />
    </div>
  </AdminLayout>
</template>
