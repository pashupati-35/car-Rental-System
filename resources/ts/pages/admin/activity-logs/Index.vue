<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { ActivityLogItem } from './types'
import ActivityLogTable from './components/ActivityLogTable.vue'
import ActivityLogDetailModal from './components/ActivityLogDetailModal.vue'

const props = defineProps<{
  logs: any
  logTypes?: string[]
  counts?: {
    total?: number
    today?: number
  }
  filters?: {
    search?: string
    log_type?: string
    causer_type?: string
    per_page?: number
  }
}>()

const logsList = computed<ActivityLogItem[]>(() => {
  if (Array.isArray(props.logs)) return props.logs
  return props.logs?.data || []
})

const searchQuery = ref(props.filters?.search || '')
const selectedLogType = ref(props.filters?.log_type || '')
const selectedCauserType = ref(props.filters?.causer_type || '')

const showDetailModal = ref(false)
const selectedLog = ref<ActivityLogItem | null>(null)

let searchTimeout: any = null

const applyFilters = () => {
  router.get('/admin/activity-logs', {
    search: searchQuery.value || undefined,
    log_type: selectedLogType.value || undefined,
    causer_type: selectedCauserType.value || undefined,
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

const resetFilters = () => {
  searchQuery.value = ''
  selectedLogType.value = ''
  selectedCauserType.value = ''
  applyFilters()
}

const openDetailModal = (log: ActivityLogItem) => {
  selectedLog.value = log
  showDetailModal.value = true
}
</script>

<template>
  <AdminLayout>
    <Head title="System Activity & Audit Logs - Admin Portal" />

    <div class="space-y-6">
      <!-- Title & Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
              Audit Trails
            </span>
            <span class="text-xs text-slate-400">Security & System Events</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <i class="ri-history-line text-indigo-600" />
            <span>Activity Logs Directory</span>
          </h1>
        </div>

        <!-- Quick Stats -->
        <div class="flex items-center gap-2.5">
          <div class="px-3.5 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-2 text-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500" />
            <span class="text-slate-400">Total Logs:</span>
            <span class="font-bold text-slate-900 dark:text-white font-mono">{{ counts?.total ?? logsList.length }}</span>
          </div>
          <div class="px-3.5 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-2 text-xs">
            <span class="w-2 h-2 rounded-full bg-indigo-500" />
            <span class="text-slate-400">Today:</span>
            <span class="font-bold text-indigo-600 dark:text-indigo-400 font-mono">{{ counts?.today ?? 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Filters Bar -->
      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Search -->
          <div class="relative">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search description, IP, table..."
              class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              @input="onSearchInput"
              @keyup.enter="applyFilters"
            >
          </div>

          <!-- Action / Log Type Filter -->
          <div>
            <select
              v-model="selectedLogType"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              @change="applyFilters"
            >
              <option value="">
                All Action Types
              </option>
              <option value="login">
                Login / Auth
              </option>
              <option value="logout">
                Logout
              </option>
              <option value="create">
                Create
              </option>
              <option value="update">
                Update
              </option>
              <option value="delete">
                Delete
              </option>
              <option
                v-for="t in logTypes"
                :key="t"
                :value="t"
              >
                {{ t }}
              </option>
            </select>
          </div>

          <!-- Actor Role Filter -->
          <div>
            <select
              v-model="selectedCauserType"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              @change="applyFilters"
            >
              <option value="">
                All Actor Roles (Admins, Owners, Customers)
              </option>
              <option value="Admin">
                Administrators
              </option>
              <option value="Owner">
                Fleet Owners
              </option>
              <option value="Customer">
                Customers
              </option>
            </select>
          </div>

          <!-- Reset Filter Button -->
          <div>
            <button
              type="button"
              class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer flex items-center justify-center gap-1.5 transition-colors"
              @click="resetFilters"
            >
              <i class="ri-refresh-line" />
              <span>Reset Filters</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Main Activity Logs Table -->
      <ActivityLogTable
        :logs="logsList"
        :pagination="props.logs"
        @view-details="openDetailModal"
      />

      <!-- Activity Log Detail Modal -->
      <ActivityLogDetailModal
        :show="showDetailModal"
        :log="selectedLog"
        @close="showDetailModal = false"
      />
    </div>
  </AdminLayout>
</template>
