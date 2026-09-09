<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  customers: Array<any>
}>()
</script>

<template>
  <AppLayout>
    <Head title="Registered Customers" />

    <template #header>
      Customer Directory
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">
            Customer Records
          </h3>
          <p class="text-xs text-gray-500 mt-0.5">
            Manage customer accounts, contact information and rental history
          </p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
            <tr>
              <th class="py-3.5 px-6">
                Customer Name
              </th>
              <th class="py-3.5 px-6">
                Email Address
              </th>
              <th class="py-3.5 px-6">
                Phone Number
              </th>
              <th class="py-3.5 px-6">
                Address
              </th>
              <th class="py-3.5 px-6">
                Joined Date
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr
              v-for="customer in customers"
              :key="customer.id"
              class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40"
            >
              <td class="py-4 px-6 font-medium text-gray-900 dark:text-white flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs">
                  {{ customer.name ? customer.name.charAt(0).toUpperCase() : 'C' }}
                </div>
                <span>{{ customer.name }}</span>
              </td>
              <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                {{ customer.email }}
              </td>
              <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                {{ customer.phone || 'N/A' }}
              </td>
              <td class="py-4 px-6 text-xs text-gray-500">
                {{ customer.address || 'N/A' }}
              </td>
              <td class="py-4 px-6 text-xs text-gray-500">
                {{ customer.created_at ? new Date(customer.created_at).toLocaleDateString() : 'Recent' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
