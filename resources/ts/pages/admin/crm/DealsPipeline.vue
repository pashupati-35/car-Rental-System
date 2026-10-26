<script setup lang="ts">
import { ref } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  view_mode: 'kanban' | 'list'
  stages: Record<string, { name: string; color: string; probability: number }>
  grouped_deals: Record<string, { stage: string; meta: any; deals: any[]; total_value: number; count: number }>
  deals_list: any
  cars: any[]
  customers: any[]
  corporate_accounts: any[]
  leads: any[]
  filters: {
    search?: string
    stage?: string
  }
}>()

const currentView = ref<'kanban' | 'list'>(props.view_mode || 'kanban')
const showCreateModal = ref(false)
const searchQuery = ref(props.filters.search || '')

const dealForm = useForm({
  title: '',
  lead_id: '',
  customer_id: '',
  corporate_account_id: '',
  car_id: '',
  stage: 'lead_in',
  value: 0,
  win_probability: 20,
  expected_close_date: '',
  loss_reason: '',
  notes: '',
})

const formatCurrency = (val: number | string) => {
  const num = Number(val) || 0
  
  return '$' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const toggleView = (mode: 'kanban' | 'list') => {
  currentView.value = mode
}

const openCreateModal = (prefillStage = 'lead_in') => {
  dealForm.reset()
  dealForm.stage = prefillStage
  dealForm.win_probability = props.stages[prefillStage]?.probability || 20
  showCreateModal.value = true
}

const onStageSelect = (stageKey: string) => {
  dealForm.win_probability = props.stages[stageKey]?.probability || 20
}

const submitCreateDeal = () => {
  dealForm.post('/admin/crm/deals', {
    onSuccess: () => {
      showCreateModal.value = false
      dealForm.reset()
    },
  })
}

const updateDealStage = (dealId: number, newStage: string) => {
  router.patch(`/admin/crm/deals/${dealId}/stage`, {
    stage: newStage,
  }, {
    preserveScroll: true,
  })
}

const deleteDeal = (deal: any) => {
  if (confirm(`Delete deal "${deal.title}"?`)) {
    router.delete(`/admin/crm/deals/${deal.id}`, {
      preserveScroll: true,
    })
  }
}

let searchTimer: any = null

const onSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    router.get('/admin/crm/deals', {
      search: searchQuery.value || undefined,
      view: currentView.value,
    }, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  }, 350)
}
</script>

