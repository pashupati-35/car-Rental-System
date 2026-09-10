<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps<{
  stats?: Record<string, number>
}>()

const activeModule = ref('faqs')
const loading = ref(false)
const items = ref<any[]>([])
const searchQuery = ref('')
const showModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const message = ref('')
const errorMessage = ref('')
const currentItem = ref<any>({})

const modules = [
  { id: 'faqs', label: 'FAQs', icon: 'ri-question-line', color: 'blue' },
  { id: 'blogs', label: 'Blogs & Articles', icon: 'ri-article-line', color: 'indigo' },
  { id: 'services', label: 'Services', icon: 'ri-customer-service-2-line', color: 'emerald' },
  { id: 'teams', label: 'Team Members', icon: 'ri-team-line', color: 'purple' },
  { id: 'testimonials', label: 'Testimonials', icon: 'ri-chat-smile-2-line', color: 'amber' },
  { id: 'notices', label: 'Notices', icon: 'ri-notification-3-line', color: 'rose' },
  { id: 'sliders', label: 'Sliders & Banners', icon: 'ri-slideshow-3-line', color: 'cyan' },
  { id: 'popups', label: 'Popups & Alerts', icon: 'ri-window-line', color: 'pink' },
  { id: 'pages', label: 'Custom Pages', icon: 'ri-file-list-3-line', color: 'violet' },
  { id: 'partners', label: 'Partners', icon: 'ri-hand-heart-line', color: 'teal' },
  { id: 'careers', label: 'Careers', icon: 'ri-briefcase-line', color: 'orange' },
  { id: 'enquiries', label: 'Enquiries & Leads', icon: 'ri-mail-unread-line', color: 'blue' },
  { id: 'contacts', label: 'Contact Messages', icon: 'ri-contacts-book-line', color: 'emerald' },
  { id: 'menus', label: 'Navigation Menus', icon: 'ri-menu-line', color: 'gray' },
  { id: 'albums', label: 'Photo Albums', icon: 'ri-gallery-line', color: 'indigo' },
  { id: 'site-settings', label: 'Site Settings & SEO', icon: 'ri-settings-4-line', color: 'slate' },
]

const currentModuleMeta = computed(() => {
  return modules.find(m => m.id === activeModule.value) || modules[0]
})

const fetchModuleData = async () => {
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
  searchQuery.value = ''
  message.value = ''
  errorMessage.value = ''
  fetchModuleData()
})

const filteredItems = computed(() => {
  if (!searchQuery.value.trim()) return items.value
  const q = searchQuery.value.toLowerCase()
  return items.value.filter(item => {
    return (
      (item.title && item.title.toLowerCase().includes(q)) ||
      (item.name && item.name.toLowerCase().includes(q)) ||
      (item.email && item.email.toLowerCase().includes(q)) ||
      (item.company_name && item.company_name.toLowerCase().includes(q)) ||
      (item.description && item.description.toLowerCase().includes(q)) ||
      (item.subject && item.subject.toLowerCase().includes(q))
    )
  })
})

const openCreateModal = () => {
  isEditing.value = false
  currentItem.value = { is_active: 1, position: 0 }
  showModal.value = true
  message.value = ''
  errorMessage.value = ''
}

const openEditModal = (item: any) => {
  isEditing.value = true
  currentItem.value = { ...item }
  showModal.value = true
  message.value = ''
  errorMessage.value = ''
}

