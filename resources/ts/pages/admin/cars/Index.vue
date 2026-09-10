<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { CarItem } from './types'
import CarTable from './components/CarTable.vue'
import CarDetailModal from './components/CarDetailModal.vue'

const props = defineProps<{
  cars: any
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
const showDetailModal = ref(false)

const carsList = computed<CarItem[]>(() => {
  if (Array.isArray(props.cars)) return props.cars
  
  return props.cars?.data || []
})

const filteredCars = computed<CarItem[]>(() => {
  let list = carsList.value

  if (activeTab.value !== 'all' && !props.filters?.status) {
    list = list.filter((c: CarItem) => {
      if (activeTab.value === 'verified') return c.status === 'verified' || c.status === 'available'
      if (activeTab.value === 'pending') return c.status === 'pending'
      if (activeTab.value === 'rejected') return c.status === 'rejected'
      
      return true
    })
  }

  if (searchQuery.value.trim() && !props.filters?.search) {
    const q = searchQuery.value.toLowerCase()

    list = list.filter((c: CarItem) => {
      const name = (c.car_name || c.brand || '') + ' ' + (c.car_model || c.model || '')
      const num = c.car_number || c.plate_number || ''
      const owner = c.owner?.full_name || c.owner?.name || c.owner?.email || ''
      
      return name.toLowerCase().includes(q) || num.toLowerCase().includes(q) || owner.toLowerCase().includes(q)
    })
  }

  return list
})

const setTab = (tab: 'all' | 'pending' | 'verified' | 'rejected') => {
  activeTab.value = tab
  router.get('/admin/cars', {
    status: tab === 'all' ? '' : tab,
    search: searchQuery.value,
  }, { preserveState: true, preserveScroll: true })
}

const handleSearch = () => {
  router.get('/admin/cars', {
    status: activeTab.value === 'all' ? '' : activeTab.value,
    search: searchQuery.value,
  }, { preserveState: true, preserveScroll: true })
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

      <!-- Filter Tabs & Search Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
          <button
            type="button"
            :class="activeTab === 'all' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all"
            @click="setTab('all')"
          >
            All Cars
          </button>
          <button
            type="button"
            :class="activeTab === 'pending' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all flex items-center gap-1"
            @click="setTab('pending')"
          >
            <span>Pending Review</span>
          </button>
          <button
            type="button"
            :class="activeTab === 'verified' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all"
            @click="setTab('verified')"
          >
            Active & Verified
          </button>
          <button
            type="button"
            :class="activeTab === 'rejected' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all"
            @click="setTab('rejected')"
          >
            Rejected
          </button>
        </div>

        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search cars by name, plate, owner..."
            class="w-full py-2 rounded-xl"
            style="padding-left: 2rem; padding-right: 0.75rem border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            @keyup.enter="handleSearch"
          >
        </div>
      </div>

      <!-- Main Display Table -->
      <CarTable
        :cars="filteredCars"
        :pagination="props.cars"
        @view="openDetails"
        @verify="verifyCar"
        @reject="rejectCar"
        @delete="deleteCar"
      />

      <!-- Inspect Car Modal -->
      <CarDetailModal
        :show="showDetailModal"
        :car="selectedCar"
        @close="showDetailModal = false"
        @verify="verifyCar"
        @reject="rejectCar"
        @delete="deleteCar"
      />
    </div>
  </AdminLayout>
</template>
