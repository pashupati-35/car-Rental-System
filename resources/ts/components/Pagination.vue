<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps<{
  links?: Array<{ url: string | null; label: string; active: boolean }>
  from?: number
  to?: number
  total?: number
  currentPage?: number
  lastPage?: number
}>()

const emit = defineEmits<{
  (e: 'page-change', page: number): void
}>()

const cleanLabel = (label: string) => {
  if (label.includes('Previous') || label.includes('&laquo;')) return '« Prev'
  if (label.includes('Next') || label.includes('&raquo;')) return 'Next »'
  return label
}

const handleCustomClick = (url: string | null, label: string) => {
  if (!url) return
  const urlObj = new URL(url, window.location.origin)
  const pageNum = urlObj.searchParams.get('page')
  if (pageNum) {
    emit('page-change', parseInt(pageNum, 10))
  }
}
</script>

<template>
  <div v-if="(total && total > 0) || (links && links.length > 3)" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 text-xs">
    <div class="text-slate-500 text-[11px] font-medium">
      <template v-if="total">
        Showing <span class="font-bold text-slate-900 dark:text-white">{{ from || 1 }}</span> to <span class="font-bold text-slate-900 dark:text-white">{{ to || total }}</span> of <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ total }}</span> results
      </template>
    </div>

    <!-- Links Navigation -->
    <div v-if="links && links.length > 3" class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0">
      <template v-for="(link, i) in links" :key="i">
        <!-- Disabled or empty link -->
        <span
          v-if="!link.url"
          class="px-2.5 py-1.5 rounded-lg text-slate-300 dark:text-slate-700 text-xs select-none cursor-not-allowed border border-transparent font-medium"
          v-html="cleanLabel(link.label)"
        />

        <!-- Active Inertia Link -->
        <Link
          v-else
          :href="link.url"
          preserve-scroll
          preserve-state
          :class="link.active ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200/60 dark:border-slate-700 font-semibold'"
          class="px-3 py-1.5 rounded-lg text-xs transition-colors"
          @click="handleCustomClick(link.url, link.label)"
          v-html="cleanLabel(link.label)"
        />
      </template>
    </div>
  </div>
</template>
