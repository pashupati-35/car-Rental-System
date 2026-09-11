<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { resolveMediaUrl } from '@/utils/helpers'
import type { CmsItem, CmsModuleMeta } from '../types'
import type { PaginationMeta } from '@/types/cms/CommonPagination'

const props = defineProps<{
  items: CmsItem[]
  loading: boolean
  activeModule: string
  currentModuleMeta: CmsModuleMeta
  meta?: PaginationMeta
}>()

const emit = defineEmits<{
  (e: 'edit', item: CmsItem): void
  (e: 'delete', id: number): void
  (e: 'toggle-status', item: CmsItem): void
  (e: 'view', item: CmsItem): void
  (e: 'refresh'): void
  (e: 'page-change', page: number): void
  (e: 'per-page-change', perPage: number): void
  (e: 'search-change', search: string): void
}>()

const searchQuery = ref('')
const currentPage = ref(props.meta?.current_page || 1)
const perPage = ref(props.meta?.per_page || 20)

let searchDebounceTimer: ReturnType<typeof setTimeout> | null = null
watch(searchQuery, (newVal: string) => {
  if (searchDebounceTimer) clearTimeout(searchDebounceTimer)
  searchDebounceTimer = setTimeout(() => {
    emit('search-change', newVal)
  }, 300)
})

watch(() => props.meta, (newMeta?: PaginationMeta) => {
  if (newMeta) {
    currentPage.value = newMeta.current_page
    perPage.value = newMeta.per_page
  }
}, { deep: true })

const totalPages = computed(() => {
  if (props.meta) {
    return props.meta.last_page || 1
  }
  
  return 1
})

const totalItems = computed(() => {
  if (props.meta) {
    return props.meta.total || 0
  }
  
  return props.items.length
})

const fromIndex = computed(() => {
  if (props.meta?.from !== undefined && props.meta?.from !== null) {
    return props.meta.from
  }
  
  return (currentPage.value - 1) * perPage.value + 1
})

const toIndex = computed(() => {
  if (props.meta?.to !== undefined && props.meta?.to !== null) {
    return props.meta.to
  }
  
  return Math.min(currentPage.value * perPage.value, totalItems.value)
})

const visiblePages = computed(() => {
  const current = currentPage.value
  const total = totalPages.value
  const delta = 2
  const range: number[] = []

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
    if (typeof l === 'number') {
      const diff = i - l
      if (diff === 2) {
        rangeWithDots.push(l + 1)
      } else if (diff > 2) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  }

  return rangeWithDots
})

const setPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    emit('page-change', page)
  }
}

const onPerPageSelect = () => {
  emit('per-page-change', perPage.value)
}

const getItemImage = (item: CmsItem): string => {
  return (
    resolveMediaUrl(item.image, item.image_path, 'cms') ||
    resolveMediaUrl(item.cover_image, item.cover_image_path, 'cms') ||
    resolveMediaUrl(item.preview_image, item.preview_image_path, 'cms') ||
    resolveMediaUrl(item.banner_image, null, 'cms') ||
    resolveMediaUrl(item.photo, null, 'cms') ||
    resolveMediaUrl(item.avatar, null, 'cms') ||
    resolveMediaUrl(item.logo, null, 'cms')
  )
}

const getItemTitle = (item: CmsItem): string => {
  return item.title || item.name || item.company_name || item.job_title || `Record #${item.id}`
}

const getItemSubtitle = (item: CmsItem): string => {
  return item.designation || item.job_title || item.subject || (item.author_name ? `By ${item.author_name}` : '')
}

const isEnquiryOrContact = computed(() => {
  return props.activeModule === 'enquiries' || props.activeModule === 'contacts'
})

const stripHtml = (html?: string): string => {
  if (!html) return ''
  
  return html.replace(/<[^>]*>?/gm, '').replace(/&nbsp;/g, ' ').trim()
}
</script>

