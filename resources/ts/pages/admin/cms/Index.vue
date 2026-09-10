<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { CmsItem, CmsModuleMeta } from './types'
import CmsModuleTabs from './components/CmsModuleTabs.vue'
import CmsTable from './components/CmsTable.vue'
import CmsFormModal from './components/CmsFormModal.vue'
import SiteSettingsView from './components/SiteSettingsView.vue'
import EnquiryDetailsModal from './components/EnquiryDetailsModal.vue'

const props = defineProps<{
  stats?: Record<string, number>
  initialModule?: string
}>()

const getInitialModule = (): string => {
  if (typeof window !== 'undefined') {
    const params = new URLSearchParams(window.location.search)
    const m = params.get('module')
    if (m) return m
  }
  return props.initialModule || 'faqs'
}

const activeModule = ref<string>(getInitialModule())
const loading = ref<boolean>(false)
const items = ref<CmsItem[]>([])
const showModal = ref<boolean>(false)
const isEditing = ref<boolean>(false)
const submitting = ref<boolean>(false)
const message = ref<string>('')
const errorMessage = ref<string>('')
const currentItem = ref<CmsItem>({ id: 0 })
const viewingEnquiry = ref<CmsItem | null>(null)
const showEnquiryModal = ref<boolean>(false)

const modules: CmsModuleMeta[] = [
  { id: 'faqs', label: 'FAQ', icon: 'ri-question-line', color: 'indigo' },
  { id: 'blogs', label: 'Blog', icon: 'ri-article-line', color: 'blue' },
  { id: 'careers', label: 'Career', icon: 'ri-briefcase-line', color: 'orange' },
  { id: 'teams', label: 'Teams', icon: 'ri-team-line', color: 'purple' },
  { id: 'services', label: 'Our service', icon: 'ri-heart-line', color: 'emerald' },
  { id: 'popups', label: 'Popup', icon: 'ri-window-line', color: 'pink' },
  { id: 'notices', label: 'Notices', icon: 'ri-notification-3-line', color: 'rose' },
  { id: 'news', label: 'News and updates', icon: 'ri-newspaper-line', color: 'cyan' },
  { id: 'sliders', label: 'Sliders', icon: 'ri-slideshow-3-line', color: 'amber' },
  { id: 'pages', label: 'Pages', icon: 'ri-file-list-3-line', color: 'violet' },
  { id: 'testimonials', label: 'Testimonials', icon: 'ri-star-line', color: 'amber' },
  { id: 'albums', label: 'Album', icon: 'ri-image-line', color: 'indigo' },
  { id: 'menus', label: 'Menu', icon: 'ri-menu-line', color: 'slate' },
  { id: 'partners', label: 'Partners', icon: 'ri-hand-heart-line', color: 'teal' },
  { id: 'enquiries', label: 'Enquiries', icon: 'ri-mail-unread-line', color: 'blue' },
  { id: 'contacts', label: 'Contacts', icon: 'ri-contacts-book-line', color: 'emerald' },
  { id: 'site-settings', label: 'Site Settings & SEO', icon: 'ri-settings-4-line', color: 'slate' },
]

const currentModuleMeta = computed<CmsModuleMeta>(() => {
  return modules.find(m => m.id === activeModule.value) || modules[0]
})

const fetchModuleData = async (): Promise<void> => {
  loading.value = true
  items.value = []
  try {
    const res = await axios.get(`/admin/cms/data/${activeModule.value}`)
    if (res.data?.data) {
      if (Array.isArray(res.data.data)) {
        items.value = res.data.data
      } else if (res.data.data.data && Array.isArray(res.data.data.data)) {
        items.value = res.data.data.data
      } else {
        items.value = [res.data.data]
      }
    }
  } catch (err) {
    console.error(`Failed to fetch CMS module ${activeModule.value}:`, err)
  } finally {
    loading.value = false
  }
}

watch(activeModule, () => {
  message.value = ''
  errorMessage.value = ''
  fetchModuleData()
})

watch(() => props.initialModule, (newMod?: string) => {
  if (newMod && newMod !== activeModule.value) {
    activeModule.value = newMod
  }
})

const openCreateModal = (): void => {
  isEditing.value = false
  currentItem.value = {
    id: 0,
    is_active: 1,
    position: 0,
    rating: 5,
    employment_type: 'Full-time',
    popup_type: 'modal',
  }
  showModal.value = true
  message.value = ''
  errorMessage.value = ''
}

const openEditModal = (item: CmsItem): void => {
  isEditing.value = true
  currentItem.value = { ...item }
  showModal.value = true
  message.value = ''
  errorMessage.value = ''
}

