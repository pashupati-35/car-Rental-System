<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

defineProps<{
  cars: Array<any>
}>()
</script>

<template>
  <FrontendLayout>
    <Head title="Browse Vehicles" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      <div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
          Our Rental Fleet
        </h1>
        <p class="text-sm text-gray-500 mt-1">
          Select from our fleet of high-quality, verified rental vehicles
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="car in cars"
          :key="car.id"
          class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-lg transition-all"
        >
          <div>
            <img
              :src="car.image ? '/' + car.image : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=400&auto=format&fit=crop&q=80'"
              class="w-full h-48 object-cover"
              :alt="car.model"
            >
            <div class="p-5">
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="font-bold text-lg text-gray-900 dark:text-white">
                    {{ car.brand }} {{ car.model }}
                  </h3>
                  <p class="text-xs text-gray-500 font-mono">
                    {{ car.car_number || car.plate_number }}
                  </p>
                </div>
                <div class="text-right">
                  <span class="text-lg font-extrabold text-blue-600">${{ car.price_per_day || car.rate || 0 }}</span>
                  <span class="text-xs text-gray-500 block">/day</span>
                </div>
              </div>

              <div class="mt-4 flex items-center gap-3 text-xs text-gray-600 dark:text-gray-400">
                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-800">{{ car.transmission || 'Automatic' }}</span>
                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-800">{{ car.fuel_type || 'Petrol' }}</span>
                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-800">{{ car.seats || 5 }} Seats</span>
              </div>
            </div>
          </div>

          <div class="p-5 pt-0">
            <Link
              :href="`/cars/${car.id}`"
              class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs text-center block shadow-md shadow-blue-500/20 transition-all"
            >
              View Details & Book
            </Link>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>
