<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { EmailLogItem } from './types'
import EmailLogTable from './components/EmailLogTable.vue'
import EmailLogDetailModal from './components/EmailLogDetailModal.vue'
import EmailLogPreviewModal from './components/EmailLogPreviewModal.vue'

const props = defineProps<{
  logs: any
  counts?: {
    total?: number
    sent?: number
    failed?: number
    today?: number
  }
  filters?: {
    search?: string
    to?: string
    status?: string
    sender_type?: string
    per_page?: number
  }
}>()

const logsList = computed<EmailLogItem[]>(() => {
  if (Array.isArray(props.logs)) return props.logs
  return props.logs?.data || []
})

const searchQuery = ref(props.filters?.search || props.filters?.to || '')
const selectedStatus = ref(props.filters?.status || '')
const selectedSenderType = ref(props.filters?.sender_type || '')

const showDetailModal = ref(false)
const showPreviewModal = ref(false)
const selectedLog = ref<EmailLogItem | null>(null)

let searchTimeout: any = null

const applyFilters = () => {
  router.get('/admin/email-logs', {
    search: searchQuery.value || undefined,
    status: selectedStatus.value || undefined,
    sender_type: selectedSenderType.value || undefined,
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
  selectedStatus.value = ''
  selectedSenderType.value = ''
  applyFilters()
}

const openDetailModal = (log: EmailLogItem) => {
  selectedLog.value = log
  showDetailModal.value = true
}

const openPreviewModal = (log: EmailLogItem) => {
  selectedLog.value = log
  showPreviewModal.value = true
}

const deleteEmailLog = async (id: number) => {
  if (!confirm('Are you sure you want to delete this email dispatch log?')) return
  try {
    const res = await axios.delete(`/admin/email-logs/${id}`)
    if (res.data?.status === 'OK' || res.status === 200) {
      router.reload({ only: ['logs', 'counts'] })
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete email log.')
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Email Logs & Dispatch History - Admin Portal" />

    <div class="space-y-6">
      <!-- Title & Quick Stats -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
              Mail Server History
            </span>
            <span class="text-xs text-slate-400">Outbound Notification Deliveries</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <i class="ri-mail-check-line text-indigo-600" />
            <span>System Email Logs</span>
          </h1>
        </div>

        <!-- Metric badges -->
        <div class="flex flex-wrap items-center gap-2.5">
          <div class="px-3.5 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-2 text-xs">
            <span class="w-2 h-2 rounded-full bg-slate-400" />
            <span class="text-slate-400">Total:</span>
            <span class="font-bold text-slate-900 dark:text-white font-mono">{{ counts?.total ?? logsList.length }}</span>
          </div>
          <div class="px-3.5 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-2 text-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500" />
            <span class="text-slate-400">Sent:</span>
            <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">{{ counts?.sent ?? 0 }}</span>
          </div>
          <div class="px-3.5 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-2 text-xs">
            <span class="w-2 h-2 rounded-full bg-rose-500" />
            <span class="text-slate-400">Failed:</span>
            <span class="font-bold text-rose-600 dark:text-rose-400 font-mono">{{ counts?.failed ?? 0 }}</span>
          </div>
          <div class="px-3.5 py-2 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-2 text-xs">
            <span class="w-2 h-2 rounded-full bg-indigo-500" />
            <span class="text-slate-400">Today:</span>
            <span class="font-bold text-indigo-600 dark:text-indigo-400 font-mono">{{ counts?.today ?? 0 }}</span>
          </div>
        </div>
      </div>

      <!-- Search & Filters -->
      <div class="p-4 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Search -->
          <div class="relative">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search recipient, subject, mailable..."
              class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              @input="onSearchInput"
              @keyup.enter="applyFilters"
            >
          </div>

          <!-- Status Filter -->
          <div>
            <select
              v-model="selectedStatus"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              @change="applyFilters"
            >
              <option value="">
                All Delivery Statuses
              </option>
              <option value="sent">
                Sent Successfully
              </option>
              <option value="failed">
                Failed
              </option>
              <option value="queued">
                Queued / Pending
              </option>
            </select>
          </div>

          <!-- Sender Actor Filter -->
          <div>
            <select
              v-model="selectedSenderType"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              @change="applyFilters"
            >
              <option value="">
                All Senders (Admin, Owner, Customer)
              </option>
              <option value="Admin">
                System Administrators
              </option>
              <option value="Owner">
                Fleet Owners
              </option>
              <option value="Customer">
                Customers
              </option>
            </select>
          </div>

          <!-- Reset Filter -->
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

      <!-- Email Logs Table -->
      <EmailLogTable
        :logs="logsList"
        :pagination="props.logs"
        @view-details="openDetailModal"
        @preview="openPreviewModal"
        @delete="deleteEmailLog"
      />

      <!-- Detail Modal -->
      <EmailLogDetailModal
        :show="showDetailModal"
        :log="selectedLog"
        @close="showDetailModal = false"
        @preview="openPreviewModal"
      />

      <!-- Preview Modal -->
      <EmailLogPreviewModal
        :show="showPreviewModal"
        :log="selectedLog"
        @close="showPreviewModal = false"
      />
    </div>
  </AdminLayout>
</template>
