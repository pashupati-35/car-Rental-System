<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { resolveMediaUrl } from '@/utils/helpers'

const props = defineProps<{
  featuredCars?: any
}>()

// Search & filter state
const heroSearch = ref('')
const heroSeats = ref('')
const heroCategory = ref('all')

const carsList = computed<Array<any>>(() => {
  let list: any[] = []
  if (props.featuredCars && Array.isArray(props.featuredCars.data)) {
    list = props.featuredCars.data
  } else if (Array.isArray(props.featuredCars)) {
    list = props.featuredCars
  }
  
  if (heroCategory.value === 'all') return list
  if (heroCategory.value === 'suv') {
    return list.filter(c => {
      const name = `${c.car_name} ${c.car_model}`.toLowerCase()
      
      return name.includes('fortuner') || name.includes('scorpio') || name.includes('creta') || name.includes('prado') || name.includes('suv')
    })
  }
  if (heroCategory.value === '4x4') {
    return list.filter(c => {
      const name = `${c.car_name} ${c.car_model}`.toLowerCase()
      
      return name.includes('4x4') || name.includes('4wd') || name.includes('thar') || name.includes('hilux') || name.includes('jeep')
    })
  }
  if (heroCategory.value === '7seats') {
    return list.filter(c => Number(c.number_of_seats) >= 7)
  }
  if (heroCategory.value === 'luxury') {
    return list.filter(c => Number(c.car_price_per_day) >= 120)
  }

  return list
})

const paginationData = computed(() => {
  if (props.featuredCars && (props.featuredCars.links || props.featuredCars.total)) {
    return props.featuredCars
  }
  
  return null
})

const executeHeroSearch = () => {
  router.get('/cars', {
    search: heroSearch.value || undefined,
    seats: heroSeats.value || undefined,
  })
}

// Curated high-resolution car photography mapped by model / make
const fallbackCarImages = [
  'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=1000&auto=format&fit=crop&q=85',
  'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1000&auto=format&fit=crop&q=85',
  'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=1000&auto=format&fit=crop&q=85',
  'https://images.unsplash.com/photo-1533106497176-45ae19e68ba2?w=1000&auto=format&fit=crop&q=85',
  'https://images.unsplash.com/photo-1563720223185-11003d516935?w=1000&auto=format&fit=crop&q=85',
  'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=1000&auto=format&fit=crop&q=85',
  'https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?w=1000&auto=format&fit=crop&q=85',
  'https://images.unsplash.com/photo-1551830820-330a71b99659?w=1000&auto=format&fit=crop&q=85',
]

const getCarImage = (car: any) => {
  const url = resolveMediaUrl(car.car_photo || car.image, car.car_photo_path || car.image_path, 'car')
  if (url) return url

  const name = `${car.car_name || ''} ${car.car_model || ''}`.toLowerCase()
  if (name.includes('fortuner') || name.includes('prado') || name.includes('land cruiser')) {
    return 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=1000&auto=format&fit=crop&q=85'
  }
  if (name.includes('thar') || name.includes('jeep') || name.includes('scorpio')) {
    return 'https://images.unsplash.com/photo-1533106497176-45ae19e68ba2?w=1000&auto=format&fit=crop&q=85'
  }
  if (name.includes('creta') || name.includes('seltos') || name.includes('vitara') || name.includes('taigun')) {
    return 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=1000&auto=format&fit=crop&q=85'
  }
  if (name.includes('byd') || name.includes('electric') || name.includes('ev') || name.includes('tesla')) {
    return 'https://images.unsplash.com/photo-1563720223185-11003d516935?w=1000&auto=format&fit=crop&q=85'
  }
  if (name.includes('innova') || name.includes('carnival') || name.includes('limousine')) {
    return 'https://images.unsplash.com/photo-1559416523-140ddc3d238c?w=1000&auto=format&fit=crop&q=85'
  }
  if (name.includes('hilux') || name.includes('pickup')) {
    return 'https://images.unsplash.com/photo-1551830820-330a71b99659?w=1000&auto=format&fit=crop&q=85'
  }
  if (name.includes('bmw') || name.includes('mercedes') || name.includes('sedan') || name.includes('audi')) {
    return 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=1000&auto=format&fit=crop&q=85'
  }

  const id = Number(car.id) || 0
  
  return fallbackCarImages[id % fallbackCarImages.length]
}

