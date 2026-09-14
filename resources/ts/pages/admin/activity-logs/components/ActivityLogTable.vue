<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { ActivityLogItem } from '../types'

defineProps<{
  logs: ActivityLogItem[]
  pagination?: any
}>()

const emit = defineEmits<{
  (e: 'view-details', log: ActivityLogItem): void
}>()

const formatDate = (dateStr?: string) => {
  if (!dateStr) return 'N/A'
  try {
    const d = new Date(dateStr)
    
    return d.toLocaleString('en-US', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    })
  } catch {
    return dateStr
  }
}

const getActionBadgeClass = (action: string) => {
  switch (action?.toLowerCase()) {
  case 'login':
  case 'register':
  case 'create':
    return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'
  case 'delete':
    return 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300'
  case 'update':
    return 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300'
  case 'logout':
    return 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300'
  default:
    return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
  }
}

const getRoleBadgeClass = (role?: string) => {
  switch (role) {
  case 'Admin':
    return 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800'
  case 'Fleet Owner':
    return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
  case 'Customer':
    return 'bg-violet-50 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300 border-violet-200 dark:border-violet-800'
  default:
    return 'bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'
  }
}
</script>

<template>
  <div class="space-y-4">
    <div class="overflow-x-auto rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
      <table class="w-full text-left text-xs text-slate-600 dark:text-slate-400">
        <thead class="bg-slate-50/80 dark:bg-slate-800/50 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-200/80 dark:border-slate-800 tracking-wider">
          <tr>
            <th class="py-3.5 px-4">
              Log ID & Time
            </th>
            <th class="py-3.5 px-4">
              Action
            </th>
            <th class="py-3.5 px-4">
              Actor (Causer)
            </th>
            <th class="py-3.5 px-4">
              Description
            </th>
            <th class="py-3.5 px-4">
              Target Table
            </th>
            <th class="py-3.5 px-4">
              IP Address
            </th>
            <th class="py-3.5 px-4 text-right">
              Details
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <tr
            v-for="log in logs"
            :key="log.id"
            class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group"
          >
            <!-- ID & Timestamp -->
            <td class="py-3 px-4">
              <span class="font-mono font-bold text-slate-900 dark:text-white block">#{{ log.id }}</span>
              <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ formatDate(log.created_at) }}</span>
            </td>

            <!-- Action Badge -->
            <td class="py-3 px-4">
              <span
                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                :class="[
                  getActionBadgeClass(log.log_type)
                ]"
              >
                {{ log.log_type }}
              </span>
            </td>

            <!-- Actor / Causer -->
            <td class="py-3 px-4">
              <div class="flex flex-col gap-0.5">
                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]">
                  {{ log.causer_name || 'System' }}
                </span>
                <span
                  class="text-[9px] font-bold px-1.5 py-0.2 rounded border w-fit"
                  :class="[
                    getRoleBadgeClass(log.causer_role)
                  ]"
                >
                  {{ log.causer_role || 'System' }}
                </span>
              </div>
            </td>

            <!-- Description -->
            <td class="py-3 px-4 max-w-xs">
              <p
                class="truncate text-slate-700 dark:text-slate-300 font-medium"
                :title="log.description || ''"
              >
                {{ log.description || '-' }}
              </p>
            </td>

            <!-- Target Table -->
            <td class="py-3 px-4">
              <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                {{ log.table_name || (log.subject_type ? log.subject_type.split('\\').pop() : '-') }}
              </span>
            </td>

            <!-- IP Address -->
            <td class="py-3 px-4">
              <span class="font-mono text-[11px] text-slate-600 dark:text-slate-400">
                {{ log.ip_address || '127.0.0.1' }}
              </span>
            </td>

            <!-- Actions -->
            <td class="py-3 px-4 text-right">
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 dark:hover:bg-indigo-900/80 font-bold text-[11px] cursor-pointer inline-flex items-center gap-1 transition-colors"
                @click="emit('view-details', log)"
              >
                <i class="ri-eye-line" />
                <span>View</span>
              </button>
            </td>
          </tr>

          <tr v-if="logs.length === 0">
            <td
              colspan="7"
              class="py-12 text-center text-slate-400"
            >
              <i class="ri-history-line text-3xl mb-2 block opacity-40" />
              No activity log entries found matching criteria.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Links -->
    <div
      v-if="pagination && pagination.links && pagination.links.length > 3"
      class="flex items-center justify-between px-2 pt-2 text-xs"
    >
      <span class="text-slate-400">
        Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} log entries
      </span>
      <div class="flex gap-1">
        <template
          v-for="(link, i) in pagination.links"
          :key="i"
        >
          <Link
            v-if="link.url"
            :href="link.url"
            class="px-3 py-1.5 rounded-xl font-bold transition-colors"
            :class="[
              link.active
                ? 'bg-indigo-600 text-white shadow-xs'
                : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700'
            ]"
          >
            <span v-html="link.label" />
          </Link>
          <span
            v-else
            class="px-3 py-1.5 rounded-xl text-slate-400 opacity-50 cursor-not-allowed border border-slate-200/50 dark:border-slate-700/50"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </div>
</template>
