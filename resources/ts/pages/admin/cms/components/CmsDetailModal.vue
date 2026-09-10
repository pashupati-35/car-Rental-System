<script setup lang="ts">
import { computed } from 'vue'
import { resolveMediaUrl } from '@/utils/helpers'
import type { CmsItem, CmsModuleMeta } from '../types'

const props = defineProps<{
  show: boolean
  item: CmsItem | null
  currentModuleMeta: CmsModuleMeta
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'edit', item: CmsItem): void
  (e: 'delete', id: number): void
}>()

const itemImage = computed(() => {
  if (!props.item) return ''
  const it = props.item
  
  return (
    resolveMediaUrl(it.image, it.image_path, 'cms') ||
    resolveMediaUrl(it.cover_image, it.cover_image_path, 'cms') ||
    resolveMediaUrl(it.preview_image, it.preview_image_path, 'cms') ||
    resolveMediaUrl(it.banner_image, null, 'cms') ||
    resolveMediaUrl(it.photo, null, 'cms') ||
    resolveMediaUrl(it.avatar, null, 'cms') ||
    resolveMediaUrl(it.logo, null, 'cms')
  )
})

const itemTitle = computed(() => {
  if (!props.item) return ''
  const it = props.item
  
  return it.title || it.question || it.name || it.subject || it.heading || it.menu_title || it.label || `Record #${it.id}`
})

const itemSubtitle = computed(() => {
  if (!props.item) return ''
  const it = props.item

  const cat = typeof it.category === 'object' && it.category !== null 
    ? (it.category.name || it.category.title || it.category.label || '') 
    : it.category

  
  return it.subtitle || cat || it.position_title || it.designation || it.email || it.slug || ''
})

const isHtmlContent = (val: string) => {
  return /<[a-z][\s\S]*>/i.test(val)
}

const formatDate = (dateStr?: string) => {
  if (!dateStr) return 'N/A'
  try {
    return new Date(dateStr).toLocaleString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return dateStr
  }
}

// Format friendly label from key
const formatKeyLabel = (key: string) => {
  return key
    .replace(/_/g, ' ')
    .replace(/\b\w/g, c => c.toUpperCase())
}

// Filter keys for key-value display table (excluding bulky/nested/internal ones)
const ignoredKeys = new Set([
  'id',
  'image',
  'banner_image',
  'image_path',
  'photo',
  'cover_image',
  'avatar',
  'logo',
  'created_at',
  'updated_at',
  'deleted_at',
  'password',
  'remember_token',
  'author_image',
  'author_image_path',
  'description',
  'content',
  'answer',
  'body',
  'message',
  'requirements',
])

const formatValue = (val: any): string => {
  if (typeof val === 'boolean') return val ? 'True / Yes' : 'False / No'
  if (Array.isArray(val)) {
    return val.map((item: any) => {
      if (typeof item === 'object' && item !== null) {
        return item.title || item.name || item.label || item.full_name || JSON.stringify(item)
      }
      
      return String(item)
    }).join(', ')
  }
  if (typeof val === 'object' && val !== null) {
    return val.title || val.name || val.label || val.full_name || JSON.stringify(val)
  }
  
  return String(val)
}

const displayableProperties = computed(() => {
  if (!props.item) return []
  
  return Object.entries(props.item).filter(([k, v]) => {
    if (ignoredKeys.has(k)) return false
    
    return v !== null && v !== undefined && v !== ''
  })
})
</script>