<template>
  <CrmLayout>
    <Head title="Sales Pipeline - CRM" />

    <div class="space-y-6 pb-12">
      <!-- Header Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            Rental Deals Pipeline
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Track high-value reservations, long-term fleet contracts, and corporate agreements through sales stages.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <!-- Kanban / List view switcher -->
          <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            <button
              type="button"
              class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
              :class="currentView === 'kanban' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
              @click="toggleView('kanban')"
            >
              <i class="ri-kanban-view" /> Kanban
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5"
              :class="currentView === 'list' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
              @click="toggleView('list')"
            >
              <i class="ri-list-check" /> Table List
            </button>
          </div>

          <button
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-sm"
            @click="openCreateModal('lead_in')"
          >
            <i class="ri-add-line text-base" />
            <span>Create Deal</span>
          </button>
        </div>
      </div>

      <!-- Search & Filters -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between gap-4">
        <div class="relative w-full max-w-sm">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search deals by title or number..."
            class="w-full ps-10 pe-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @input="onSearch"
          >
        </div>

        <div class="text-xs text-slate-500">
          Showing all deals across 6 pipeline stages
        </div>
      </div>

      <!-- KANBAN VIEW -->
      <div
        v-if="currentView === 'kanban'"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 overflow-x-auto pb-4 items-start"
      >
        <div
          v-for="(stageGroup, stageKey) in grouped_deals"
          :key="stageKey"
          class="bg-slate-50 dark:bg-slate-900/70 rounded-2xl border border-slate-200 dark:border-slate-800 p-3 flex flex-col gap-3 min-w-[260px]"
        >
          <!-- Stage Header -->
          <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 min-w-0">
              <span
                class="w-2.5 h-2.5 rounded-full shrink-0"
                :class="`bg-${stageGroup.meta.color}-500`"
              />
              <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 truncate">
                {{ stageGroup.meta.name }}
              </h2>
            </div>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
              {{ stageGroup.count }}
            </span>
          </div>

          <!-- Stage Total Value -->
          <div class="text-xs text-slate-500 font-semibold flex items-center justify-between px-1">
            <span>Stage Value:</span>
            <span class="text-indigo-600 dark:text-indigo-400 font-bold">
              {{ formatCurrency(stageGroup.total_value) }}
            </span>
          </div>

          <!-- Cards Stack -->
          <div class="space-y-3 min-h-[160px]">
            <div
              v-for="deal in stageGroup.deals"
              :key="deal.id"
              class="bg-white dark:bg-slate-800 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs space-y-2.5 hover:shadow-md transition"
            >
              <!-- Deal Title & Value -->
              <div class="flex items-start justify-between gap-2">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white leading-tight">
                  {{ deal.title }}
                </h3>
                <button
                  type="button"
                  class="text-slate-400 hover:text-rose-500 transition"
                  title="Delete deal"
                  @click="deleteDeal(deal)"
                >
                  <i class="ri-delete-bin-line text-xs" />
                </button>
              </div>

              <!-- Deal Details -->
              <div class="text-xs space-y-1 text-slate-500 dark:text-slate-400">
                <div
                  v-if="deal.customer"
                  class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300 font-medium truncate"
                >
                  <i class="ri-user-line text-indigo-500" />
                  <span>{{ deal.customer.name }}</span>
                </div>
                <div
                  v-else-if="deal.lead"
                  class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300 font-medium truncate"
                >
                  <i class="ri-contacts-book-line text-indigo-500" />
                  <span>{{ deal.lead.first_name }} {{ deal.lead.last_name }}</span>
                </div>
                <div
                  v-if="deal.corporate_account"
                  class="flex items-center gap-1.5 text-purple-600 dark:text-purple-400 font-medium truncate"
                >
                  <i class="ri-building-line" />
                  <span>{{ deal.corporate_account.company_name }}</span>
                </div>
                <div
                  v-if="deal.car"
                  class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 truncate"
                >
                  <i class="ri-car-line text-emerald-500" />
                  <span>{{ deal.car.brand_name }} {{ deal.car.name }}</span>
                </div>
              </div>

              <!-- Value & Probability Bar -->
              <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
                <div class="flex items-center justify-between text-xs font-bold mb-1">
                  <span class="text-emerald-600 dark:text-emerald-400">
                    {{ formatCurrency(deal.value) }}
                  </span>
                  <span class="text-slate-400 text-[11px]">
                    {{ deal.win_probability }}% win prob
                  </span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-emerald-500 rounded-full"
                    :style="{ width: `${deal.win_probability}%` }"
                  />
                </div>
              </div>

              <!-- Stage Mover Dropdown -->
              <div class="pt-1 flex items-center justify-between text-[11px]">
                <span class="text-slate-400 font-mono">{{ deal.deal_number }}</span>
                <select
                  :value="deal.stage"
                  class="text-[11px] px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700 border-0 font-medium text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-indigo-500"
                  @change="updateDealStage(deal.id, ($event.target as HTMLSelectElement).value)"
                >
                  <option
                    v-for="(stg, key) in stages"
                    :key="key"
                    :value="key"
                  >
                    Move: {{ stg.name }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Empty stage placeholder -->
            <div
              v-if="!stageGroup.deals?.length"
              class="h-28 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center text-xs text-slate-400 font-medium"
            >
              No deals
            </div>
          </div>

          <!-- Quick Add Button at bottom of column -->
          <button
            type="button"
            class="w-full py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-medium text-slate-600 dark:text-slate-400 transition flex items-center justify-center gap-1"
            @click="openCreateModal(String(stageKey))"
          >
            <i class="ri-add-line" /> Add Deal
          </button>
        </div>
      </div>

      <!-- LIST VIEW -->
      <div
        v-else
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden"
      >
        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
          <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="px-5 py-3.5">
                Deal Number & Title
              </th>
              <th class="px-5 py-3.5">
                Client / Prospect
              </th>
              <th class="px-5 py-3.5">
                Vehicle
              </th>
              <th class="px-5 py-3.5">
                Stage
              </th>
              <th class="px-5 py-3.5">
                Deal Value
              </th>
              <th class="px-5 py-3.5">
                Win Prob
              </th>
              <th class="px-5 py-3.5 text-right">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-if="!deals_list?.data?.length">
              <td
                colspan="7"
                class="text-center py-8 text-slate-400 text-sm"
              >
                No deals found.
              </td>
            </tr>
            <tr
              v-for="deal in deals_list?.data || []"
              :key="deal.id"
              class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40"
            >
              <td class="px-5 py-4">
                <div class="font-semibold text-slate-900 dark:text-white">
                  {{ deal.title }}
                </div>
                <div class="text-xs text-slate-400 font-mono">
                  {{ deal.deal_number }}
                </div>
              </td>
              <td class="px-5 py-4 text-xs">
                <span
                  v-if="deal.customer"
                  class="font-medium text-slate-800 dark:text-slate-200"
                >
                  {{ deal.customer.name }}
                </span>
                <span
                  v-else-if="deal.lead"
                  class="font-medium text-slate-800 dark:text-slate-200"
                >
                  {{ deal.lead.first_name }} {{ deal.lead.last_name }}
                </span>
                <span
                  v-if="deal.corporate_account"
                  class="block text-purple-600 dark:text-purple-400 font-medium"
                >
                  {{ deal.corporate_account.company_name }}
                </span>
              </td>
              <td class="px-5 py-4 text-xs font-medium">
                {{ deal.car ? `${deal.car.brand_name} ${deal.car.name}` : 'Not specified' }}
              </td>
              <td class="px-5 py-4">
                <select
                  :value="deal.stage"
                  class="text-xs px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border-0 font-semibold capitalize"
                  @change="updateDealStage(deal.id, ($event.target as HTMLSelectElement).value)"
                >
                  <option
                    v-for="(stg, key) in stages"
                    :key="key"
                    :value="key"
                  >
                    {{ stg.name }}
                  </option>
                </select>
              </td>
              <td class="px-5 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                {{ formatCurrency(deal.value) }}
              </td>
              <td class="px-5 py-4 text-xs font-medium">
                {{ deal.win_probability }}%
              </td>
              <td class="px-5 py-4 text-right">
                <button
                  type="button"
                  class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 transition"
                  title="Delete deal"
                  @click="deleteDeal(deal)"
                >
                  <i class="ri-delete-bin-line" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Create Deal Modal -->
      <div
        v-if="showCreateModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              Create Pipeline Deal
            </h3>
            <button
              class="text-slate-400 hover:text-slate-600"
              @click="showCreateModal = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <form
            class="space-y-4"
            @submit.prevent="submitCreateDeal"
          >
            <div>
              <label class="block text-xs font-semibold mb-1">Deal Title *</label>
              <input
                v-model="dealForm.title"
                required
                type="text"
                placeholder="e.g. 3-Month Fleet Rental for Acme Corp"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Registered Customer (Optional)</label>
                <select
                  v-model="dealForm.customer_id"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="">
                    None (or choose from leads below)
                  </option>
                  <option
                    v-for="c in customers"
                    :key="c.id"
                    :value="c.id"
                  >
                    {{ c.name }} ({{ c.email }})
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Or Prospective Lead</label>
                <select
                  v-model="dealForm.lead_id"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="">
                    None
                  </option>
                  <option
                    v-for="l in leads"
                    :key="l.id"
                    :value="l.id"
                  >
                    {{ l.first_name }} {{ l.last_name }} ({{ l.company_name || 'Individual' }})
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">B2B Corporate Account</label>
                <select
                  v-model="dealForm.corporate_account_id"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="">
                    None
                  </option>
                  <option
                    v-for="corp in corporate_accounts"
                    :key="corp.id"
                    :value="corp.id"
                  >
                    {{ corp.company_name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Target Vehicle</label>
                <select
                  v-model="dealForm.car_id"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="">
                    Select vehicle
                  </option>
                  <option
                    v-for="car in cars"
                    :key="car.id"
                    :value="car.id"
                  >
                    {{ car.brand_name }} {{ car.name }}
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Pipeline Stage</label>
                <select
                  v-model="dealForm.stage"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                  @change="onStageSelect(dealForm.stage)"
                >
                  <option
                    v-for="(stg, key) in stages"
                    :key="key"
                    :value="key"
                  >
                    {{ stg.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Deal Value ($) *</label>
                <input
                  v-model="dealForm.value"
                  required
                  type="number"
                  step="0.01"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Win Probability (%)</label>
                <input
                  v-model="dealForm.win_probability"
                  type="number"
                  min="0"
                  max="100"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Expected Close Date</label>
              <input
                v-model="dealForm.expected_close_date"
                type="date"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Notes & Terms</label>
              <textarea
                v-model="dealForm.notes"
                rows="3"
                placeholder="Contract conditions, deposit discussion, pricing concessions..."
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              />
            </div>

            <div class="flex justify-end gap-2.5 pt-3">
              <button
                type="button"
                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
                @click="showCreateModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="dealForm.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm"
              >
                {{ dealForm.processing ? 'Saving...' : 'Save Deal' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
