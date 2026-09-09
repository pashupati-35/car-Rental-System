<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  cars: Array<any>
}>()
</script>

<template>
  <AppLayout>
    <Head title="Fleet Management" />

    <template #header>
      Fleet Management
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">All Registered Cars</h3>
          <p class="text-xs text-gray-500 mt-0.5">Manage vehicles, pricing, availability and fleet details</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
            <tr>
              <th class="py-3.5 px-6">Vehicle</th>
              <th class="py-3.5 px-6">Owner</th>
              <th class="py-3.5 px-6">Model / Year</th>
              <th class="py-3.5 px-6">Daily Rate</th>
              <th class="py-3.5 px-6">Status</th>
              <th class="py-3.5 px-6 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr v-for="car in cars" :key="car.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
              <td class="py-4 px-6 font-medium text-gray-900 dark:text-white flex items-center gap-3">
                <img :src="car.image ? '/' + car.image : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=120&auto=format&fit=crop&q=80'" class="w-12 h-10 object-cover rounded-lg border border-gray-100" />
                <div>
                  <div class="font-bold">{{ car.brand }} {{ car.model }}</div>
                  <span class="text-xs text-gray-500 font-mono">{{ car.car_number || car.plate_number }}</span>
                </div>
              </td>
              <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                {{ car.owner?.name || car.owner?.email || 'System' }}
              </td>
              <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                {{ car.year || '2024' }} ({{ car.transmission || 'Automatic' }})
              </td>
              <td class="py-4 px-6 font-semibold text-blue-600">
                ${{ car.price_per_day || car.rate || 0 }} / day
              </td>
              <td class="py-4 px-6">
                <span :class="car.status === 'available' || !car.status ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-50 text-amber-700'" class="px-2.5 py-1 rounded-full text-xs font-medium">
                  {{ car.status || 'Available' }}
                </span>
              </td>
              <td class="py-4 px-6 text-right">
                <Link
                  :href="`/cars/${car.id}`"
                  class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs font-medium transition-colors"
                >
                  View Details
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
