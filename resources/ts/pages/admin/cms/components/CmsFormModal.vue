<script setup lang="ts">
import type { CmsItem, CmsModuleMeta } from '../types'
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

const props = defineProps<{
  show: boolean
  isEditing: boolean
  item: CmsItem
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
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-2xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
          <i :class="currentModuleMeta.icon" class="text-indigo-600" />
          <span>{{ isEditing ? 'Edit' : 'Create New' }} {{ currentModuleMeta.label }}</span>
        </h3>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <div v-if="errorMessage" class="p-3 rounded-2xl bg-rose-50 text-rose-800 text-xs font-semibold">
        {{ errorMessage }}
      </div>

      <form class="space-y-4 text-xs" @submit.prevent="emit('save')">
        <!-- Separate Component Per CMS Module -->
        <FaqForm v-if="activeModule === 'faqs'" :item="item" />
        <BlogForm v-else-if="activeModule === 'blogs'" :item="item" />
        <CareerForm v-else-if="activeModule === 'careers'" :item="item" />
        <TeamForm v-else-if="activeModule === 'teams'" :item="item" />
        <ServiceForm v-else-if="activeModule === 'services'" :item="item" />
        <PopupForm v-else-if="activeModule === 'popups'" :item="item" />
        <NoticeForm v-else-if="activeModule === 'notices'" :item="item" />
        <NewsForm v-else-if="activeModule === 'news'" :item="item" />
        <SliderForm v-else-if="activeModule === 'sliders'" :item="item" />
        <PageForm v-else-if="activeModule === 'pages'" :item="item" />
        <TestimonialForm v-else-if="activeModule === 'testimonials'" :item="item" />
        <AlbumForm v-else-if="activeModule === 'albums'" :item="item" />
        <MenuForm v-else-if="activeModule === 'menus'" :item="item" />
        <PartnerForm v-else-if="activeModule === 'partners'" :item="item" />
        <GenericForm v-else :item="item" />

        <!-- Status Toggle -->
        <div class="flex items-center gap-2 pt-2">
          <input
            v-model="item.is_active"
            type="checkbox"
            id="modal_active"
            :true-value="1"
            :false-value="0"
            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
          />
          <label for="modal_active" class="font-bold cursor-pointer text-slate-700 dark:text-slate-300">
            Active / Published on Public Site
          </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer flex items-center gap-1.5"
          >
            <i class="ri-check-line" />
            <span>{{ submitting ? 'Saving...' : 'Save Record' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
