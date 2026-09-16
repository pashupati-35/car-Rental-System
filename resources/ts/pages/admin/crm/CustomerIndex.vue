<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  customers: any
  filters: {
    search?: string
    tier?: string
  }
}>()

const searchQuery = ref(props.filters.search || '')
const selectedTier = ref(props.filters.tier || '')

let debounceTimer: any = null
const handleSearch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    router.get('/crm/customers', {
      search: searchQuery.value || undefined,
      tier: selectedTier.value || undefined,
    }, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  }, 350)
}

const filterTier = (tier: string) => {
  selectedTier.value = tier === selectedTier.value ? '' : tier
  handleSearch()
}

const tierBadges: Record<string, { label: string; class: string }> = {
  Standard: { label: 'Standard', class: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' },
  Silver: { label: 'Silver VIP', class: 'bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 font-semibold' },
  Gold: { label: 'Gold VIP', class: 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-semibold' },
  Platinum: { label: 'Platinum Elite', class: 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 font-bold' },
}
</script>

<template>
  <CrmLayout>
    <Head title="Customer 360 Directory - CRM" />

    <div class="space-y-6 pb-12">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <i class="ri-user-smile-line text-emerald-600 dark:text-emerald-400" />
            Customer 360 Directory
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Complete relationship management, loyalty tiers, booking histories, and client preferences.
          </p>
        </div>
      </div>

      <!-- Filters & Search Bar -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by customer name, email, phone..."
            class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
            @input="handleSearch"
          >
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <span class="text-xs font-semibold text-slate-500 uppercase mr-1">Loyalty Tier:</span>
          <button
            v-for="t in ['Standard', 'Silver', 'Gold', 'Platinum']"
            :key="t"
            type="button"
            class="px-3 py-1.5 rounded-lg text-xs font-medium transition cursor-pointer"
            :class="selectedTier === t ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
            @click="filterTier(t)"
          >
            {{ t }}
          </button>
        </div>
      </div>

      <!-- Customer Cards Grid -->
      <div v-if="customers.data && customers.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="cust in customers.data"
          :key="cust.id"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition-all hover:border-emerald-500/50 group flex flex-col justify-between"
        >
          <div>
            <!-- Card Header -->
            <div class="flex items-start justify-between gap-3 mb-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-bold flex items-center justify-center text-lg shadow-sm">
                  {{ (cust.name || cust.first_name || 'C').charAt(0).toUpperCase() }}
                </div>
                <div>
                  <h3 class="font-bold text-slate-900 dark:text-white text-base group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">
                    {{ cust.name || `${cust.first_name || ''} ${cust.last_name || ''}`.trim() || 'Client #' + cust.id }}
                  </h3>
                  <div class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                    <i class="ri-mail-line" /> {{ cust.email }}
                  </div>
                </div>
              </div>

              <!-- Loyalty Tier Badge -->
              <span
                class="px-2.5 py-1 rounded-md text-[10px] tracking-wide"
                :class="tierBadges[cust.preference?.loyalty_tier || 'Standard']?.class"
              >
                {{ tierBadges[cust.preference?.loyalty_tier || 'Standard']?.label || 'Standard' }}
              </span>
            </div>

            <!-- Contact & Details -->
            <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300 py-2 border-y border-slate-100 dark:border-slate-800/80 mb-4">
              <div v-if="cust.phone_number || cust.mobile" class="flex items-center gap-2">
                <i class="ri-phone-line text-slate-400" />
                <span>{{ cust.phone_number || cust.mobile }}</span>
              </div>
              <div v-if="cust.preference?.preferred_car_type" class="flex items-center gap-2">
                <i class="ri-roadster-line text-slate-400" />
                <span>Prefers: <b class="text-slate-800 dark:text-slate-200">{{ cust.preference.preferred_car_type }}</b></span>
              </div>
              <div class="flex items-center gap-2">
                <i class="ri-history-line text-slate-400" />
                <span>Client since {{ new Date(cust.created_at).toLocaleDateString() }}</span>
              </div>
            </div>

            <!-- Stats Counters -->
            <div class="grid grid-cols-4 gap-2 text-center py-1">
              <div class="bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ cust.bookings_count ?? cust.booked_cars_count ?? 0 }}</span>
                <span class="text-[10px] text-slate-400">Rentals</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ cust.interactions_count || 0 }}</span>
                <span class="text-[10px] text-slate-400">Logs</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ cust.quotations_count || 0 }}</span>
                <span class="text-[10px] text-slate-400">Quotes</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ cust.support_tickets_count || 0 }}</span>
                <span class="text-[10px] text-slate-400">Tickets</span>
              </div>
            </div>
          </div>

          <!-- Actions footer -->
          <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <span v-if="cust.preference?.vip_status" class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 dark:text-amber-400">
              <i class="ri-vip-crown-fill" /> VIP Client
            </span>
            <span v-else class="text-[11px] text-slate-400">
              Regular Account
            </span>

            <Link
              :href="`/crm/customers/${cust.id}/timeline`"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 font-semibold text-xs transition"
            >
              <span>View 360 Timeline</span>
              <i class="ri-arrow-right-line" />
            </Link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8">
        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center text-3xl mb-3">
          <i class="ri-user-search-line" />
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Customers Found</h3>
        <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">
          No customer records match your filter criteria. Try adjusting your search keyword or loyalty tier.
        </p>
      </div>

      <!-- Pagination -->
      <div v-if="customers.links && customers.links.length > 3" class="flex items-center justify-center gap-1 pt-4">
        <template v-for="(link, i) in customers.links" :key="i">
          <Link
            v-if="link.url"
            :href="link.url"
            class="px-3.5 py-2 rounded-xl text-xs font-semibold transition"
            :class="link.active ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
            v-html="link.label"
          />
          <span
            v-else
            class="px-3.5 py-2 text-xs text-slate-400 cursor-not-allowed"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </CrmLayout>
</template>
