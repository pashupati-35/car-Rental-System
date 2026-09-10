<script setup lang="ts">
import type { CmsItem, CmsModuleMeta } from '../types'
import FormToggle from '@/components/FormToggle.vue'
import FaqForm from '../forms/FaqForm.vue'
import BlogForm from '../forms/BlogForm.vue'
import CareerForm from '../forms/CareerForm.vue'
import TeamForm from '../forms/TeamForm.vue'
import ServiceForm from '../forms/ServiceForm.vue'
import PopupForm from '../forms/PopupForm.vue'
import NoticeForm from '../forms/NoticeForm.vue'
import NewsForm from '../forms/NewsForm.vue'
import SliderForm from '../forms/SliderForm.vue'
import PageForm from '../forms/PageForm.vue'
import TestimonialForm from '../forms/TestimonialForm.vue'
import AlbumForm from '../forms/AlbumForm.vue'
import MenuForm from '../forms/MenuForm.vue'
import PartnerForm from '../forms/PartnerForm.vue'
import GenericForm from '../forms/GenericForm.vue'

defineProps<{
  show: boolean
  isEditing: boolean
  activeModule: string
  currentModuleMeta: CmsModuleMeta
  submitting: boolean
  errorMessage: string
}>()

const emit = defineEmits<{
  (e: 'update:show', value: boolean): void
  (e: 'save'): void
  (e: 'close'): void
}>()

const item = defineModel<CmsItem>('item', { required: true })
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-3xl w-full max-h-[92vh] flex flex-col border border-slate-200 dark:border-slate-800 shadow-2xl relative">
      <!-- Modal Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-lg border border-indigo-100 dark:border-indigo-900">
            <i :class="currentModuleMeta.icon" />
          </div>
          <div>
            <h3 class="font-bold text-base text-slate-900 dark:text-white leading-tight">
              {{ isEditing ? 'Edit' : 'Create New' }} {{ currentModuleMeta.label }}
            </h3>
            <p class="text-[11px] text-slate-400">
              Manage CMS content, publishing status, and rich details
            </p>
          </div>
        </div>

        <button
          type="button"
          class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-lg cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <!-- Error Alert -->
      <div
        v-if="errorMessage"
        class="mx-6 mt-4 p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 text-xs font-semibold"
      >
        {{ errorMessage }}
      </div>

      <!-- Scrollable Modal Body -->
      <div class="flex-1 overflow-y-auto px-6 py-5">
        <form
          id="cmsModalForm"
          class="space-y-5 text-xs"
          @submit.prevent="emit('save')"
        >
          <!-- CMS Module Specific Form -->
          <FaqForm
            v-if="activeModule === 'faqs'"
            v-model:item="item"
          />
          <BlogForm
            v-else-if="activeModule === 'blogs'"
            v-model:item="item"
          />
          <CareerForm
            v-else-if="activeModule === 'careers'"
            v-model:item="item"
          />
          <TeamForm
            v-else-if="activeModule === 'teams'"
            v-model:item="item"
          />
          <ServiceForm
            v-else-if="activeModule === 'services'"
            v-model:item="item"
          />
          <PopupForm
            v-else-if="activeModule === 'popups'"
            v-model:item="item"
          />
          <NoticeForm
            v-else-if="activeModule === 'notices'"
            v-model:item="item"
          />
          <NewsForm
            v-else-if="activeModule === 'news'"
            v-model:item="item"
          />
          <SliderForm
            v-else-if="activeModule === 'sliders'"
            v-model:item="item"
          />
          <PageForm
            v-else-if="activeModule === 'pages'"
            v-model:item="item"
          />
          <TestimonialForm
            v-else-if="activeModule === 'testimonials'"
            v-model:item="item"
          />
          <AlbumForm
            v-else-if="activeModule === 'albums'"
            v-model:item="item"
          />
          <MenuForm
            v-else-if="activeModule === 'menus'"
            v-model:item="item"
          />
          <PartnerForm
            v-else-if="activeModule === 'partners'"
            v-model:item="item"
          />
          <GenericForm
            v-else
            v-model:item="item"
          />

          <!-- Publication Status Toggle -->
          <div
            v-if="item.is_active !== undefined || !isEditing"
            class="pt-2"
          >
            <FormToggle
              v-model="item.is_active"
              label="Active & Published on Public Site"
              description="Toggle whether this record is visible to customers on the public frontend"
              active-text="Published"
              inactive-text="Draft / Hidden"
            />
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 shrink-0">
        <button
          type="button"
          class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-bold text-xs cursor-pointer transition-colors"
          @click="emit('close')"
        >
          Cancel
        </button>
        <button
          type="submit"
          form="cmsModalForm"
          :disabled="submitting"
          class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer flex items-center gap-1.5 transition-all"
        >
          <i
            v-if="submitting"
            class="ri-loader-4-line animate-spin"
          />
          <i
            v-else
            class="ri-check-line"
          />
          <span>{{ submitting ? 'Saving...' : 'Save Record' }}</span>
        </button>
      </div>
    </div>
  </div>
</template>
