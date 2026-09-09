<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  templates: {
    data: Array<any>
    links: Array<any>
    current_page: number
    last_page: number
  }
  filters?: {
    title?: string
    role?: string
  }
}>()
</script>

<template>
  <AppLayout>
    <Head title="Email Templates" />

    <template #header>
      Email Templates Management
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">
            System Email Templates
          </h3>
          <p class="text-xs text-gray-500 mt-0.5">
            Customize automatic system notifications and transactional emails
          </p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
            <tr>
              <th class="py-3.5 px-6">
                Template Title
              </th>
              <th class="py-3.5 px-6">
                Identifier
              </th>
              <th class="py-3.5 px-6">
                Role
              </th>
              <th class="py-3.5 px-6">
                Status
              </th>
              <th class="py-3.5 px-6 text-right">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr
              v-for="template in templates.data"
              :key="template.id"
              class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40"
            >
              <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                {{ template.title }}
              </td>
              <td class="py-4 px-6 text-xs font-mono text-gray-500">
                {{ template.identifier }}
              </td>
              <td class="py-4 px-6">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                  {{ template.role || 'general' }}
                </span>
              </td>
              <td class="py-4 px-6">
                <span
                  :class="template.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-600'"
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                >
                  {{ template.is_active ? 'Active' : 'Disabled' }}
                </span>
              </td>
              <td class="py-4 px-6 text-right">
                <Link
                  :href="`/admin/email-templates/${template.id}/edit`"
                  class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium text-xs transition-colors"
                >
                  Edit Template
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
