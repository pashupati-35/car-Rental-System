<script setup lang="ts">
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

interface LinkItem {
  url: string | null
  label: string
  active: boolean
}

const props = withDefaults(
  defineProps<{
    links?: LinkItem[]
    from?: number
    to?: number
    total?: number
    currentPage?: number
    lastPage?: number
    perPage?: number
    perPageOptions?: number[]
  }>(),
  {
    from: 1,
    to: 10,
    total: 0,
    currentPage: 1,
    lastPage: 1,
    perPage: 25,
    perPageOptions: () => [10, 25, 50, 100],
  },
)

const emit = defineEmits<{
  (e: 'page-change', page: number): void
  (e: 'update:perPage', perPage: number): void
}>()

// Calculate current page & last page if not directly provided
const computedCurrentPage = computed(() => {
  if (props.currentPage) return props.currentPage
  if (props.links && props.links.length > 0) {
    const activeLink = props.links.find(l => l.active)
    if (activeLink) {
      const page = parseInt(activeLink.label, 10)
      if (!isNaN(page)) return page
    }
  }
  
  return 1
})

const computedLastPage = computed(() => {
  if (props.lastPage) return props.lastPage
  if (props.total && props.perPage) {
    return Math.ceil(props.total / props.perPage)
  }
  if (props.links && props.links.length > 0) {
    const numericLinks = props.links
      .map(l => parseInt(l.label, 10))
      .filter(n => !isNaN(n))

    if (numericLinks.length > 0) {
      return Math.max(...numericLinks)
    }
  }
  
  return 1
})

// Generate visible page numbers with ellipsis (e.g. 1 2 3 4 5 6 ... 21)
const visiblePages = computed(() => {
  const current = computedCurrentPage.value
  const total = computedLastPage.value
  const delta = 2
  const range: (number | string)[] = []

  if (total <= 7) {
    for (let i = 1; i <= total; i++) range.push(i)
    
    return range
  }

  const left = current - delta
  const right = current + delta + 1
  const rangeWithDots: (number | string)[] = []
  let l: number | undefined

  for (let i = 1; i <= total; i++) {
    if (i === 1 || i === total || (i >= left && i < right)) {
      range.push(i)
    }
  }

  for (const i of range) {
    if (l !== undefined) {
      if (typeof i === 'number' && i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (typeof i === 'number' && i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    if (typeof i === 'number') l = i
  }

  return rangeWithDots
})

const goToPage = (page: number) => {
  if (page < 1 || page > computedLastPage.value || page === computedCurrentPage.value) return

  emit('page-change', page)

  // If using Inertia pagination via URL
  if (props.links && props.links.length > 0) {
    const targetLink = props.links.find(
      l => l.label === String(page) || (page === 1 && l.label.includes('Previous')),
    )

    if (targetLink && targetLink.url) {
      router.visit(targetLink.url, { preserveScroll: true, preserveState: true })
      
      return
    }

    // Try finding by page query parameter
    const currentUrl = new URL(window.location.href)

    currentUrl.searchParams.set('page', String(page))
    router.visit(currentUrl.toString(), { preserveScroll: true, preserveState: true })
  }
}

const onPerPageChange = (event: Event) => {
  const select = event.target as HTMLSelectElement
  const val = parseInt(select.value, 10)

  emit('update:perPage', val)

  const currentUrl = new URL(window.location.href)

  currentUrl.searchParams.set('per_page', String(val))
  currentUrl.searchParams.set('page', '1')
  router.visit(currentUrl.toString(), { preserveScroll: true, preserveState: true })
}
</script>

<template>
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-5 py-3.5 bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 text-xs select-none">
    <!-- Left: Showing 1 – 25 of 518 [25 v] -->
    <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400 font-medium">
      <div>
        Showing
        <span class="font-bold text-slate-900 dark:text-white">{{ from || 1 }} – {{ to || Math.min(perPage, total || 0) }}</span>
        of
        <span class="font-bold text-slate-900 dark:text-white">{{ total }}</span>
      </div>

      <!-- Per page dropdown matching 3rd screenshot -->
      <div class="relative">
        <select
          :value="perPage"
          class="appearance-none px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer shadow-2xs"
          style="padding-right: 1.5rem"
          @change="onPerPageChange"
        >
          <option
            v-for="opt in perPageOptions"
            :key="opt"
            :value="opt"
          >
            {{ opt }}
          </option>
        </select>
        <span class="pointer-events-none absolute inset-y-0 right-1.5 flex items-center text-slate-400 text-[10px]">
          ▼
        </span>
      </div>
    </div>

    <!-- Right: |< < 1 2 3 4 5 6 ... 21 > >| -->
    <div class="flex items-center gap-1">
      <!-- First Page Button |< -->
      <button
        type="button"
        :disabled="computedCurrentPage <= 1"
        title="First Page"
        class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200/70 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-[11px] font-semibold"
        @click="goToPage(1)"
      >
        <i class="ri-skip-back-line text-xs" />
      </button>

      <!-- Previous Page Button < -->
      <button
        type="button"
        :disabled="computedCurrentPage <= 1"
        title="Previous Page"
        class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200/70 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-[11px] font-semibold"
        @click="goToPage(computedCurrentPage - 1)"
      >
        <i class="ri-arrow-left-s-line text-sm" />
      </button>

      <!-- Page Numbers -->
      <template
        v-for="(page, idx) in visiblePages"
        :key="idx"
      >
        <span
          v-if="page === '...'"
          class="w-7 h-7 flex items-center justify-center text-slate-400 text-xs font-bold"
        >
          ...
        </span>
        <button
          v-else
          type="button"
          :class="[
            page === computedCurrentPage
              ? 'bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 font-extrabold shadow-2xs'
              : 'border border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'
          ]"
          class="min-w-7 h-7 px-1.5 flex items-center justify-center rounded-lg text-xs transition-colors cursor-pointer"
          @click="goToPage(Number(page))"
        >
          {{ page }}
        </button>
      </template>

      <!-- Next Page Button > -->
      <button
        type="button"
        :disabled="computedCurrentPage >= computedLastPage"
        title="Next Page"
        class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200/70 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-[11px] font-semibold"
        @click="goToPage(computedCurrentPage + 1)"
      >
        <i class="ri-arrow-right-s-line text-sm" />
      </button>

      <!-- Last Page Button >| -->
      <button
        type="button"
        :disabled="computedCurrentPage >= computedLastPage"
        title="Last Page"
        class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200/70 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-[11px] font-semibold"
        @click="goToPage(computedLastPage)"
      >
        <i class="ri-skip-forward-line text-xs" />
      </button>
    </div>
  </div>
</template>
