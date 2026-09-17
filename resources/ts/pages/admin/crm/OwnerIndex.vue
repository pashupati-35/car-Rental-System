<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  owners: any
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
    router.get('/crm/owners', {
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

const partnerBadges: Record<string, { label: string; class: string }> = {
  Standard: { label: 'Standard Partner', class: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' },
  'Silver Partner': { label: 'Silver Partner', class: 'bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 font-semibold' },
  'Gold Partner': { label: 'Gold Partner', class: 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-semibold' },
  'Platinum Partner': { label: 'Platinum Partner', class: 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 font-bold' },
}
</script>

<template>
  <CrmLayout>
    <Head title="Fleet Owner 360 Directory - CRM" />

    <div class="space-y-6 pb-12">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
            <i class="ri-building-line text-emerald-600 dark:text-emerald-400" />
            Fleet Owner 360 Directory
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Manage partner fleet providers, vehicle rosters, commission splits, payout accounts, and business relationships.
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
            placeholder="Search by owner name, company, email, phone..."
            class="w-full ps-10 pe-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
            @input="handleSearch"
          >
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <span class="text-xs font-semibold text-slate-500 uppercase me-1">Partner Tier:</span>
          <button
            v-for="t in ['Standard', 'Silver Partner', 'Gold Partner', 'Platinum Partner']"
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

      <!-- Owner Cards Grid -->
      <div
        v-if="owners.data && owners.data.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5"
      >
        <div
          v-for="owner in owners.data"
          :key="owner.id"
          class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition-all hover:border-emerald-500/50 group flex flex-col justify-between"
        >
          <div>
            <!-- Card Header -->
            <div class="flex items-start justify-between gap-3 mb-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-700 text-white font-bold flex items-center justify-center text-lg shadow-sm">
                  {{ (owner.full_name || owner.first_name || 'O').charAt(0).toUpperCase() }}
                </div>
                <div>
                  <h3 class="font-bold text-slate-900 dark:text-white text-base group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">
                    {{ owner.full_name || `${owner.first_name || ''} ${owner.last_name || ''}`.trim() || 'Owner #' + owner.id }}
                  </h3>
                  <div class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                    <i class="ri-mail-line" /> {{ owner.email }}
                  </div>
                </div>
              </div>

              <!-- Partner Tier Badge -->
              <span
                class="px-2.5 py-1 rounded-md text-[10px] tracking-wide"
                :class="partnerBadges[owner.preference?.partner_tier || 'Standard']?.class"
              >
                {{ partnerBadges[owner.preference?.partner_tier || 'Standard']?.label || 'Standard' }}
              </span>
            </div>

            <!-- Contact & Terms info -->
            <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300 py-2 border-y border-slate-100 dark:border-slate-800/80 mb-4">
              <div
                v-if="owner.contact_number || owner.mobile || owner.phone"
                class="flex items-center gap-2"
              >
                <i class="ri-phone-line text-slate-400" />
                <span>{{ owner.contact_number || owner.mobile || owner.phone }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="flex items-center gap-2">
                  <i class="ri-percent-line text-slate-400" />
                  <span>Commission: <b class="text-slate-800 dark:text-slate-200">{{ owner.preference?.commission_rate || 15 }}%</b></span>
                </span>
                <span class="flex items-center gap-1">
                  <i class="ri-bank-card-line text-slate-400" />
                  <span>{{ owner.preference?.payout_frequency || 'Monthly' }} Payout</span>
                </span>
              </div>
              <div class="flex items-center gap-2">
                <i class="ri-calendar-line text-slate-400" />
                <span>Partnered since {{ new Date(owner.created_at).toLocaleDateString() }}</span>
              </div>
            </div>

            <!-- Stats Counters -->
            <div class="grid grid-cols-4 gap-2 text-center py-1">
              <div class="bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ owner.cars_count || 0 }}</span>
                <span class="text-[10px] text-slate-400">Vehicles</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ owner.drivers_count || 0 }}</span>
                <span class="text-[10px] text-slate-400">Drivers</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ owner.interactions_count || 0 }}</span>
                <span class="text-[10px] text-slate-400">Logs</span>
              </div>
              <div class="bg-slate-50 dark:bg-slate-800/60 p-2 rounded-xl">
                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ owner.support_tickets_count || 0 }}</span>
                <span class="text-[10px] text-slate-400">Tickets</span>
              </div>
            </div>
          </div>

          <!-- Actions footer -->
          <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <span
              v-if="owner.preference?.vip_partner"
              class="inline-flex items-center gap-1 text-[11px] font-semibold text-purple-600 dark:text-purple-400"
            >
              <i class="ri-vip-crown-2-fill" /> VIP Partner
            </span>
            <span
              v-else
              class="text-[11px] text-slate-400"
            >
              Standard Roster
            </span>

            <Link
              :href="`/crm/owners/${owner.id}/timeline`"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 font-semibold text-xs transition"
            >
              <span>Owner 360 Dossier</span>
              <i class="ri-arrow-right-line" />
            </Link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8"
      >
        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center text-3xl mb-3">
          <i class="ri-building-line" />
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
          No Fleet Owners Found
        </h3>
        <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">
          No fleet owners matched your search query. Try clearing filters or updating your search terms.
        </p>
      </div>

      <!-- Pagination -->
      <div
        v-if="owners.links && owners.links.length > 3"
        class="flex items-center justify-center gap-1 pt-4"
      >
        <template
          v-for="(link, i) in owners.links"
          :key="i"
        >
          <Link
            v-if="link.url"
            :href="link.url"
            class="px-3.5 py-2 rounded-xl text-xs font-semibold transition"
            :class="link.active ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
          >
            <span v-html="link.label" />
          </Link>
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
