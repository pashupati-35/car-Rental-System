<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import Pagination from '@/components/Pagination.vue'

const props = defineProps<{
  cars?: any
  filters?: {
    search?: string
    seats?: string
    max_price?: number | string
    sort_by?: string
    per_page?: number
  }
}>()

const searchQuery = ref(props.filters?.search || '')
const selectedSeats = ref(props.filters?.seats || '')
const maxPrice = ref<number | ''>(props.filters?.max_price ? Number(props.filters.max_price) : '')
const sortBy = ref(props.filters?.sort_by || 'latest')
const perPage = ref(props.filters?.per_page || 9)

const carsList = computed<Array<any>>(() => {
  if (props.cars && Array.isArray(props.cars.data)) {
    return props.cars.data
  }
  
  return Array.isArray(props.cars) ? props.cars : []
})

let searchTimeout: any = null

const applyFilters = () => {
  router.get('/cars', {
    search: searchQuery.value || undefined,
    seats: selectedSeats.value || undefined,
    max_price: maxPrice.value || undefined,
    sort_by: sortBy.value || undefined,
    per_page: perPage.value || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const onSearchInput = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 400)
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedSeats.value = ''
  maxPrice.value = ''
  sortBy.value = 'latest'
  applyFilters()
}
</script>

<template>
  <FrontendLayout>
    <Head title="Explore Luxury & Economy Rental Cars" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      <!-- Header & Search Banner -->
      <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-900 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl space-y-3">
          <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold uppercase tracking-wider text-blue-100 inline-block">
            Verified Fleet Marketplace
          </span>
          <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
            Find the Perfect Car for Your Next Journey
          </h1>
          <p class="text-sm text-blue-100/90 leading-relaxed">
            Browse fully verified cars with professional drivers and direct owner transparency. Reserve dates with our zero-conflict calendar.
          </p>
        </div>

        <!-- Decorative background glow -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-400/20 rounded-full blur-3xl pointer-events-none" />
      </div>

      <!-- Search & Filters Toolbar (API Driven) -->
      <div class="p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="relative w-full md:w-80">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by car name or model..."
            class="w-full py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
            style="padding-left: 2.5rem; padding-right: 1rem"
            @input="onSearchInput"
          >
          <svg
            class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          ><path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          /></svg>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
          <select
            v-model="selectedSeats"
            class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-700 dark:text-gray-300 focus:outline-none"
            @change="applyFilters"
          >
            <option value="">
              All Seats
            </option>
            <option value="4">
              4+ Seats
            </option>
            <option value="5">
              5+ Seats
            </option>
            <option value="7">
              7+ Seats
            </option>
          </select>

          <select
            v-model="sortBy"
            class="px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-700 dark:text-gray-300 focus:outline-none"
            @change="applyFilters"
          >
            <option value="latest">
              Sort: Newest
            </option>
            <option value="price-low">
              Price: Low to High
            </option>
            <option value="price-high">
              Price: High to Low
            </option>
          </select>

          <button
            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-all shadow-md shadow-blue-500/20"
            @click="applyFilters"
          >
            Refresh Cars
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div
        v-if="loading"
        class="py-20 text-center"
      >
        <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-3" />
        <p class="text-sm text-gray-500">
          Loading verified vehicles...
        </p>
      </div>

      <!-- Car Cards Grid -->
      <div
        v-else-if="filteredCars.length > 0"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <div
          v-for="car in filteredCars"
          :key="car.id"
          class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group"
        >
          <div>
            <!-- Image & Badges -->
            <div class="relative h-52 overflow-hidden bg-slate-100 dark:bg-gray-800">
              <img
                :src="car.car_photo ? '/' + car.car_photo : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&auto=format&fit=crop&q=80'"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                :alt="car.car_name || car.car_model"
              >
              <div class="absolute top-3 left-3 flex gap-2">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase bg-emerald-500/90 text-white backdrop-blur-md shadow-sm">
                  {{ car.available === 'yes' ? 'Available' : 'Verified' }}
                </span>
              </div>
              <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-mono">
                {{ car.car_number }}
              </div>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-4">
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="font-bold text-xl text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">
                    {{ car.car_name }} {{ car.car_model }}
                  </h3>
                  <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-0.5">
                    <svg
                      class="w-3.5 h-3.5 text-blue-500"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" /></svg>
                    <span>Owner: <strong>{{ car.owner?.full_name || 'Fleet Owner' }}</strong></span>
                  </div>
                </div>
                <div class="text-right shrink-0">
                  <span class="text-2xl font-black text-blue-600 dark:text-blue-400">${{ car.car_price_per_day }}</span>
                  <span class="text-[11px] text-gray-500 block">per day</span>
                </div>
              </div>

              <!-- Driver details snippet -->
              <div class="p-3 rounded-2xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100/50 dark:border-blue-900/30 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                    {{ (car.driver?.name || car.driver_name || 'D')[0] }}
                  </div>
                  <div>
                    <span class="font-semibold text-gray-900 dark:text-gray-100 block">
                      Driver: {{ car.driver?.name || car.driver_name || 'Available on request' }}
                    </span>
                    <span class="text-[10px] text-gray-500">
                      {{ car.driver?.experience_years || car.driving_experience || '3' }} yrs exp &bull; Lic: {{ car.driver?.license_number || 'Verified' }}
                    </span>
                  </div>
                </div>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                  Included
                </span>
              </div>

              <!-- Badges -->
              <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-600 dark:text-gray-400">
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800">
                  <span class="text-[10px] text-gray-400 block">Seats</span>
                  <span class="font-bold text-gray-900 dark:text-white">{{ car.number_of_seats }} Seats</span>
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800">
                  <span class="text-[10px] text-gray-400 block">Per KM</span>
                  <span class="font-bold text-gray-900 dark:text-white">${{ car.car_price_per_km }}</span>
                </div>
                <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800">
                  <span class="text-[10px] text-gray-400 block">Approval</span>
                  <span class="font-bold text-emerald-600 capitalize">{{ car.status }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Action -->
          <div class="p-6 pt-0">
            <Link
              :href="`/cars/${car.id}`"
              class="w-full py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs text-center flex items-center justify-center gap-2 shadow-lg shadow-blue-500/25 transition-all"
            >
              <span>View Details, Owner & Book</span>
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              ><path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M14 5l7 7m0 0l-7 7m7-7H3"
              /></svg>
            </Link>
          </div>
        </div>
      </div>

      <!-- Pagination Section -->
      <div
        v-if="props.cars && props.cars.links && props.cars.total > 0"
        class="pt-4"
      >
        <Pagination
          :links="props.cars.links"
          :from="props.cars.from"
          :to="props.cars.to"
          :total="props.cars.total"
          :per-page="props.cars.per_page"
          :per-page-options="[6, 9, 12, 24, 50]"
        />
      </div>

      <!-- Empty State -->
      <div
        v-else-if="carsList.length === 0"
        class="p-16 text-center bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800"
      >
        <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto mb-4">
          🚗
        </div>
        <h3 class="font-bold text-lg text-gray-900 dark:text-white">
          No cars match your filters
        </h3>
        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
          Try adjusting your search criteria, clearing seat filters, or resetting the price filter.
        </p>
        <button
          class="mt-4 px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold cursor-pointer"
          @click="resetFilters"
        >
          Reset Filters
        </button>
      </div>
    </div>
  </FrontendLayout>
</template>
