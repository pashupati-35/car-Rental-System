<script setup lang="ts">
import type { EmailLogItem } from '../types'

defineProps<{
  show: boolean
  log: EmailLogItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()
</script>

<template>
  <div
    v-if="show && log"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-3xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4 my-8 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2">
          <i class="ri-mail-open-line text-indigo-600 text-lg" />
          <h3 class="font-bold text-base text-slate-900 dark:text-white truncate max-w-lg">
            Preview: {{ log.subject }}
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

      <!-- Email Headers Brief -->
      <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1.5 text-xs">
        <div class="flex items-center justify-between">
          <div>
            <span class="text-slate-400 font-bold block text-[10px] uppercase">From</span>
            <span class="text-slate-900 dark:text-white font-mono">{{ log.from || 'noreply@autorent.com' }}</span>
          </div>
          <div class="text-right">
            <span class="text-slate-400 font-bold block text-[10px] uppercase">Status</span>
            <span
              class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
              :class="[
                log.status === 'sent'
                  ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'
                  : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300'
              ]"
            >
              {{ log.status }}
            </span>
          </div>
        </div>

        <div>
          <span class="text-slate-400 font-bold block text-[10px] uppercase">To (Recipient)</span>
          <span class="text-indigo-600 dark:text-indigo-400 font-mono font-bold">{{ log.to }}</span>
        </div>

        <div v-if="log.cc">
          <span class="text-slate-400 font-bold block text-[10px] uppercase">CC</span>
          <span class="text-slate-600 dark:text-slate-300 font-mono">{{ log.cc }}</span>
        </div>
      </div>

      <!-- Email Rendered Body -->
      <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white p-4 min-h-[250px] max-h-[500px] overflow-y-auto text-slate-800 text-sm">
        <div
          v-if="log.body || log.content"
          class="prose prose-sm max-w-none dark:prose-invert"
          v-html="log.body || log.content"
        />
        <div
          v-else
          class="text-slate-400 text-center py-12"
        >
          No email body content available for this record.
        </div>
      </div>

      <div class="flex justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
        <button
          type="button"
          class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer"
          @click="emit('close')"
        >
          Close Preview
        </button>
      </div>
    </div>
  </div>
</template>
