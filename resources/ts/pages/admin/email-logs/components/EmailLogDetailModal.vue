<script setup lang="ts">
import type { EmailLogItem } from '../types'

defineProps<{
  show: boolean
  log: EmailLogItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'preview', log: EmailLogItem): void
}>()

const formatDate = (dateStr?: string | null) => {
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

const formatJson = (data: any) => {
  if (!data) return 'None'
  try {
    return JSON.stringify(data, null, 2)
  } catch {
    return String(data)
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
            Email Dispatch Record #{{ log.id }}
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

      <div class="space-y-3.5 text-xs">
        <!-- Status and Subject Summary -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status & Protocol</span>
            <span
              class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
              :class="[
                log.status === 'sent'
                  ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'
                  : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300'
              ]"
            >
              {{ log.status }}
            </span>
          </div>

          <h4 class="font-bold text-sm text-slate-900 dark:text-white">
            {{ log.subject }}
          </h4>

          <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-400 font-mono">
            <span>Sent: {{ formatDate(log.sent_at || log.created_at) }}</span>
            <span v-if="log.transport">Transport: {{ log.transport }}</span>
            <span v-if="log.mailable_class">Class: {{ log.mailable_class.split('\\').pop() }}</span>
          </div>
        </div>

        <!-- Sender and Recipient -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sender / Triggered By</span>
            <div class="font-bold text-slate-900 dark:text-white">
              {{ log.sender_name || 'System Automated' }}
            </div>
            <div class="text-slate-500 dark:text-slate-400 text-[11px]">
              Role: <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ log.sender_role || 'System' }}</span>
            </div>
            <div class="text-slate-400 font-mono text-[11px]">
              From: {{ log.from || 'noreply@autorent.com' }}
            </div>
          </div>

          <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Recipient</span>
            <div class="font-bold text-indigo-600 dark:text-indigo-400 font-mono text-sm break-all">
              {{ log.to }}
            </div>
            <div
              v-if="log.cc"
              class="text-slate-500 font-mono text-[11px]"
            >
              CC: {{ log.cc }}
            </div>
            <div
              v-if="log.reply_to"
              class="text-slate-400 font-mono text-[10px]"
            >
              Reply-To: {{ log.reply_to }}
            </div>
          </div>
        </div>

        <!-- Error Message (if failed) -->
        <div
          v-if="log.error_message"
          class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-200 space-y-1"
        >
          <span class="text-[10px] font-bold uppercase tracking-wider block">Error Trace</span>
          <p class="font-mono text-xs">
            {{ log.error_message }}
          </p>
        </div>

        <!-- Network Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IP Address</span>
            <span class="font-mono text-slate-800 dark:text-slate-200 font-bold text-xs">{{ log.ip_address || '127.0.0.1' }}</span>
          </div>

          <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1 overflow-hidden">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">User Agent</span>
            <span class="text-slate-600 dark:text-slate-400 text-[10px] font-mono line-clamp-2 block">
              {{ log.user_agent || 'PHP Mailer / Webhook' }}
            </span>
          </div>
        </div>

        <!-- Headers / Attachments if any -->
        <div
          v-if="log.headers"
          class="space-y-1"
        >
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Raw Headers</span>
          <div class="p-3 rounded-2xl bg-slate-900 text-emerald-400 font-mono text-[10px] overflow-x-auto max-h-32">
            <pre class="whitespace-pre-wrap">{{ formatJson(log.headers) }}</pre>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
        <button
          type="button"
          class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-indigo-500/20 cursor-pointer transition-colors"
          @click="emit('preview', log); emit('close');"
        >
          <i class="ri-mail-open-line" />
          <span>Preview Rendered Email</span>
        </button>

        <button
          type="button"
          class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer"
          @click="emit('close')"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>