<template>
  <div class="space-y-4">
    <!-- Search & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="relative w-full sm:w-80">
        <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="`Search ${currentModuleMeta.label.toLowerCase()} (server-paginated)...`"
          class="w-full py-2 sm:py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          style="padding-left: 2rem; padding-right: 0.75rem"
        >
      </div>

      <div class="flex items-center justify-between sm:justify-end gap-3">
        <span class="text-xs font-bold text-slate-400">
          Total {{ totalItems }} {{ currentModuleMeta.label }}
        </span>
        <button
          type="button"
          class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs transition-colors cursor-pointer"
          title="Refresh Data"
          @click="emit('refresh')"
        >
          <i
            class="ri-refresh-line"
            :class="loading ? 'animate-spin' : ''"
          />
        </button>
      </div>
    </div>

    <!-- Content Table / View -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
      <!-- Loading State -->
      <div
        v-if="loading"
        class="py-16 text-center"
      >
        <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-2" />
        <p class="text-xs text-slate-500 font-semibold">
          Loading {{ currentModuleMeta.label }}...
        </p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="items.length === 0"
        class="py-16 text-center px-4"
      >
        <i
          :class="currentModuleMeta.icon"
          class="text-4xl text-slate-300 dark:text-slate-700 mb-2 inline-block"
        />
        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
          No records found
        </p>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
          No records are currently listed under this CMS module.
        </p>
      </div>

      <template v-else>
        <!-- Mobile Cards View for Small Screens (xs, sm) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
          <div
            v-for="item in items"
            :key="item.id"
            class="p-4 space-y-3 cursor-pointer hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
            @click="emit('view', item)"
          >
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3">
                <div
                  v-if="getItemImage(item)"
                  class="w-12 h-12 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200"
                >
                  <img
                    :src="getItemImage(item)"
                    :alt="getItemTitle(item)"
                    class="w-full h-full object-cover"
                    @error="(e) => (e.target as HTMLElement).style.display = 'none'"
                  >
                </div>
                <div>
                  <h4 class="font-bold text-sm text-slate-900 dark:text-white">
                    {{ getItemTitle(item) }}
                  </h4>
                  <p
                    v-if="getItemSubtitle(item)"
                    class="text-xs text-slate-400 font-mono"
                  >
                    {{ getItemSubtitle(item) }}
                  </p>
                </div>
              </div>

              <button
                v-if="item.is_active !== undefined"
                type="button"
                :class="item.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 cursor-pointer shrink-0"
                @click.stop="emit('toggle-status', item)"
              >
                <span
                  class="w-1.5 h-1.5 rounded-full"
                  :class="item.is_active ? 'bg-emerald-500' : 'bg-slate-400'"
                />
                <span>{{ item.is_active ? 'Active' : 'Inactive' }}</span>
              </button>
            </div>

            <p
              v-if="item.short_description || item.description || item.message"
              class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2"
            >
              {{ stripHtml(item.short_description || item.description || item.message) }}
            </p>

            <div
              class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800/60 text-xs"
              @click.stop
            >
              <span class="text-[11px] text-slate-400">
                {{ item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A' }}
              </span>
              <div class="flex items-center gap-1.5">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs cursor-pointer inline-flex items-center gap-1"
                  @click="emit('view', item)"
                >
                  <i class="ri-eye-line text-xs" />
                  <span>View</span>
                </button>
                <button
                  v-if="!isEnquiryOrContact"
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200 font-bold text-xs cursor-pointer"
                  @click="emit('edit', item)"
                >
                  Edit
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
                  @click="emit('delete', item.id)"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Table View for Desktop / Tablet Screens -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
              <tr>
                <th class="py-3.5 px-5">
                  Record / Title
                </th>
                <th class="py-3.5 px-5">
                  Details / Slug
                </th>
                <th class="py-3.5 px-5">
                  Status
                </th>
                <th class="py-3.5 px-5">
                  Created Date
                </th>
                <th class="py-3.5 px-5 text-right">
                  Admin Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
              <tr
                v-for="item in items"
                :key="item.id"
                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
                @click="emit('view', item)"
              >
                <!-- Title & Image -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <div
                      v-if="getItemImage(item)"
                      class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200"
                    >
                      <img
                        :src="getItemImage(item)"
                        :alt="getItemTitle(item)"
                        class="w-full h-full object-cover"
                        @error="(e) => (e.target as HTMLElement).style.display = 'none'"
                      >
                    </div>
                    <div>
                      <span class="font-bold text-slate-900 dark:text-white block text-sm">
                        {{ getItemTitle(item) }}
                      </span>
                      <span
                        v-if="getItemSubtitle(item)"
                        class="text-slate-400 text-[11px]"
                      >
                        {{ getItemSubtitle(item) }}
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Slug / Info -->
                <td class="py-4 px-5 text-slate-600 dark:text-slate-400">
                  <span
                    v-if="item.slug"
                    class="font-mono text-[11px] bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded"
                  >
                    /{{ item.slug }}
                  </span>
                  <span
                    v-else-if="item.email"
                    class="font-mono text-[11px]"
                  >
                    {{ item.email }}
                  </span>
                  <span
                    v-else-if="item.short_description || item.description"
                    class="truncate max-w-xs block text-slate-500"
                  >
                    {{ stripHtml(item.short_description || item.description) }}
                  </span>
                  <span
                    v-else
                    class="text-slate-400"
                  >
                    {{ item.location || item.type || 'N/A' }}
                  </span>
                </td>

                <!-- Status Toggle -->
                <td
                  class="py-4 px-5"
                  @click.stop
                >
                  <button
                    v-if="item.is_active !== undefined"
                    type="button"
                    :class="item.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider flex items-center gap-1.5 cursor-pointer"
                    @click="emit('toggle-status', item)"
                  >
                    <span
                      class="w-1.5 h-1.5 rounded-full"
                      :class="item.is_active ? 'bg-emerald-500' : 'bg-slate-400'"
                    />
                    <span>{{ item.is_active ? 'Active' : 'Inactive' }}</span>
                  </button>
                  <span
                    v-else-if="item.mark_as_read !== undefined || item.is_read !== undefined"
                    class="text-slate-500 text-[11px]"
                  >
                    {{ (item.mark_as_read || item.is_read) ? 'Read' : 'New' }}
                  </span>
                </td>

                <!-- Created Date -->
                <td class="py-4 px-5 text-slate-400 text-[11px]">
                  {{ item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A' }}
                </td>

                <!-- Actions -->
                <td
                  class="py-4 px-5 text-right space-x-1.5"
                  @click.stop
                >
                  <button
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-[11px] transition-colors cursor-pointer inline-flex items-center gap-1"
                    @click="emit('view', item)"
                  >
                    <i class="ri-eye-line text-xs" />
                    <span>View</span>
                  </button>
                  <button
                    v-if="!isEnquiryOrContact"
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="emit('edit', item)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="emit('delete', item.id)"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Server-Side API Pagination Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 select-none">
          <!-- Left: Showing 1 – 20 of 518 [20 v] -->
          <div class="flex items-center gap-3">
            <div>
              Showing
              <span class="font-bold text-slate-900 dark:text-white">{{ totalItems === 0 ? 0 : fromIndex }} – {{ toIndex }}</span>
              of
              <span class="font-bold text-slate-900 dark:text-white">{{ totalItems }}</span>
            </div>

            <div class="relative">
              <select
                v-model="perPage"
                class="appearance-none px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer shadow-2xs"
                @change="onPerPageSelect"
              >
                <option :value="10">
                  10
                </option>
                <option :value="20">
                  20
                </option>
                <option :value="25">
                  25
                </option>
                <option :value="50">
                  50
                </option>
                <option :value="100">
                  100
                </option>
              </select>
              <span class="pointer-events-none absolute inset-y-0 right-1.5 flex items-center text-slate-400 text-[10px]">
                ▼
              </span>
            </div>
          </div>

          <!-- Right: |< < 1 2 3 4 5 6 ... 21 > >| -->
          <div class="flex items-center gap-1">
            <!-- First |< -->
            <button
              type="button"
              :disabled="currentPage <= 1"
              title="First Page"
              class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200/70 dark:border-slate-700/80 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-[11px] font-semibold cursor-pointer"
              @click="setPage(1)"
            >
              <i class="ri-skip-back-line text-xs" />
            </button>

            <!-- Prev < -->
            <button
              type="button"
              :disabled="currentPage <= 1"
              title="Previous Page"
              class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200/70 dark:border-slate-700/80 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-[11px] font-semibold cursor-pointer"
              @click="setPage(currentPage - 1)"
            >
              <i class="ri-arrow-left-s-line text-sm" />
            </button>

            <!-- Numbers -->
            <template
              v-for="(p, idx) in visiblePages"
              :key="idx"
            >
              <span
                v-if="p === '...'"
                class="w-7 h-7 flex items-center justify-center text-slate-400 text-xs font-bold"
              >
                ...
              </span>
              <button
                v-else
                type="button"
                :class="[
                  p === currentPage
                    ? 'bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 font-extrabold shadow-2xs'
                    : 'border border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'
                ]"
                class="min-w-7 h-7 px-1.5 flex items-center justify-center rounded-lg text-xs transition-colors cursor-pointer"
                @click="setPage(Number(p))"
              >
                {{ p }}
              </button>
            </template>

            <!-- Next > -->
            <button
              type="button"
              :disabled="currentPage >= totalPages"
              title="Next Page"
              class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200/70 dark:border-slate-700/80 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-[11px] font-semibold cursor-pointer"
              @click="setPage(currentPage + 1)"
            >
              <i class="ri-arrow-right-s-line text-sm" />
            </button>

            <!-- Last >| -->
            <button
              type="button"
              :disabled="currentPage >= totalPages"
              title="Last Page"
              class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200/70 dark:border-slate-700/80 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition-colors text-[11px] font-semibold cursor-pointer"
              @click="setPage(totalPages)"
            >
              <i class="ri-skip-forward-line text-xs" />
            </button>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>