const openViewEnquiry = (item: CmsItem): void => {
  viewingEnquiry.value = item
  showEnquiryModal.value = true
}

const saveItem = async (): Promise<void> => {
  submitting.value = true
  message.value = ''
  errorMessage.value = ''

  try {
    if (activeModule.value === 'site-settings') {
      const payload = items.value[0] || currentItem.value
      const res = await axios.post(`/admin/cms/data/site-settings`, payload)
      if (res.data?.status === 'OK' || res.status === 200) {
        message.value = 'Site settings saved successfully.'
        fetchModuleData()
      }
    } else if (isEditing.value && currentItem.value.id) {
      const res = await axios.post(`/admin/cms/data/${activeModule.value}/${currentItem.value.id}`, currentItem.value)
      if (res.data?.status === 'OK') {
        message.value = `${currentModuleMeta.value.label} record updated successfully.`
        showModal.value = false
        fetchModuleData()
      }
    } else {
      const res = await axios.post(`/admin/cms/data/${activeModule.value}`, currentItem.value)
      if (res.data?.status === 'OK' || res.status === 201) {
        message.value = `${currentModuleMeta.value.label} record created successfully.`
        showModal.value = false
        fetchModuleData()
      }
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to save CMS record.'
  } finally {
    submitting.value = false
  }
}

const deleteItem = async (id: number): Promise<void> => {
  if (!confirm('Are you sure you want to permanently delete this CMS record?')) return
  try {
    const res = await axios.delete(`/admin/cms/data/${activeModule.value}/${id}`)
    if (res.data?.status === 'OK') {
      message.value = 'Record deleted.'
      fetchModuleData()
    }
  } catch (err) {
    alert('Failed to delete item.')
  }
}

const toggleStatus = async (item: CmsItem): Promise<void> => {
  try {
    const res = await axios.post(`/admin/cms/data/${activeModule.value}/${item.id}/toggle-status`)
    if (res.data?.status === 'OK') {
      item.is_active = res.data.is_active
    }
  } catch (err) {
    console.error('Failed to toggle status:', err)
  }
}

onMounted(() => {
  fetchModuleData()
})
</script>

<template>
  <AdminLayout>
    <Head title="Master CMS Management - Super Admin" />

    <div class="space-y-6">
      <!-- Title Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
              Admin Exclusive Suite
            </span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
              <i class="ri-layout-masonry-line text-indigo-600" />
              Master CMS & Content Management
            </h2>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            Full administrative Create, Read, Update, Delete (CRUD) authority over 16 content modules on the public web platform.
          </p>
        </div>

        <button
          v-if="activeModule !== 'site-settings' && activeModule !== 'enquiries' && activeModule !== 'contacts'"
          type="button"
          class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2 shrink-0 self-start sm:self-auto cursor-pointer"
          @click="openCreateModal"
        >
          <i class="ri-add-line text-sm" />
          <span>Add {{ currentModuleMeta.label.replace(' & Articles', '').replace(' Members', '').replace(' Messages', '').replace(' & Leads', '') }}</span>
        </button>
      </div>

      <!-- Flash Notification -->
      <div v-if="message" class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
        <i class="ri-checkbox-circle-fill text-emerald-600 text-base shrink-0" />
        <span>{{ message }}</span>
      </div>

      <!-- Module Switcher Tabs -->
      <CmsModuleTabs
        :modules="modules"
        :active-module="activeModule"
        :stats="props.stats"
        @update:active-module="activeModule = $event"
      />

      <!-- Main Content Area: Site Settings vs CMS Table -->
      <SiteSettingsView
        v-if="activeModule === 'site-settings'"
        :settings="items[0] || {}"
        :submitting="submitting"
        @save="saveItem"
      />

      <CmsTable
        v-else
        :items="items"
        :loading="loading"
        :active-module="activeModule"
        :current-module-meta="currentModuleMeta"
        @edit="openEditModal"
        @delete="deleteItem"
        @toggle-status="toggleStatus"
        @view="openViewEnquiry"
        @refresh="fetchModuleData"
      />

      <!-- Create / Edit CMS Item Modal -->
      <CmsFormModal
        :show="showModal"
        :is-editing="isEditing"
        :item="currentItem"
        :active-module="activeModule"
        :current-module-meta="currentModuleMeta"
        :submitting="submitting"
        :error-message="errorMessage"
        @save="saveItem"
        @close="showModal = false"
      />

      <!-- Enquiry / Contact Details Modal -->
      <EnquiryDetailsModal
        :show="showEnquiryModal"
        :item="viewingEnquiry"
        @close="showEnquiryModal = false"
        @delete="deleteItem"
      />
    </div>
  </AdminLayout>
</template>