const getCategoryLabel = (car: any) => {
  const name = `${car.car_name || ''} ${car.car_model || ''}`.toLowerCase()
  if (name.includes('4x4') || name.includes('4wd') || name.includes('thar') || name.includes('jeep')) return '4x4 Off-Road'
  if (Number(car.number_of_seats) >= 7) return 'Luxury MPV'
  if (name.includes('electric') || name.includes('byd')) return 'Pure EV'
  if (Number(car.car_price_per_day) >= 120) return 'Premium Luxury'
  
  return 'Comfort SUV'
}
</script>

<template>
  <FrontendLayout>
    <Head title="Premium Fleet & Chauffeur Car Rental" />

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-18 lg:pb-28 bg-gradient-to-b from-blue-50/70 via-white to-gray-50/40 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
      <!-- Glow ambient backdrop effects -->
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-gradient-to-r from-blue-400/10 via-indigo-500/15 to-purple-500/10 blur-3xl pointer-events-none -z-10" />

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 relative z-10">
        <!-- Pill Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100/90 dark:bg-blue-950/80 border border-blue-200/80 dark:border-blue-800 text-blue-700 dark:text-blue-300 text-xs font-bold uppercase tracking-wider shadow-xs">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping" />
          <span>Verified Fleet • Zero Double-Booking Calendar</span>
        </div>

        <!-- Headline -->
        <h1 class="text-4xl sm:text-6xl font-black text-gray-950 dark:text-white tracking-tight max-w-4xl mx-auto leading-[1.15] font-display">
          Drive the Best Cars for Every <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">Journey & Occasion</span>
        </h1>

        <!-- Subtitle -->
        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
          Book fully inspected fleet vehicles with certified drivers and direct owner transparency. Reserve dates with our zero-conflict calendar booking system.
        </p>

        <!-- Quick Interactive Search / Filter Bar -->
        <div class="max-w-4xl mx-auto mt-8 p-3 sm:p-4 rounded-3xl bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border border-gray-200/80 dark:border-gray-800 shadow-xl shadow-blue-900/5">
          <form
            class="grid grid-cols-1 sm:grid-cols-3 gap-3"
            @submit.prevent="executeHeroSearch"
          >
            <div class="relative">
              <i class="ri-search-line absolute left-3.5 top-3 text-gray-400 text-base" />
              <input
                v-model="heroSearch"
                type="text"
                placeholder="Search car (e.g. Fortuner, Creta...)"
                class="w-full ps-10 pe-3 py-2.5 rounded-2xl bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>

            <div class="relative">
              <i class="ri-user-smile-line absolute left-3.5 top-3 text-gray-400 text-base" />
              <select
                v-model="heroSeats"
                class="w-full ps-10 pe-8 py-2.5 rounded-2xl bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">
                  Any Passenger Seats
                </option>
                <option value="4">
                  4+ Seats
                </option>
                <option value="5">
                  5+ Seats (Compact SUV)
                </option>
                <option value="7">
                  7+ Seats (Full-size MPV)
                </option>
              </select>
            </div>

            <button
              type="submit"
              class="w-full py-2.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <i class="ri-flashlight-fill text-amber-300" />
              <span>Search Fleet Directory</span>
            </button>
          </form>
        </div>

        <!-- Trust Stats Bar -->
        <div class="pt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-3xl mx-auto text-left">
          <div class="p-3 rounded-2xl bg-white/60 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
              <i class="ri-shield-check-fill text-base" />
            </div>
            <div>
              <div class="text-xs font-bold text-gray-900 dark:text-white">
                100% Inspected
              </div>
              <div class="text-[10px] text-gray-500">
                Verified Fleet
              </div>
            </div>
          </div>

          <div class="p-3 rounded-2xl bg-white/60 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
              <i class="ri-user-star-fill text-base" />
            </div>
            <div>
              <div class="text-xs font-bold text-gray-900 dark:text-white">
                Pro Drivers
              </div>
              <div class="text-[10px] text-gray-500">
                Licensed Chauffeurs
              </div>
            </div>
          </div>

          <div class="p-3 rounded-2xl bg-white/60 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
              <i class="ri-calendar-check-fill text-base" />
            </div>
            <div>
              <div class="text-xs font-bold text-gray-900 dark:text-white">
                Zero Conflict
              </div>
              <div class="text-[10px] text-gray-500">
                Real-Time Sync
              </div>
            </div>
          </div>

          <div class="p-3 rounded-2xl bg-white/60 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
              <i class="ri-customer-service-2-fill text-base" />
            </div>
            <div>
              <div class="text-xs font-bold text-gray-900 dark:text-white">
                24/7 Support
              </div>
              <div class="text-[10px] text-gray-500">
                Roadside Assist
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Cars Showcase -->
    <section class="py-12 lg:py-16 bg-gray-50/60 dark:bg-gray-950/60">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Section Header with Category Tabs -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-2 border-b border-gray-200/80 dark:border-gray-800">
          <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400">
              <i class="ri-fire-fill text-amber-500" />
              <span>Verified Top Fleet</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-gray-950 dark:text-white tracking-tight mt-1 font-display">
              Top Rated Fleet Cars
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Select any vehicle to inspect real-time calendar availability and complete booking.
            </p>
          </div>

          <!-- Category filter buttons -->
          <div class="flex flex-wrap items-center gap-1.5 p-1 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-xs font-semibold">
            <button
              type="button"
              :class="heroCategory === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
              class="px-3 py-1.5 rounded-xl transition-all cursor-pointer"
              @click="heroCategory = 'all'"
            >
              All ({{ props.featuredCars?.total || carsList.length }})
            </button>
            <button
              type="button"
              :class="heroCategory === 'suv' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
              class="px-3 py-1.5 rounded-xl transition-all cursor-pointer"
              @click="heroCategory = 'suv'"
            >
              SUVs
            </button>
            <button
              type="button"
              :class="heroCategory === '4x4' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
              class="px-3 py-1.5 rounded-xl transition-all cursor-pointer"
              @click="heroCategory = '4x4'"
            >
              4x4 Off-Road
            </button>
            <button
              type="button"
              :class="heroCategory === '7seats' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
              class="px-3 py-1.5 rounded-xl transition-all cursor-pointer"
              @click="heroCategory = '7seats'"
            >
              7+ Seats
            </button>
            <button
              type="button"
              :class="heroCategory === 'luxury' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
              class="px-3 py-1.5 rounded-xl transition-all cursor-pointer"
              @click="heroCategory = 'luxury'"
            >
              Luxury
            </button>
          </div>
        </div>

        <!-- Car Cards Grid -->
        <div
          v-if="carsList.length > 0"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7"
        >
          <div
            v-for="car in carsList"
            :key="car.id"
            class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200/80 dark:border-gray-800 shadow-sm hover:shadow-2xl hover:border-blue-500/40 dark:hover:border-blue-500/40 transition-all duration-300 overflow-hidden flex flex-col justify-between group"
          >
            <div>
              <!-- Vehicle Image Container -->
              <div class="relative h-56 bg-slate-100 dark:bg-gray-800 overflow-hidden">
                <img
                  :src="getCarImage(car)"
                  class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500"
                  :alt="car.car_name + ' ' + car.car_model"
                  loading="lazy"
                >

                <!-- Category tag & verification badge -->
                <div class="absolute top-3.5 left-3.5 flex flex-wrap gap-2">
                  <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-500/95 text-white shadow-md backdrop-blur-md flex items-center gap-1">
                    <i class="ri-checkbox-circle-fill text-xs" />
                    <span>Verified</span>
                  </span>
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/60 text-white backdrop-blur-md">
                    {{ getCategoryLabel(car) }}
                  </span>
                </div>

                <!-- License Number Badge -->
                <div class="absolute bottom-3.5 right-3.5 bg-gray-950/80 backdrop-blur-md text-gray-200 px-3 py-1 rounded-xl text-xs font-mono border border-white/10 shadow-sm">
                  {{ car.car_number }}
                </div>
              </div>

              <!-- Card Body -->
              <div class="p-6 space-y-4">
                <!-- Title & Daily Price -->
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <h3 class="font-extrabold text-lg text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                      {{ car.car_name }} {{ car.car_model }}
                    </h3>
                    <div class="flex items-center gap-1 text-xs text-amber-500 font-semibold mt-0.5">
                      <i class="ri-star-fill text-xs" />
                      <span>4.9</span>
                      <span class="text-gray-400 font-normal">(Verified Fleet)</span>
                    </div>
                  </div>

                  <div class="text-right shrink-0">
                    <div class="text-2xl font-black text-blue-600 dark:text-blue-400 leading-none">
                      ${{ car.car_price_per_day }}
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mt-0.5">/ day</span>
                  </div>
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-3 gap-2 py-2 px-3 rounded-2xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 text-center text-xs">
                  <div>
                    <span class="text-[10px] text-gray-400 block uppercase font-medium">Seats</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200 flex items-center justify-center gap-1">
                      <i class="ri-user-3-line text-xs text-blue-500" />
                      {{ car.number_of_seats }}
                    </span>
                  </div>
                  <div class="border-x border-gray-200 dark:border-gray-700/60">
                    <span class="text-[10px] text-gray-400 block uppercase font-medium">Rate / KM</span>
                    <span class="font-bold text-gray-800 dark:text-gray-200">${{ car.car_price_per_km }}</span>
                  </div>
                  <div>
                    <span class="text-[10px] text-gray-400 block uppercase font-medium">Transmission</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 capitalize">
                      {{ car.transmission || 'Automatic' }}
                    </span>
                  </div>
                </div>

                <!-- Chauffeur & Owner Info -->
                <div class="space-y-1.5 text-xs text-gray-600 dark:text-gray-300">
                  <div class="flex items-center justify-between">
                    <span class="text-gray-400">Chauffeur:</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-1">
                      <i class="ri-steering-2-line text-blue-500 text-xs" />
                      {{ car.driver?.name || car.driver_name || 'Assigned Chauffeur' }}
                    </span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-400">Fleet Owner:</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                      {{ car.owner?.full_name || 'Verified Partner' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Booking Call to Action -->
            <div class="p-6 pt-0">
              <Link
                :href="`/cars/${car.id}`"
                class="w-full py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs text-center flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 transition-all cursor-pointer"
              >
                <span>View Details & Reserve</span>
                <i class="ri-arrow-right-line text-sm" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Empty State if category filter has no match -->
        <div
          v-else
          class="p-16 text-center bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 space-y-4"
        >
          <div class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl mx-auto">
            <i class="ri-car-line" />
          </div>
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            No vehicles currently matching this category
          </h3>
          <p class="text-xs text-gray-500 max-w-md mx-auto">
            Try switching back to all categories or explore our entire fleet directory.
          </p>
          <button
            type="button"
            class="px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold cursor-pointer"
            @click="heroCategory = 'all'"
          >
            Show All Fleet Cars
          </button>
        </div>

        <!-- Pagination Section -->
        <div
          v-if="paginationData && paginationData.total > 0"
          class="pt-4"
        >
          <Pagination
            :links="paginationData.links"
            :from="paginationData.from"
            :to="paginationData.to"
            :total="paginationData.total"
            :current-page="paginationData.current_page"
            :last-page="paginationData.last_page"
            :per-page="paginationData.per_page"
            :per-page-options="[6, 12, 24, 48]"
          />
        </div>
      </div>
    </section>
  </FrontendLayout>
</template>
