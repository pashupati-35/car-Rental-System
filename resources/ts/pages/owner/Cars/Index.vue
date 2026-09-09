<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
  cars: Array<any>
}>()

const deleteCar = (id: number) => {
  if (confirm('Are you sure you want to remove this car listing?')) {
    router.delete(`/owner/cars/${id}`)
  }
}
</script>

<template>
  <AppLayout>
    <Head title="My Vehicles" />

    <template #header>
      My Listed Vehicles
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">
            Your Vehicle Portfolio
          </h3>
          <p class="text-xs text-gray-500 mt-0.5">
            Manage your cars available for customer rent
          </p>
        </div>
        <Link
          href="/owner/cars/create"
          class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5"
        >
          <i class="ri-add-line" />
          Add New Vehicle
        </Link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="car in cars"
          :key="car.id"
          class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col justify-between"
        >
          <div>
            <img
              :src="car.image ? '/' + car.image : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=400&auto=format&fit=crop&q=80'"
              class="w-full h-44 object-cover rounded-xl mb-4"
              alt="Car image"
            >
            <div class="flex items-start justify-between">
              <div>
                <h4 class="font-bold text-base text-gray-900 dark:text-white">
                  {{ car.brand }} {{ car.model }}
                </h4>
                <p class="text-xs text-gray-500 font-mono">
                  {{ car.car_number || car.plate_number }} &bull; {{ car.year || '2024' }}
                </p>
              </div>
              <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                ${{ car.price_per_day || car.rate || 0 }}/day
              </span>
            </div>
          </div>

          <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <Link
              :href="`/owner/cars/${car.id}/edit`"
              class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs font-medium transition-colors"
            >
              Edit Car
            </Link>
            <button
              class="px-3 py-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-xs font-medium transition-colors"
              @click="deleteCar(car.id)"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
