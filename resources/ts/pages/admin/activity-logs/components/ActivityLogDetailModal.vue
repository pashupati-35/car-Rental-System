<script setup lang="ts">
import type { ActivityLogItem } from '../types'

defineProps<{
  show: boolean
  log: ActivityLogItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const formatJson = (data: any) => {
  if (!data) return 'None'
  try {
    return JSON.stringify(data, null, 2)
  } catch (e) {
    return String(data)
  }
}

const formatDate = (dateStr?: string) => {
  if (!dateStr) return 'N/A'
  try {
    const d = new Date(dateStr)
    return d.toLocaleString('en-US', {
      year: 'numeric',
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
</script>

<template>
  <div
    v-if="show && log"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4 my-8 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-indigo-600" />
          <h3 class="font-bold text-base text-slate-900 dark:text-white">
            Activity Log Record #{{ log.id }}
          </h3>
        </div>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <div class="space-y-4 text-xs">
        <!-- Main Highlight Card -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-2.5">
          <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Log Action / Type</span>
            <span
              :class="[
                'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                log.log_type === 'login' || log.log_type === 'create'
                  ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'
                  : log.log_type === 'delete'
                  ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300'
                  : log.log_type === 'update'
                  ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300'
                  : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'
              ]"
            >
              {{ log.log_type }}
            </span>
          </div>

          <div class="text-slate-900 dark:text-white font-medium text-sm">
            {{ log.description || 'System event recorded' }}
          </div>

          <div class="text-slate-400 text-[11px] font-mono">
            Timestamp: {{ formatDate(log.created_at) }}
          </div>
        </div>

        <!-- Actor / Causer Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Triggered By (Actor)</span>
            <div class="font-bold text-slate-900 dark:text-white">
              {{ log.causer_name || 'System / Guest' }}
            </div>
            <div class="text-slate-500 dark:text-slate-400 text-[11px]">
              Role: <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ log.causer_role || 'System' }}</span>
            </div>
            <div v-if="log.causer?.email" class="text-slate-400 text-[11px] font-mono truncate">
              {{ log.causer.email }}
            </div>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Target Subject / Resource</span>
            <div class="font-bold text-slate-900 dark:text-white truncate">
              {{ log.table_name || (log.subject_type ? log.subject_type.split('\\').pop() : 'General System') }}
            </div>
            <div class="text-slate-500 dark:text-slate-400 text-[11px]">
              Subject ID: <span class="font-mono">{{ log.subject_id ?? 'N/A' }}</span>
            </div>
            <div v-if="log.subject_type" class="text-slate-400 text-[10px] font-mono truncate">
              {{ log.subject_type }}
            </div>
          </div>
        </div>

        <!-- Network Context -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IP Address</span>
            <span class="font-mono text-slate-800 dark:text-slate-200 font-bold text-xs">{{ log.ip_address || '127.0.0.1' }}</span>
          </div>

          <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1 overflow-hidden">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">User Agent</span>
            <span class="text-slate-600 dark:text-slate-400 text-[10px] font-mono line-clamp-2 block" :title="log.user_agent || ''">
              {{ log.user_agent || 'Unknown Client' }}
            </span>
          </div>
        </div>

        <!-- Payload Properties (Changes, Context) -->
        <div class="space-y-1.5">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Logged Payload & State Properties</span>
          <div class="p-3.5 rounded-2xl bg-slate-900 text-emerald-400 font-mono text-[11px] overflow-x-auto max-h-60">
            <pre class="whitespace-pre-wrap">{{ formatJson(log.properties) }}</pre>
          </div>
        </div>
      </div>

      <div class="flex justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
        <button
          type="button"
          class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer"
          @click="emit('close')"
        >
          Close Record
        </button>
      </div>
    </div>
  </div>
</template>
