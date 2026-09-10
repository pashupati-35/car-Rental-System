<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import axios from 'axios'

defineProps<{
  featuredCars?: Array<any>
}>()

const cars = ref<Array<any>>(props.featuredCars || [])
const loading = ref(false)

const loadCars = async () => {
  if (cars.value.length > 0) return
  loading.value = true
  try {
    const res = await axios.get('/api/cars')
    if (res.data.status === 'success') {
      cars.value = res.data.data.slice(0, 6)
    }
  } catch (err) {
    console.error('Error fetching cars:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCars()
})
</script>

<template>
  <FrontendLayout>
    <Head title="Premium Fleet & Chauffeur Car Rental" />

    <!-- Hero Section -->
    <section class="relative overflow-hidden py-16 lg:py-24 bg-gradient-to-b from-blue-50/60 via-white to-white dark:from-gray-900 dark:via-gray-950 dark:to-gray-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-xs font-bold uppercase tracking-wider">
          ⭐ Verified Fleet & Zero-Conflict Calendar Booking
        </span>
        <h1 class="text-4xl sm:text-6xl font-black text-gray-900 dark:text-white tracking-tight max-w-4xl mx-auto leading-tight">
          Drive the Best Cars for Every <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">Journey & Occasion</span>
        </h1>
        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
          Explore inspected fleet vehicles with certified drivers and direct owner transparency. Reserve dates seamlessly with zero double-booking overlap.
        </p>
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
          <Link
            href="/cars"
            class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xl shadow-blue-500/25 transition-all"
          >
            Explore Available Cars &rarr;
          </Link>
          <Link
            href="/customer/login"
            class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-bold text-sm transition-all"
          >
            Customer Sign In
          </Link>
        </div>
      </div>
    </section>

    <!-- Featured Cars Showcase -->
    <section class="py-12 bg-gray-50/50 dark:bg-gray-950/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Featured Vehicles</span>
            <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">
              Top Rated Fleet Cars
            </h2>
          </div>
          <Link
            href="/cars"
            class="text-xs font-bold text-blue-600 hover:underline"
          >
            View All {{ cars.length }}+ Cars &rarr;
          </Link>
        </div>

        <div
          v-if="cars.length > 0"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          <div
            v-for="car in cars"
            :key="car.id"
            class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group"
          >
            <div>
              <div class="relative h-52 bg-slate-100 dark:bg-gray-800 overflow-hidden">
                <img
                  :src="car.car_photo ? '/' + car.car_photo : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&auto=format&fit=crop&q=80'"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  :alt="car.car_name"
                >
                <div class="absolute top-3 left-3">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500 text-white shadow-sm">
                    Verified
                  </span>
                </div>
                <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-md text-white px-2.5 py-1 rounded-full text-xs font-mono">
                  {{ car.car_number }}
                </div>
              </div>

              <div class="p-6 space-y-3">
                <div class="flex items-start justify-between">
                  <div>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">
                      {{ car.car_name }} {{ car.car_model }}
                    </h3>
                    <p class="text-xs text-gray-500">
                      Owner: <strong>{{ car.owner?.full_name || 'Fleet Partner' }}</strong>
                    </p>
                  </div>
                  <div class="text-right">
                    <span class="text-xl font-black text-blue-600">${{ car.car_price_per_day }}</span>
                    <span class="text-[10px] text-gray-400 block">/ day</span>
                  </div>
                </div>

                <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 text-xs flex justify-between items-center text-gray-600 dark:text-gray-300">
                  <span>Driver: <strong>{{ car.driver?.name || car.driver_name || 'Assigned Driver' }}</strong></span>
                  <span>{{ car.number_of_seats }} Seats</span>
                </div>
              </div>
            </div>

            <div class="p-6 pt-0">
              <Link
                :href="`/cars/${car.id}`"
                class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs text-center block shadow-md shadow-blue-500/20 transition-all"
              >
                View Full Details & Book
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </FrontendLayout>
</template>
