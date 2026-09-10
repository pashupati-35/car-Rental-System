<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps<{
  drivers?: Array<any>
}>()

const driversList = ref<Array<any>>(props.drivers || [])
const loading = ref(false)

const fetchDrivers = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/admin/drivers')
    if (res.data.status === 'success') {
      driversList.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to load admin drivers:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (!driversList.value.length) {
    fetchDrivers()
  }
})
</script>

<template>
  <AppLayout>
    <Head title="Admin - Driver Oversight Directory" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            System Drivers Directory
          </h1>
          <p class="text-xs text-gray-500 mt-1">
            Global roster of all verified and registered drivers across fleet owners
          </p>
        </div>
      </div>

      <div v-if="loading" class="py-16 text-center">
        <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
        <p class="text-xs text-gray-500">Loading system drivers...</p>
      </div>

      <div v-else-if="driversList.length > 0" class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-500 uppercase font-semibold border-b border-gray-100 dark:border-gray-800">
              <tr>
                <th class="py-3.5 px-4">Driver</th>
                <th class="py-3.5 px-4">Phone / Email</th>
                <th class="py-3.5 px-4">Fleet Owner</th>
                <th class="py-3.5 px-4">License</th>
                <th class="py-3.5 px-4">Experience</th>
                <th class="py-3.5 px-4">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
              <tr v-for="driver in driversList" :key="driver.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                <td class="py-3.5 px-4 font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                  <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs overflow-hidden">
                    <img v-if="driver.photo" :src="'/' + driver.photo" class="w-full h-full object-cover" />
                    <span v-else>{{ driver.name[0] }}</span>
                  </div>
                  <span>{{ driver.name }}</span>
                </td>
                <td class="py-3.5 px-4 font-mono">
                  {{ driver.phone }}
                  <span v-if="driver.email" class="block text-[10px] text-gray-400">{{ driver.email }}</span>
                </td>
                <td class="py-3.5 px-4">
                  {{ driver.owner?.full_name || 'Fleet Partner' }}
                </td>
                <td class="py-3.5 px-4 font-mono font-semibold">
                  {{ driver.license_number }}
                </td>
                <td class="py-3.5 px-4">
                  {{ driver.experience_years }} Years
                </td>
                <td class="py-3.5 px-4">
                  <span
                    :class="driver.status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'"
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                  >
                    {{ driver.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-else class="p-12 text-center bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800">
        <p class="text-xs text-gray-500">No registered drivers in the system.</p>
      </div>
    </div>
  </AppLayout>
</template>
