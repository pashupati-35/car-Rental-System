<script setup lang="ts">
import type { CmsItem } from '../types'

defineProps<{
  show: boolean
  item: CmsItem | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'delete', id: number): void
}>()
</script>

<template>
  <div
    v-if="show && item"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-indigo-600" />
          <h3 class="font-bold text-base text-slate-900 dark:text-white">
            Inquiry & Contact Details
          </h3>
        </div>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <div class="space-y-3 text-xs">
        <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl">
          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Full Name</span>
            <span class="font-bold text-slate-900 dark:text-white">{{ item.name || 'N/A' }}</span>
          </div>
          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Email Address</span>
            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ item.email || 'N/A' }}</span>
          </div>
          <div v-if="item.phone || item.mobile">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Phone Number</span>
            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ item.phone || item.mobile }}</span>
          </div>
          <div v-if="item.created_at">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Received On</span>
            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ new Date(item.created_at).toLocaleString() }}</span>
          </div>
        </div>

        <div v-if="item.subject">
          <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1">Subject</span>
          <div class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-bold text-slate-900 dark:text-white">
            {{ item.subject }}
          </div>
        </div>

        <div>
          <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1">Message Body</span>
          <div class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 whitespace-pre-line max-h-48 overflow-y-auto leading-relaxed">
            {{ item.message || item.description || item.content || 'No message content provided.' }}
          </div>
        </div>
      </div>

      <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-slate-800">
        <button
          type="button"
          class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
          @click="emit('delete', item.id); emit('close');"
        >
          Delete Message
        </button>
        <button
          type="button"
          class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-xs cursor-pointer"
          @click="emit('close')"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>
