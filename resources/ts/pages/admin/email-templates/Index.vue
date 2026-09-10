<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Pagination from '@/components/Pagination.vue'

const props = defineProps<{
  templates: {
    data: Array<any>
    links: Array<any>
    from?: number
    to?: number
    total?: number
    current_page: number
    last_page: number
  }
  filters?: {
    title?: string
    role?: string
  }
}>()

const activeRole = ref(props.filters?.role || 'all')
const searchQuery = ref(props.filters?.title || '')

const setRole = (role: string) => {
  activeRole.value = role
  router.get('/admin/email-templates', {
    role: role === 'all' ? '' : role,
    title: searchQuery.value,
  }, { preserveState: true, preserveScroll: true })
}

const handleSearch = () => {
  router.get('/admin/email-templates', {
    role: activeRole.value === 'all' ? '' : activeRole.value,
    title: searchQuery.value,
  }, { preserveState: true, preserveScroll: true })
}

const getRoleBadgeClass = (role: string) => {
  switch (role) {
    case 'owner':
      return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border-emerald-200/60 dark:border-emerald-800'
    case 'customer':
      return 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border-indigo-200/60 dark:border-indigo-800'
    case 'admin':
      return 'bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300 border-purple-200/60 dark:border-purple-800'
    default:
      return 'bg-slate-50 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="System Email Templates - AutoRent Super Admin" />

    <div class="space-y-6">
      <!-- Title Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <i class="ri-mail-settings-line text-indigo-600" />
            System Email Templates & Notifications
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Configure automated system emails, placeholders, and notification content for Fleet Owners, Customers, and Administrators.
          </p>
        </div>
      </div>

      <!-- Filters & Search Bar -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <!-- Role Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
          <button
            type="button"
            :class="activeRole === 'all' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-200/70'"
            class="px-3.5 py-2 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer shrink-0"
            @click="setRole('all')"
          >
            <i class="ri-mail-line" />
            <span>All Templates</span>
          </button>

          <button
            type="button"
            :class="activeRole === 'owner' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-200/70'"
            class="px-3.5 py-2 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer shrink-0"
            @click="setRole('owner')"
          >
            <i class="ri-building-line" />
            <span>Fleet Owners</span>
          </button>

          <button
            type="button"
            :class="activeRole === 'customer' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-200/70'"
            class="px-3.5 py-2 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer shrink-0"
            @click="setRole('customer')"
          >
            <i class="ri-user-smile-line" />
            <span>Customers</span>
          </button>

          <button
            type="button"
            :class="activeRole === 'admin' ? 'bg-purple-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-200/70'"
            class="px-3.5 py-2 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer shrink-0"
            @click="setRole('admin')"
          >
            <i class="ri-shield-star-line" />
            <span>Admins</span>
          </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full md:w-64">
          <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by title or role..."
            class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            @keyup.enter="handleSearch"
          />
        </div>
      </div>

      <!-- Templates Table Card -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
              <tr>
                <th class="py-3.5 px-5">Template Title</th>
                <th class="py-3.5 px-5">Identifier Key</th>
                <th class="py-3.5 px-5">Target Role</th>
                <th class="py-3.5 px-5">Email Subject</th>
                <th class="py-3.5 px-5">Status</th>
                <th class="py-3.5 px-5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
              <tr v-if="templates.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400">
                  <i class="ri-mail-open-line text-3xl mb-2 inline-block text-slate-300" />
                  <p class="font-semibold text-sm text-slate-600 dark:text-slate-400">No email templates found</p>
                </td>
              </tr>
              <tr
                v-for="template in templates.data"
                :key="template.id"
                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Title -->
                <td class="py-4 px-5">
                  <span class="font-bold text-slate-900 dark:text-white block text-sm">
                    {{ template.title }}
                  </span>
                  <span class="text-[11px] text-slate-400 capitalize">Type: {{ template.type || 'Transactional' }}</span>
                </td>

                <!-- Identifier -->
                <td class="py-4 px-5 font-mono text-[11px] text-slate-500">
                  <span class="bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                    {{ template.identifier }}
                  </span>
                </td>

                <!-- Role -->
                <td class="py-4 px-5">
                  <span
                    :class="getRoleBadgeClass(template.role)"
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border inline-block"
                  >
                    {{ template.role || 'general' }}
                  </span>
                </td>

                <!-- Subject -->
                <td class="py-4 px-5 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                  {{ template.subject || 'N/A' }}
                </td>

                <!-- Status -->
                <td class="py-4 px-5">
                  <span
                    :class="template.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                    class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                  >
                    {{ template.is_active ? 'Active' : 'Disabled' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-4 px-5 text-right">
                  <Link
                    :href="`/admin/email-templates/${template.id}/edit`"
                    class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-xs transition-colors inline-flex items-center gap-1.5"
                  >
                    <i class="ri-edit-line" />
                    <span>Edit Template</span>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <Pagination
          :links="templates.links || []"
          :from="templates.from"
          :to="templates.to"
          :total="templates.total"
        />
      </div>
    </div>
  </AdminLayout>
</template>
