<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'

const props = defineProps<{
  cars: {
    data: Array<any>
    current_page: number
    last_page: number
    total: number
    links: Array<{ url: string | null; label: string; active: boolean }>
  }
  drivers?: Array<any>
  statusCounts?: {
    all: number
    approved: number
    pending: number
    rejected: number
  }
  filters?: {
    status?: string
    search?: string
  }
}>()

const search = ref(props.filters?.search || '')
const currentStatus = ref(props.filters?.status || 'all')

const applyFilters = () => {
  router.get(
    '/owner/cars',
    {
      search: search.value || undefined,
      status: currentStatus.value !== 'all' ? currentStatus.value : undefined,
    },
    {
      preserveState: true,
      replace: true,
    },
  )
}

let debounceTimeout: any = null
watch(search, () => {
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    applyFilters()
  }, 350)
})

const setStatus = (st: string) => {
  currentStatus.value = st
  applyFilters()
}

const deleteCar = (id: number, name: string) => {
  if (confirm(`Are you sure you want to delete "${name}" from your fleet?`)) {
    router.delete(`/owner/cars/${id}`)
  }
}
</script>

<template>
  <OwnerLayout>
    <Head title="My Fleet Vehicles" />

    <div class="space-y-6 max-w-7xl mx-auto">
      <!-- Header & Action Row -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
            Vehicle Fleet Portfolio
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Manage your registered cars, monitor verification status, and assign designated chauffeurs.
          </p>
        </div>

        <Link
          href="/owner/cars/create"
          class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all self-start sm:self-auto cursor-pointer"
        >
          <i class="ri-add-circle-line text-base" />
          <span>Register New Vehicle</span>
        </Link>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <!-- Status Tabs with Counts -->
        <div class="flex flex-wrap items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl">
          <button
            type="button"
            :class="currentStatus === 'all' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('all')"
          >
            <span>All Cars</span>
            <span
              :class="currentStatus === 'all' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.all ?? cars.total }}
            </span>
          </button>
          <button
            type="button"
            :class="currentStatus === 'approved' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('approved')"
          >
            <span>Approved</span>
            <span
              :class="currentStatus === 'approved' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.approved ?? 0 }}
            </span>
          </button>
          <button
            type="button"
            :class="currentStatus === 'pending' ? 'bg-white dark:bg-slate-900 text-amber-700 dark:text-amber-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('pending')"
          >
            <span>Pending Verification</span>
            <span
              :class="currentStatus === 'pending' ? 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.pending ?? 0 }}
            </span>
          </button>
          <button
            type="button"
            :class="currentStatus === 'rejected' ? 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-300 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer inline-flex items-center gap-1.5"
            @click="setStatus('rejected')"
          >
            <span>Rejected</span>
            <span
              :class="currentStatus === 'rejected' ? 'bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-bold font-mono min-w-[18px] text-center"
            >
              {{ statusCounts?.rejected ?? 0 }}
            </span>
          </button>
        </div>

        <!-- Search Input -->
        <div class="relative min-w-[240px] md:w-72">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm" />
          <input
            v-model="search"
            type="text"
            placeholder="Search make, model, plate..."
            class="w-full ps-9 pe-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
          >
        </div>
      </div>

      <!-- Cars Grid -->
      <div
        v-if="cars?.data && cars.data.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <div
          v-for="car in cars.data"
          :key="car.id"
          class="group rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all overflow-hidden flex flex-col justify-between"
        >
          <!-- Car Image & Top Badge -->
          <div class="relative h-48 bg-slate-100 dark:bg-slate-800 overflow-hidden">
            <img
              :src="car.car_photo ? '/' + car.car_photo : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&auto=format&fit=crop&q=80'"
              :alt="car.car_name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent" />

            <!-- Status Badge -->
            <div class="absolute top-3 right-3">
              <span
                v-if="car.status === 'approved' || car.status === 'verified' || car.status === 'active'"
                class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-500/90 text-white backdrop-blur-md shadow-xs flex items-center gap-1"
              >
                <i class="ri-checkbox-circle-fill text-xs" /> Approved
              </span>
              <span
                v-else-if="car.status === 'rejected' || car.status === 'declined' || car.status === 'disapproved'"
                class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-rose-500/90 text-white backdrop-blur-md shadow-xs flex items-center gap-1"
              >
                <i class="ri-close-circle-fill text-xs" /> Rejected
              </span>
              <span
                v-else
                class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-amber-500/90 text-white backdrop-blur-md shadow-xs flex items-center gap-1"
              >
                <i class="ri-time-fill text-xs" /> Pending Review
              </span>
            </div>

            <!-- Price Overlay Bottom Left -->
            <div class="absolute bottom-3 left-3 text-white">
              <span class="text-xl font-black">${{ car.car_price_per_day }}</span>
              <span class="text-xs text-emerald-200 font-medium"> / day</span>
              <span
                v-if="car.car_price_per_km"
                class="text-[11px] text-slate-300 block font-mono"
              >+ ${{ car.car_price_per_km }}/km</span>
            </div>
          </div>

          <!-- Car Details Body -->
          <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
            <div>
              <div class="flex items-start justify-between gap-2">
                <div>
                  <h3 class="font-bold text-base text-slate-900 dark:text-white">
                    {{ car.car_name }} {{ car.car_model }}
                  </h3>
                  <p class="text-xs font-mono text-slate-400 mt-0.5">
                    {{ car.car_number }}
                  </p>
                </div>
                <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-600 dark:text-slate-300 flex items-center gap-1 shrink-0">
                  <i class="ri-user-3-line text-xs" /> {{ car.number_of_seats }} Seats
                </span>
              </div>

              <!-- Driver Roster Assignment Detail -->
              <div class="mt-4 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs space-y-1">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                  <span class="text-[11px]">Assigned Chauffeur:</span>
                  <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[140px]">
                    {{ car.driver?.name || car.driver_name || 'No Chauffeur' }}
                  </span>
                </div>
                <div
                  v-if="car.driver?.phone || car.driver_number"
                  class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[11px]"
                >
                  <span>Contact:</span>
                  <span class="font-mono text-slate-700 dark:text-slate-300">{{ car.driver?.phone || car.driver_number }}</span>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
              <Link
                :href="`/owner/cars/${car.id}`"
                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
              >
                <i class="ri-eye-line text-sm" /> View
              </Link>
              <div class="flex items-center gap-1.5">
                <Link
                  :href="`/owner/cars/${car.id}/edit`"
                  class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
                >
                  <i class="ri-edit-line text-sm" /> Edit
                </Link>
                <button
                  type="button"
                  class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold transition-all cursor-pointer"
                  title="Delete Car"
                  @click="deleteCar(car.id, `${car.car_name} ${car.car_model}`)"
                >
                  <i class="ri-delete-bin-line text-base" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4"
      >
        <div class="w-16 h-16 rounded-3xl bg-emerald-50 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mx-auto">
          <i class="ri-car-line" />
        </div>
        <div>
          <h3 class="font-black text-lg text-slate-900 dark:text-white">
            No Vehicles Found
          </h3>
          <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
            {{ search || currentStatus !== 'all' ? 'No vehicles match your active search filter.' : 'You have not registered any vehicles under your fleet owner account yet.' }}
          </p>
        </div>
        <div class="pt-2">
          <Link
            href="/owner/cars/create"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
          >
            <i class="ri-add-circle-line" /> Register Your First Car
          </Link>
        </div>
      </div>

      <!-- Pagination Links -->
      <div
        v-if="cars?.links && cars.links.length > 3"
        class="flex items-center justify-center gap-1.5 pt-4"
      >
        <Link
          v-for="(link, i) in cars.links"
          :key="i"
          :href="link.url || '#'"
          :class="[
            link.active
              ? 'bg-emerald-600 text-white font-bold'
              : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800',
            !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'cursor-pointer'
          ]"
          class="px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-200/80 dark:border-slate-800 transition-all"
        >
          <span v-html="link.label" />
        </Link>
      </div>
    </div>
  </OwnerLayout>
</template>
