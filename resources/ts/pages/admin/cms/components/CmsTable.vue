<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import type { CmsItem, CmsModuleMeta } from '../types'

const props = defineProps<{
  items: CmsItem[]
  loading: boolean
  activeModule: string
  currentModuleMeta: CmsModuleMeta
}>()

const emit = defineEmits<{
  (e: 'edit', item: CmsItem): void
  (e: 'delete', id: number): void
  (e: 'toggle-status', item: CmsItem): void
  (e: 'view', item: CmsItem): void
  (e: 'refresh'): void
}>()

const searchQuery = ref('')
const currentPage = ref(1)
const perPage = ref(10)

const filteredItems = computed<CmsItem[]>(() => {
  if (!searchQuery.value.trim()) return props.items
  const q = searchQuery.value.toLowerCase()
  return props.items.filter((item: CmsItem) => {
    return (
      (item.title && String(item.title).toLowerCase().includes(q)) ||
      (item.name && String(item.name).toLowerCase().includes(q)) ||
      (item.email && String(item.email).toLowerCase().includes(q)) ||
      (item.company_name && String(item.company_name).toLowerCase().includes(q)) ||
      (item.description && String(item.description).toLowerCase().includes(q)) ||
      (item.subject && String(item.subject).toLowerCase().includes(q)) ||
      (item.job_title && String(item.job_title).toLowerCase().includes(q)) ||
      (item.department && String(item.department).toLowerCase().includes(q)) ||
      (item.location && String(item.location).toLowerCase().includes(q))
    )
  })
})

const totalPages = computed(() => Math.ceil(filteredItems.value.length / perPage.value) || 1)

const paginatedItems = computed<CmsItem[]>(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredItems.value.slice(start, start + perPage.value)
})

watch([() => props.activeModule, searchQuery], () => {
  currentPage.value = 1
})

const setPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const getItemImage = (item: CmsItem): string | undefined => {
  return item.image || item.cover_image || item.featured_photo || item.featured_image || item.logo
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
          :placeholder="`Search ${currentModuleMeta.label.toLowerCase()}...`"
          class="w-full pl-8 pr-3 py-2 sm:py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
        />
      </div>

      <div class="flex items-center justify-between sm:justify-end gap-3">
        <span class="text-xs font-bold text-slate-400">
          Showing {{ filteredItems.length }} {{ currentModuleMeta.label }}
        </span>
        <button
          type="button"
          class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs transition-colors cursor-pointer"
          title="Refresh Data"
          @click="emit('refresh')"
        >
          <i class="ri-refresh-line" :class="loading ? 'animate-spin' : ''" />
        </button>
      </div>
    </div>

    <!-- Content Table / View -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
      <!-- Loading State -->
      <div v-if="loading" class="py-16 text-center">
        <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
        <p class="text-xs text-slate-500 font-semibold">Loading {{ currentModuleMeta.label }}...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredItems.length === 0" class="py-16 text-center px-4">
        <i :class="currentModuleMeta.icon" class="text-4xl text-slate-300 dark:text-slate-700 mb-2 inline-block" />
        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">No records found</p>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
          No records are currently listed under this CMS module.
        </p>
      </div>

      <template v-else>
        <!-- Mobile Cards View for Small Screens (xs, sm) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
          <div
            v-for="item in paginatedItems"
            :key="item.id"
            class="p-4 space-y-3"
          >
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-start gap-3 min-w-0">
                <div v-if="getItemImage(item)" class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                  <img :src="getItemImage(item)" class="w-full h-full object-cover" />
                </div>
                <div class="min-w-0">
                  <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">
                    {{ getItemTitle(item) }}
                  </h4>
                  <p v-if="getItemSubtitle(item)" class="text-slate-500 text-xs">
                    {{ getItemSubtitle(item) }}
                  </p>
                  <p v-if="item.slug" class="font-mono text-[10px] text-slate-400 mt-0.5">/{{ item.slug }}</p>
                  <p v-else-if="item.email" class="font-mono text-[10px] text-slate-400 mt-0.5">{{ item.email }}</p>
                </div>
              </div>

              <!-- Status Toggle -->
              <button
                v-if="item.is_active !== undefined"
                type="button"
                :class="item.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 flex items-center gap-1 cursor-pointer"
                @click="emit('toggle-status', item)"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="item.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                <span>{{ item.is_active ? 'Active' : 'Draft' }}</span>
              </button>
            </div>

            <p v-if="item.short_description || item.description || item.message" class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2">
              {{ item.short_description || item.description || item.message }}
            </p>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800/60 text-xs">
              <span class="text-[11px] text-slate-400">
                {{ item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A' }}
              </span>
              <div class="flex items-center gap-2">
                <button
                  v-if="isEnquiryOrContact"
                  type="button"
                  class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs cursor-pointer"
                  @click="emit('view', item)"
                >
                  View Message
                </button>
                <button
                  v-else
                  type="button"
                  class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200 font-bold text-xs cursor-pointer"
                  @click="emit('edit', item)"
                >
                  Edit
                </button>
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
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
                <th class="py-3.5 px-5">Record / Title</th>
                <th class="py-3.5 px-5">Details / Slug</th>
                <th class="py-3.5 px-5">Status</th>
                <th class="py-3.5 px-5">Created Date</th>
                <th class="py-3.5 px-5 text-right">Admin Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
              <tr
                v-for="item in paginatedItems"
                :key="item.id"
                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Title & Image -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <div v-if="getItemImage(item)" class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                      <img :src="getItemImage(item)" class="w-full h-full object-cover" />
                    </div>
                    <div>
                      <span class="font-bold text-slate-900 dark:text-white block text-sm">
                        {{ getItemTitle(item) }}
                      </span>
                      <span v-if="getItemSubtitle(item)" class="text-slate-400 text-[11px]">
                        {{ getItemSubtitle(item) }}
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Slug / Info -->
                <td class="py-4 px-5 text-slate-600 dark:text-slate-400">
                  <span v-if="item.slug" class="font-mono text-[11px] bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                    /{{ item.slug }}
                  </span>
                  <span v-else-if="item.email" class="font-mono text-[11px]">
                    {{ item.email }}
                  </span>
                  <span v-else-if="item.short_description || item.description" class="truncate max-w-xs block text-slate-500">
                    {{ item.short_description || item.description }}
                  </span>
                  <span v-else class="text-slate-400">
                    {{ item.location || item.type || 'N/A' }}
                  </span>
                </td>

                <!-- Status Toggle -->
                <td class="py-4 px-5">
                  <button
                    v-if="item.is_active !== undefined"
                    type="button"
                    :class="item.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider flex items-center gap-1.5 cursor-pointer"
                    @click="emit('toggle-status', item)"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="item.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                    <span>{{ item.is_active ? 'Active' : 'Inactive' }}</span>
                  </button>
                  <span v-else-if="item.mark_as_read !== undefined || item.is_read !== undefined" class="text-slate-500 text-[11px]">
                    {{ (item.mark_as_read || item.is_read) ? 'Read' : 'New' }}
                  </span>
                </td>

                <!-- Created Date -->
                <td class="py-4 px-5 text-slate-400 text-[11px]">
                  {{ item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A' }}
                </td>

                <!-- Actions -->
                <td class="py-4 px-5 text-right space-x-1.5">
                  <button
                    v-if="isEnquiryOrContact"
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="emit('view', item)"
                  >
                    View
                  </button>
                  <button
                    v-else
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

        <!-- CMS Client Pagination Bar -->
        <div
          v-if="filteredItems.length > perPage"
          class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 bg-slate-50/50 dark:bg-slate-800/30"
        >
          <div>
            Showing <span class="font-bold text-slate-900 dark:text-white">{{ (currentPage - 1) * perPage + 1 }}</span> to
            <span class="font-bold text-slate-900 dark:text-white">{{ Math.min(currentPage * perPage, filteredItems.length) }}</span> of
            <span class="font-bold text-slate-900 dark:text-white">{{ filteredItems.length }}</span> records
          </div>

          <div class="flex items-center gap-1">
            <button
              type="button"
              :disabled="currentPage === 1"
              class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 font-semibold disabled:opacity-40 hover:bg-slate-50 cursor-pointer"
              @click="setPage(currentPage - 1)"
            >
              Previous
            </button>
            <button
              v-for="p in totalPages"
              :key="p"
              type="button"
              :class="p === currentPage ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'"
              class="w-8 h-8 rounded-xl text-xs flex items-center justify-center font-semibold cursor-pointer"
              @click="setPage(p)"
            >
              {{ p }}
            </button>
            <button
              type="button"
              :disabled="currentPage === totalPages"
              class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 font-semibold disabled:opacity-40 hover:bg-slate-50 cursor-pointer"
              @click="setPage(currentPage + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>