<template>
  <div
    v-if="show && item"
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl w-full max-w-4xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
      <!-- Header -->
      <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40 shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
            <i :class="currentModuleMeta.icon || 'ri-file-list-3-line'" />
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
                {{ currentModuleMeta.label }}
              </span>
              <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
                Record Details Overview
              </h3>
            </div>
            <p class="text-xs text-slate-500">
              Record ID: #{{ item.id }} &bull; Complete model attributes and relational parameters
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-xs cursor-pointer flex items-center gap-1.5 transition-colors"
            @click="emit('edit', item)"
          >
            <i class="ri-edit-line text-sm" />
            <span>Edit Record</span>
          </button>
          <button
            type="button"
            class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center transition-colors cursor-pointer"
            @click="emit('close')"
          >
            <i class="ri-close-line text-lg" />
          </button>
        </div>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 overflow-y-auto space-y-6 text-xs text-slate-700 dark:text-slate-300">
        <!-- Banner Card with Image & Title -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 p-5 rounded-2xl bg-gradient-to-br from-indigo-50/60 via-slate-50/40 to-purple-50/40 dark:from-indigo-950/30 dark:via-slate-900/40 dark:to-purple-950/20 border border-indigo-100/80 dark:border-indigo-900/40">
          <div
            v-if="itemImage"
            class="relative w-28 h-28 rounded-2xl overflow-hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm shrink-0"
          >
            <img
              :src="itemImage"
              :alt="itemTitle"
              class="w-full h-full object-cover"
              @error="(e) => (e.target as HTMLElement).style.display = 'none'"
            >
          </div>
          <div
            v-else
            class="w-20 h-20 rounded-2xl bg-indigo-100 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-3xl font-black shrink-0 border border-indigo-200 dark:border-indigo-800"
          >
            <i :class="currentModuleMeta.icon || 'ri-file-text-line'" />
          </div>

          <div class="flex-1 text-center sm:text-left space-y-2">
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
              <h4 class="text-lg font-black text-slate-900 dark:text-white">
                {{ itemTitle }}
              </h4>
              <span
                v-if="item.is_active !== undefined"
                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                :class="item.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
              <span
                v-if="item.status"
                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300"
              >
                {{ item.status }}
              </span>
            </div>

            <p
              v-if="itemSubtitle"
              class="text-xs font-semibold text-indigo-600 dark:text-indigo-400"
            >
              {{ itemSubtitle }}
            </p>

            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 text-slate-400 text-[11px] pt-1">
              <span
                v-if="item.created_at"
                class="flex items-center gap-1"
              >
                <i class="ri-calendar-line text-indigo-500" />
                Created: {{ formatDate(item.created_at) }}
              </span>
              <span
                v-if="item.updated_at"
                class="flex items-center gap-1"
              >
                <i class="ri-refresh-line text-purple-500" />
                Updated: {{ formatDate(item.updated_at) }}
              </span>
              <span
                v-if="item.slug"
                class="font-mono bg-white/80 dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300"
              >
                /{{ item.slug }}
              </span>
            </div>
          </div>
        </div>

        <!-- Rich Descriptions / Content / Body -->
        <div
          v-if="item.description || item.answer || item.content || item.body || item.message || item.requirements"
          class="p-5 rounded-2xl bg-white dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3"
        >
          <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2">
            <i class="ri-article-line text-indigo-500" />
            Detailed Content & Description
          </h5>

          <!-- Formatted Content -->
          <div
            v-if="item.answer"
            class="space-y-1"
          >
            <span class="text-[10px] uppercase font-bold text-slate-400 block">FAQ Answer</span>
            <div
              v-if="isHtmlContent(item.answer)"
              class="prose prose-xs max-w-none text-slate-700 dark:text-slate-300"
              v-html="item.answer"
            />
            <p
              v-else
              class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed"
            >
              {{ item.answer }}
            </p>
          </div>

          <div
            v-if="item.description"
            class="space-y-1"
          >
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Description</span>
            <div
              v-if="isHtmlContent(item.description)"
              class="prose prose-xs max-w-none text-slate-700 dark:text-slate-300"
              v-html="item.description"
            />
            <p
              v-else
              class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed"
            >
              {{ item.description }}
            </p>
          </div>

          <div
            v-if="item.content"
            class="space-y-1"
          >
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Body Content</span>
            <div
              v-if="isHtmlContent(item.content)"
              class="prose prose-xs max-w-none text-slate-700 dark:text-slate-300"
              v-html="item.content"
            />
            <p
              v-else
              class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed"
            >
              {{ item.content }}
            </p>
          </div>

          <div
            v-if="item.message"
            class="space-y-1"
          >
            <span class="text-[10px] uppercase font-bold text-slate-400 block">Message Text</span>
            <p class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed bg-slate-50 dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
              {{ item.message }}
            </p>
          </div>
        </div>

        <!-- All Model Fields Grid -->
        <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3">
          <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] flex items-center gap-1.5 border-b border-slate-200/60 dark:border-slate-700 pb-2">
            <i class="ri-list-settings-line text-indigo-500" />
            Model Attributes & Metadata
          </h5>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            <div
              v-for="[key, value] in displayableProperties"
              :key="key"
              class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700"
            >
              <span class="text-[10px] uppercase font-bold text-slate-400 block truncate">
                {{ formatKeyLabel(key) }}
              </span>
              <span class="font-semibold text-slate-800 dark:text-slate-200 break-words block mt-0.5">
                {{ formatValue(value) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center justify-between shrink-0">
        <button
          type="button"
          class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer transition-colors inline-flex items-center gap-1.5"
          @click="emit('delete', item.id); emit('close')"
        >
          <i class="ri-delete-bin-line text-sm" />
          <span>Delete Record</span>
        </button>

        <div class="flex items-center gap-2">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer"
            @click="emit('close')"
          >
            Close
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer inline-flex items-center gap-1.5"
            @click="emit('edit', item); emit('close')"
          >
            <i class="ri-edit-line text-sm" />
            <span>Edit Record</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
