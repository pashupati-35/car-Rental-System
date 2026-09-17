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
    pagination?: any
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
    pagination: undefined,
    links: undefined,
    from: undefined,
    to: undefined,
    total: undefined,
    currentPage: undefined,
    lastPage: undefined,
    perPage: undefined,
    perPageOptions: () => [6, 10, 12, 15, 20, 25, 50, 100],
  },
)

const emit = defineEmits<{
  (e: 'page-change', page: number): void
  (e: 'update:perPage', perPage: number): void
}>()

const rawSource = computed(() => props.pagination || {})
const meta = computed(() => rawSource.value.meta || {})

const normalizedLinks = computed<LinkItem[]>(() => {
  if (Array.isArray(props.links) && props.links.length > 0) return props.links
  if (Array.isArray(rawSource.value.links)) return rawSource.value.links
  if (Array.isArray(meta.value.links)) return meta.value.links

  return []
})

const effectiveTotal = computed(() => {
  if (typeof props.total === 'number') return props.total
  if (typeof rawSource.value.total === 'number') return rawSource.value.total
  if (typeof meta.value.total === 'number') return meta.value.total

  return 0
})

const effectivePerPage = computed(() => {
  if (typeof props.perPage === 'number' && props.perPage > 0) return props.perPage
  if (typeof rawSource.value.per_page === 'number') return rawSource.value.per_page
  if (typeof meta.value.per_page === 'number') return meta.value.per_page

  if (typeof window !== 'undefined') {
    const urlParam = new URLSearchParams(window.location.search).get('per_page')
    const parsed = parseInt(urlParam || '', 10)
    if (!isNaN(parsed) && parsed > 0) return parsed
  }

  return 10
})

const computedPerPageOptions = computed(() => {
  const opts = [...props.perPageOptions]
  if (effectivePerPage.value && !opts.includes(effectivePerPage.value)) {
    opts.push(effectivePerPage.value)
    opts.sort((a, b) => a - b)
  }

  return opts
})

const effectiveFrom = computed(() => {
  if (effectiveTotal.value === 0) return 0
  if (typeof props.from === 'number' && props.from > 0) return props.from
  if (typeof rawSource.value.from === 'number' && rawSource.value.from > 0) return rawSource.value.from
  if (typeof meta.value.from === 'number' && meta.value.from > 0) return meta.value.from

  return 1
})

const effectiveTo = computed(() => {
  if (effectiveTotal.value === 0) return 0
  if (typeof props.to === 'number' && props.to > 0) return props.to
  if (typeof rawSource.value.to === 'number' && rawSource.value.to > 0) return rawSource.value.to
  if (typeof meta.value.to === 'number' && meta.value.to > 0) return meta.value.to

  return Math.min(effectivePerPage.value, effectiveTotal.value)
})

// Calculate current page & last page properly
const computedCurrentPage = computed(() => {
  if (normalizedLinks.value.length > 0) {
    const activeLink = normalizedLinks.value.find((item: LinkItem) => item.active)
    if (activeLink) {
      const page = parseInt(activeLink.label, 10)
      if (!isNaN(page) && page > 0) return page
    }
  }

  if (props.currentPage && props.currentPage > 0) return props.currentPage
  if (rawSource.value.current_page && rawSource.value.current_page > 0) return rawSource.value.current_page
  if (meta.value.current_page && meta.value.current_page > 0) return meta.value.current_page

  if (typeof window !== 'undefined') {
    const urlParam = new URLSearchParams(window.location.search).get('page')
    const parsed = parseInt(urlParam || '', 10)
    if (!isNaN(parsed) && parsed >= 1) return parsed
  }

  return 1
})

const computedLastPage = computed(() => {
  if (props.lastPage && props.lastPage > 0) return props.lastPage
  if (rawSource.value.last_page && rawSource.value.last_page > 0) return rawSource.value.last_page
  if (meta.value.last_page && meta.value.last_page > 0) return meta.value.last_page

  if (effectiveTotal.value > 0 && effectivePerPage.value > 0) {
    return Math.max(1, Math.ceil(effectiveTotal.value / effectivePerPage.value))
  }

  if (normalizedLinks.value.length > 0) {
    const numericLinks = normalizedLinks.value
      .map((item: LinkItem) => parseInt(item.label, 10))
      .filter((n: number) => !isNaN(n) && n > 0)

    if (numericLinks.length > 0) {
      return Math.max(1, ...numericLinks)
    }
  }

  return 1
})

// Generate visible page numbers with ellipsis (e.g. 1 2 3 4 5 6 ... 21)
const visiblePages = computed(() => {
  const current = computedCurrentPage.value
  const total = computedLastPage.value
  const delta = 2
  const range: number[] = []

  if (total <= 7) {
    for (let i = 1; i <= total; i++) range.push(i)

    return range
  }

  const left = current - delta
  const right = current + delta + 1
  const rangeWithDots: (number | string)[] = []
  let prevNumber: number | undefined

  for (let i = 1; i <= total; i++) {
    if (i === 1 || i === total || (i >= left && i < right)) {
      range.push(i)
    }
  }

  for (const i of range) {
    if (typeof prevNumber === 'number') {
      const diff = i - prevNumber
      if (diff === 2) {
        rangeWithDots.push(prevNumber + 1)
      } else if (diff > 2) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    prevNumber = i
  }

  return rangeWithDots
})

const goToPage = (page: number) => {
  if (page < 1 || page > computedLastPage.value || page === computedCurrentPage.value) return

  emit('page-change', page)

  // If using Inertia pagination with direct link url
  if (normalizedLinks.value.length > 0) {
    const targetLink = normalizedLinks.value.find(
      (item: LinkItem) => item.label === String(page),
    )

    if (targetLink && targetLink.url) {
      router.visit(targetLink.url, { preserveScroll: true, preserveState: true })

      return
    }
  }

  // Fallback to URL search parameter navigation
  const currentUrl = new URL(window.location.href)

  currentUrl.searchParams.set('page', String(page))
  if (effectivePerPage.value) {
    currentUrl.searchParams.set('per_page', String(effectivePerPage.value))
  }
  router.visit(currentUrl.toString(), { preserveScroll: true, preserveState: true })
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
        <span class="font-bold text-slate-900 dark:text-white">{{ effectiveFrom }} – {{ effectiveTo }}</span>
        of
        <span class="font-bold text-slate-900 dark:text-white">{{ effectiveTotal }}</span>
      </div>

      <!-- Per page dropdown matching 3rd screenshot -->
      <div class="relative">
        <select
          :value="effectivePerPage"
          class="appearance-none px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer shadow-2xs"
          style="padding-right: 1.5rem"
          @change="onPerPageChange"
        >
          <option
            v-for="opt in computedPerPageOptions"
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