const saveItem = async () => {
  submitting.value = true
  message.value = ''
  errorMessage.value = ''

  try {
    if (isEditing.value && currentItem.value.id) {
      const res = await axios.post(`/admin/cms/data/${activeModule.value}/${currentItem.value.id}`, currentItem.value)
      if (res.data?.status === 'OK') {
        message.value = 'Item updated successfully.'
        showModal.value = false
        fetchModuleData()
      }
    } else {
      const res = await axios.post(`/admin/cms/data/${activeModule.value}`, currentItem.value)
      if (res.data?.status === 'OK' || res.status === 201) {
        message.value = 'Item created successfully.'
        showModal.value = false
        fetchModuleData()
      }
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to save item.'
  } finally {
    submitting.value = false
  }
}

const deleteItem = async (id: number) => {
  if (!confirm('Are you sure you want to delete this CMS record?')) return
  try {
    const res = await axios.delete(`/admin/cms/data/${activeModule.value}/${id}`)
    if (res.data?.status === 'OK') {
      fetchModuleData()
    }
  } catch (err) {
    alert('Failed to delete item.')
  }
}

const toggleStatus = async (item: any) => {
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
  <AppLayout>
    <Head title="Master CMS & Content Management - Admin Portal" />

    <div class="space-y-6">
      <!-- Breadcrumb Navigation -->
      <div class="flex items-center gap-2 text-xs">
        <Link
          href="/admin/dashboard"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50"
        >
          <i class="ri-dashboard-line" />
          <span>Dashboard</span>
        </Link>
        <span class="text-gray-400">/</span>
        <button
          type="button"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-blue-600 border-blue-200 bg-blue-50/70 font-semibold"
        >
          <i class="ri-article-line" />
          <span>Master CMS Management</span>
        </button>
      </div>

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
            <i class="ri-layout-masonry-line text-blue-600" />
            Content Management Suite (CMS)
          </h2>
          <p class="text-xs text-gray-500 mt-0.5">
            Manage public FAQs, Blogs, Services, Notices, Testimonials, Popups, Pages, and Settings
          </p>
        </div>

        <button
          v-if="activeModule !== 'site-settings' && activeModule !== 'enquiries' && activeModule !== 'contacts'"
          type="button"
          class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 shrink-0"
          @click="openCreateModal"
        >
          <i class="ri-add-line text-sm" />
          <span>Add New {{ currentModuleMeta.label.replace(' & Articles', '').replace(' Members', '').replace(' Messages', '').replace(' & Leads', '') }}</span>
        </button>
      </div>

      <!-- Flash Notification -->
      <div v-if="message" class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2 shadow-sm">
        <i class="ri-checkbox-circle-fill text-emerald-600 text-base" />
        <span>{{ message }}</span>
      </div>

      <!-- Module Scrollable Tabs -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
        <button
          v-for="mod in modules"
          :key="mod.id"
          type="button"
          :class="activeModule === mod.id ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-100 dark:border-gray-800 hover:bg-gray-50'"
          class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 shrink-0 transition-all"
          @click="activeModule = mod.id"
        >
          <i :class="mod.icon" />
          <span>{{ mod.label }}</span>
          <span
            v-if="props.stats && props.stats[mod.id] !== undefined"
            :class="activeModule === mod.id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
            class="px-1.5 py-0.5 rounded-full text-[10px] font-mono"
          >
            {{ props.stats[mod.id] }}
          </span>
        </button>
      </div>

      <!-- Search & Filters -->
      <div class="flex items-center justify-between gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="relative flex-1 max-w-md">
          <i class="ri-search-line absolute left-3.5 top-2.5 text-gray-400 text-sm" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search within this CMS module..."
            class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 text-xs focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <span class="text-xs text-gray-500">
          Showing {{ filteredItems.length }} records
        </span>
      </div>

      <!-- Records Table View -->
      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div v-if="loading" class="p-12 text-center">
          <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
          <p class="text-xs text-gray-500">Loading {{ currentModuleMeta.label }}...</p>
        </div>

        <div v-else-if="filteredItems.length === 0" class="p-12 text-center">
          <i :class="currentModuleMeta.icon" class="text-3xl text-gray-400 mb-2 inline-block" />
          <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">No records found</p>
          <p class="text-xs text-gray-500 mt-1">Get started by creating a new entry in this CMS module.</p>
        </div>

        <table v-else class="w-full text-left text-xs">
          <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 font-semibold uppercase tracking-wider">
            <tr>
              <th class="py-3.5 px-6">Record / Title</th>
              <th class="py-3.5 px-6">Key Info / Slug</th>
              <th class="py-3.5 px-6">Status</th>
              <th class="py-3.5 px-6">Created Date</th>
              <th class="py-3.5 px-6 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr
              v-for="item in filteredItems"
              :key="item.id"
              class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40"
            >
              <!-- Primary Column -->
              <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                <div class="flex items-center gap-3">
                  <div v-if="item.image || item.cover_image || item.featured_photo || item.featured_image" class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                    <img :src="item.image || item.cover_image || item.featured_photo || item.featured_image" class="w-full h-full object-cover" alt="" />
                  </div>
                  <div>
                    <span class="font-bold block text-sm">
                      {{ item.title || item.name || item.company_name || ('Record #' + item.id) }}
                    </span>
                    <span v-if="item.designation || item.job_title || item.subject" class="text-gray-500 text-[11px]">
                      {{ item.designation || item.job_title || item.subject }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Info Column -->
              <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                <span v-if="item.slug" class="font-mono text-[11px] bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded">
                  /{{ item.slug }}
                </span>
                <span v-else-if="item.email" class="text-gray-600">
                  {{ item.email }}
                </span>
                <span v-else-if="item.short_description" class="text-gray-500 truncate max-w-xs block">
                  {{ item.short_description }}
                </span>
                <span v-else class="text-gray-400">
                  {{ item.type || item.location || 'N/A' }}
                </span>
              </td>

              <!-- Status Toggle -->
              <td class="py-4 px-6">
                <button
                  v-if="item.is_active !== undefined"
                  type="button"
                  :class="item.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200'"
                  class="px-2.5 py-1 rounded-full text-[11px] font-semibold border flex items-center gap-1.5"
                  @click="toggleStatus(item)"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="item.is_active ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                  <span>{{ item.is_active ? 'Active' : 'Inactive' }}</span>
                </button>
                <span v-else-if="item.mark_as_read !== undefined || item.is_read !== undefined" class="text-gray-500 text-[11px]">
                  {{ (item.mark_as_read || item.is_read) ? 'Read' : 'New' }}
                </span>
              </td>

              <!-- Created At -->
              <td class="py-4 px-6 text-gray-500 text-[11px]">
                {{ item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A' }}
              </td>

              <!-- Actions -->
              <td class="py-4 px-6 text-right space-x-2">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 font-semibold text-[11px] transition-colors"
                  @click="openEditModal(item)"
                >
                  Edit
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-semibold text-[11px] transition-colors"
                  @click="deleteItem(item.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- CMS Modal Editor -->
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto"
      >
        <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-2xl w-full p-6 border border-gray-100 dark:border-gray-800 shadow-2xl relative">
          <div class="flex items-center justify-between mb-4 border-b border-gray-100 dark:border-gray-800 pb-3">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white">
              {{ isEditing ? 'Edit' : 'Create' }} {{ currentModuleMeta.label }}
            </h3>
            <button class="text-gray-400 hover:text-gray-600 text-lg" @click="showModal = false">&times;</button>
          </div>

          <div v-if="errorMessage" class="mb-4 p-3 rounded-xl bg-rose-50 text-rose-800 text-xs flex items-center gap-1.5">
            <i class="ri-error-warning-fill" /> <span>{{ errorMessage }}</span>
          </div>

          <form class="space-y-4" @submit.prevent="saveItem">
            <!-- Dynamic Fields by Module -->
            <template v-if="activeModule === 'faqs'">
              <div>
                <label class="block text-xs font-semibold mb-1">FAQ Question / Title</label>
                <input v-model="currentItem.title" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Answer / Description</label>
                <textarea v-model="currentItem.description" rows="4" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs"></textarea>
              </div>
            </template>

            <template v-else-if="activeModule === 'blogs'">
              <div>
                <label class="block text-xs font-semibold mb-1">Blog Title</label>
                <input v-model="currentItem.title" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Author Name</label>
                <input v-model="currentItem.author_name" type="text" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Featured Image URL</label>
                <input v-model="currentItem.image" type="text" placeholder="https://..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Article Content</label>
                <textarea v-model="currentItem.content" rows="5" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs"></textarea>
              </div>
            </template>

            <template v-else-if="activeModule === 'services'">
              <div>
                <label class="block text-xs font-semibold mb-1">Service Title</label>
                <input v-model="currentItem.title" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold mb-1">Price ($)</label>
                  <input v-model="currentItem.price" type="number" step="0.01" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
                <div>
                  <label class="block text-xs font-semibold mb-1">Image URL</label>
                  <input v-model="currentItem.image" type="text" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Description</label>
                <textarea v-model="currentItem.description" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs"></textarea>
              </div>
            </template>

            <template v-else-if="activeModule === 'teams'">
              <div>
                <label class="block text-xs font-semibold mb-1">Member Name</label>
                <input v-model="currentItem.name" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold mb-1">Designation</label>
                  <input v-model="currentItem.designation" type="text" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
                <div>
                  <label class="block text-xs font-semibold mb-1">Email</label>
                  <input v-model="currentItem.email" type="email" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Photo URL</label>
                <input v-model="currentItem.image" type="text" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
            </template>

            <template v-else-if="activeModule === 'testimonials'">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold mb-1">Client Name</label>
                  <input v-model="currentItem.name" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
                <div>
                  <label class="block text-xs font-semibold mb-1">Rating (1-5)</label>
                  <input v-model="currentItem.rating" type="number" min="1" max="5" step="0.5" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Testimonial Review</label>
                <textarea v-model="currentItem.description" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs"></textarea>
              </div>
            </template>

            <template v-else-if="activeModule === 'site-settings'">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold mb-1">Company / App Name</label>
                  <input v-model="currentItem.company_name" type="text" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
                <div>
                  <label class="block text-xs font-semibold mb-1">Official Email</label>
                  <input v-model="currentItem.email" type="email" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold mb-1">Mobile / Phone</label>
                  <input v-model="currentItem.mobile" type="text" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
                <div>
                  <label class="block text-xs font-semibold mb-1">Website URL</label>
                  <input v-model="currentItem.website" type="text" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">SEO Title</label>
                <input v-model="currentItem.seo_title" type="text" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">SEO Description</label>
                <textarea v-model="currentItem.seo_description" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs"></textarea>
              </div>
            </template>

            <!-- Default Generic Fallback for Other Modules -->
            <template v-else>
              <div>
                <label class="block text-xs font-semibold mb-1">Title / Name</label>
                <input v-model="currentItem.title" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs" />
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Description / Content</label>
                <textarea v-model="currentItem.description" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs"></textarea>
              </div>
            </template>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800">
              <button
                type="button"
                class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs"
                @click="showModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-500/20 disabled:opacity-50"
              >
                {{ submitting ? 'Saving...' : 'Save Record' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
