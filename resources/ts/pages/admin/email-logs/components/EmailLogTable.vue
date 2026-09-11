<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { EmailLogItem } from '../types'

defineProps<{
  logs: EmailLogItem[]
  pagination?: any
}>()

const emit = defineEmits<{
  (e: 'view-details', log: EmailLogItem): void
  (e: 'preview', log: EmailLogItem): void
  (e: 'delete', id: number): void
}>()

const formatDate = (dateStr?: string | null) => {
  if (!dateStr) return 'N/A'
  try {
    const d = new Date(dateStr)
    
    return d.toLocaleString('en-US', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return dateStr
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
              Log ID & Sent Time
            </th>
            <th class="py-3.5 px-4">
              Recipient (To)
            </th>
            <th class="py-3.5 px-4">
              Subject
            </th>
            <th class="py-3.5 px-4">
              Sender (Triggered By)
            </th>
            <th class="py-3.5 px-4">
              Status
            </th>
            <th class="py-3.5 px-4 text-right">
              Actions
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
              <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ formatDate(log.sent_at || log.created_at) }}</span>
            </td>

            <!-- Recipient -->
            <td class="py-3 px-4 max-w-[200px]">
              <span
                class="font-mono text-indigo-600 dark:text-indigo-400 font-bold block truncate"
                :title="log.to"
              >
                {{ log.to }}
              </span>
              <span
                v-if="log.from"
                class="text-[10px] text-slate-400 truncate block"
              >
                From: {{ log.from }}
              </span>
            </td>

            <!-- Subject -->
            <td class="py-3 px-4 max-w-xs">
              <p
                class="font-bold text-slate-800 dark:text-slate-200 truncate"
                :title="log.subject"
              >
                {{ log.subject }}
              </p>
              <span
                v-if="log.mailable_class"
                class="text-[10px] font-mono text-slate-400 truncate block"
              >
                {{ log.mailable_class.split('\\').pop() }}
              </span>
            </td>

            <!-- Sender / Actor -->
            <td class="py-3 px-4">
              <div class="flex flex-col gap-0.5">
                <span class="font-bold text-slate-900 dark:text-white truncate max-w-[130px]">
                  {{ log.sender_name || 'System' }}
                </span>
                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded border w-fit bg-slate-50 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                  {{ log.sender_role || 'System' }}
                </span>
              </div>
            </td>

            <!-- Status -->
            <td class="py-3 px-4">
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
            </td>

            <!-- Actions -->
            <td class="py-3 px-4 text-right">
              <div class="inline-flex items-center gap-1.5">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 dark:hover:bg-indigo-900/80 font-bold text-[11px] cursor-pointer inline-flex items-center gap-1 transition-colors"
                  title="Preview rendered email"
                  @click="emit('preview', log)"
                >
                  <i class="ri-mail-open-line" />
                  <span>Preview</span>
                </button>

                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 font-bold text-[11px] cursor-pointer inline-flex items-center gap-1 transition-colors"
                  title="View full dispatch record"
                  @click="emit('view-details', log)"
                >
                  <i class="ri-information-line" />
                  <span>Details</span>
                </button>

                <button
                  type="button"
                  class="w-7 h-7 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 dark:hover:bg-rose-900/80 flex items-center justify-center cursor-pointer transition-colors"
                  title="Delete email log"
                  @click="emit('delete', log.id)"
                >
                  <i class="ri-delete-bin-line" />
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="logs.length === 0">
            <td
              colspan="6"
              class="py-12 text-center text-slate-400"
            >
              <i class="ri-mail-check-line text-3xl mb-2 block opacity-40" />
              No email log records found matching criteria.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      v-if="pagination && pagination.links && pagination.links.length > 3"
      class="flex items-center justify-between px-2 pt-2 text-xs"
    >
      <span class="text-slate-400">
        Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total || 0 }} emails
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
