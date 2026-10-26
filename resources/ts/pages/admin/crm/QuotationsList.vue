<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  quotations: any
  cars: any[]
  customers: any[]
  leads: any[]
  filters: {
    search?: string
    status?: string
  }
}>()

const searchQuery = ref(props.filters.search || '')
const selectedStatus = ref(props.filters.status || 'all')
const selectedQuoteForView = ref<any>(null)
const showViewModal = ref(false)

const formatCurrency = (val: number | string) => {
  const num = Number(val) || 0
  
  return '$' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const applyFilters = () => {
  router.get('/admin/crm/quotations', {
    search: searchQuery.value || undefined,
    status: selectedStatus.value === 'all' ? undefined : selectedStatus.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

let searchTimer: any = null

const onSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    applyFilters()
  }, 350)
}

const updateStatus = (quoteId: number, newStatus: string) => {
  router.patch(`/admin/crm/quotations/${quoteId}/status`, {
    status: newStatus,
  }, {
    preserveScroll: true,
  })
}

const deleteQuotation = (quote: any) => {
  if (confirm(`Delete quotation ${quote.quotation_number}?`)) {
    router.delete(`/admin/crm/quotations/${quote.id}`, {
      preserveScroll: true,
    })
  }
}

const openQuoteView = (quote: any) => {
  selectedQuoteForView.value = quote
  showViewModal.value = true
}

const printQuotation = () => {
  window.print()
}
</script>

<template>
  <CrmLayout>
    <Head title="Rental Quotations & Proposals - CRM" />

    <div class="space-y-6 pb-12">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            Rental Quotations & Proposals
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Generate and manage professional car rental estimates with custom add-ons and taxes.
          </p>
        </div>

        <Link
          href="/admin/crm/quotations/create"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-sm"
        >
          <i class="ri-file-add-line text-base" />
          <span>New Quotation</span>
        </Link>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="relative w-full sm:w-80">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by quote number or recipient..."
            class="w-full ps-10 pe-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @input="onSearch"
          >
        </div>

        <div class="flex items-center gap-2">
          <select
            v-model="selectedStatus"
            class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium"
            @change="applyFilters"
          >
            <option value="all">
              All Statuses
            </option>
            <option value="draft">
              Draft
            </option>
            <option value="sent">
              Sent to Client
            </option>
            <option value="accepted">
              Accepted
            </option>
            <option value="rejected">
              Rejected
            </option>
            <option value="expired">
              Expired
            </option>
          </select>
        </div>
      </div>

      <!-- Quotations Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-5 py-3.5">
                  Quote Reference
                </th>
                <th class="px-5 py-3.5">
                  Client / Lead
                </th>
                <th class="px-5 py-3.5">
                  Vehicle
                </th>
                <th class="px-5 py-3.5">
                  Rental Period
                </th>
                <th class="px-5 py-3.5">
                  Status
                </th>
                <th class="px-5 py-3.5">
                  Total Amount
                </th>
                <th class="px-5 py-3.5 text-right">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-if="!quotations?.data?.length">
                <td
                  colspan="7"
                  class="text-center py-10 text-slate-400 text-sm"
                >
                  No quotations created yet. Click "New Quotation" to build a rental proposal.
                </td>
              </tr>
              <tr
                v-for="quote in quotations?.data || []"
                :key="quote.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition"
              >
                <!-- Reference -->
                <td class="px-5 py-4">
                  <div class="font-bold text-slate-900 dark:text-white font-mono text-xs">
                    {{ quote.quotation_number }}
                  </div>
                  <div class="text-[11px] text-slate-400 mt-0.5">
                    Issued: {{ new Date(quote.created_at).toLocaleDateString() }}
                  </div>
                </td>

                <!-- Recipient -->
                <td class="px-5 py-4 text-xs">
                  <div
                    v-if="quote.customer"
                    class="font-bold text-slate-800 dark:text-slate-200"
                  >
                    {{ quote.customer.name }}
                  </div>
                  <div
                    v-else-if="quote.lead"
                    class="font-bold text-slate-800 dark:text-slate-200"
                  >
                    {{ quote.lead.first_name }} {{ quote.lead.last_name }} (Lead)
                  </div>
                  <div
                    v-else
                    class="text-slate-400"
                  >
                    Direct Proposal
                  </div>
                </td>

                <!-- Vehicle -->
                <td class="px-5 py-4 text-xs font-medium">
                  <div
                    v-if="quote.car"
                    class="flex items-center gap-1.5"
                  >
                    <i class="ri-car-line text-emerald-500" />
                    <span>{{ quote.car.brand_name }} {{ quote.car.name }}</span>
                  </div>
                  <span
                    v-else
                    class="text-slate-400"
                  >Custom Fleet</span>
                </td>

                <!-- Period -->
                <td class="px-5 py-4 text-xs">
                  <div>{{ quote.start_date }} to {{ quote.end_date }}</div>
                  <span class="text-indigo-600 dark:text-indigo-400 font-semibold">
                    {{ quote.days_count }} Days
                  </span>
                </td>

                <!-- Status -->
                <td class="px-5 py-4">
                  <select
                    :value="quote.status"
                    class="text-xs px-2.5 py-1 rounded-lg border-0 font-semibold capitalize"
                    :class="{
                      'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300': quote.status === 'draft',
                      'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': quote.status === 'sent',
                      'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': quote.status === 'accepted',
                      'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': quote.status === 'rejected',
                      'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': quote.status === 'expired',
                    }"
                    @change="updateStatus(quote.id, ($event.target as HTMLSelectElement).value)"
                  >
                    <option value="draft">
                      Draft
                    </option>
                    <option value="sent">
                      Sent
                    </option>
                    <option value="accepted">
                      Accepted
                    </option>
                    <option value="rejected">
                      Rejected
                    </option>
                    <option value="expired">
                      Expired
                    </option>
                  </select>
                </td>

                <!-- Total -->
                <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">
                  {{ formatCurrency(quote.total_amount) }}
                </td>

                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                      title="View & Print Quote"
                      @click="openQuoteView(quote)"
                    >
                      <i class="ri-printer-line text-base" />
                    </button>
                    <button
                      type="button"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                      title="Delete Quote"
                      @click="deleteQuotation(quote)"
                    >
                      <i class="ri-delete-bin-line text-base" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Printable Quote Modal -->
      <div
        v-if="showViewModal && selectedQuoteForView"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full p-8 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
          <!-- Print header -->
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
              <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Car Rental System</span>
              <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Official Rental Quotation
              </h2>
              <p class="text-xs font-mono text-slate-400">
                {{ selectedQuoteForView.quotation_number }}
              </p>
            </div>
            <div class="flex items-center gap-2">
              <button
                type="button"
                class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm"
                @click="printQuotation"
              >
                <i class="ri-printer-line" /> Print Proposal
              </button>
              <button
                class="text-slate-400 hover:text-slate-600"
                @click="showViewModal = false"
              >
                <i class="ri-close-line text-xl" />
              </button>
            </div>
          </div>

          <!-- Recipient & Dates -->
          <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800">
              <span class="text-slate-400 block mb-1 font-semibold uppercase">Prepared For</span>
              <div class="font-bold text-slate-900 dark:text-white text-sm">
                {{ selectedQuoteForView.customer?.name || (selectedQuoteForView.lead?.first_name + ' ' + (selectedQuoteForView.lead?.last_name || '')) || 'Valued Client' }}
              </div>
              <div class="text-slate-500 mt-0.5">
                {{ selectedQuoteForView.customer?.email || selectedQuoteForView.lead?.email || '' }}
              </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800">
              <span class="text-slate-400 block mb-1 font-semibold uppercase">Rental Details</span>
              <div class="font-semibold text-slate-900 dark:text-white">
                Duration: {{ selectedQuoteForView.days_count }} Days
              </div>
              <div class="text-slate-500 mt-0.5">
                {{ selectedQuoteForView.start_date }} &rarr; {{ selectedQuoteForView.end_date }}
              </div>
            </div>
          </div>

          <!-- Summary Breakdown Table -->
          <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden text-xs">
            <table class="w-full text-left">
              <thead class="bg-slate-50 dark:bg-slate-800 font-semibold text-slate-600 dark:text-slate-300">
                <tr>
                  <th class="p-3">
                    Item Description
                  </th>
                  <th class="p-3 text-right">
                    Daily / Unit
                  </th>
                  <th class="p-3 text-right">
                    Qty/Days
                  </th>
                  <th class="p-3 text-right">
                    Total
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <tr>
                  <td class="p-3 font-semibold text-slate-800 dark:text-slate-200">
                    Vehicle Daily Rental Rate ({{ selectedQuoteForView.car?.brand_name }} {{ selectedQuoteForView.car?.name }})
                  </td>
                  <td class="p-3 text-right">
                    ${{ Number(selectedQuoteForView.daily_rate).toFixed(2) }}
                  </td>
                  <td class="p-3 text-right">
                    {{ selectedQuoteForView.days_count }} days
                  </td>
                  <td class="p-3 text-right font-bold">
                    ${{ Number(selectedQuoteForView.subtotal).toFixed(2) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totals Calculation -->
          <div class="flex justify-end text-xs">
            <div class="w-64 space-y-2">
              <div class="flex justify-between text-slate-500">
                <span>Subtotal:</span>
                <span class="font-medium text-slate-800 dark:text-slate-200">${{ Number(selectedQuoteForView.subtotal).toFixed(2) }}</span>
              </div>
              <div
                v-if="Number(selectedQuoteForView.discount_amount) > 0"
                class="flex justify-between text-rose-500"
              >
                <span>Promotional Discount:</span>
                <span>-${{ Number(selectedQuoteForView.discount_amount).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-slate-500">
                <span>VAT / Tax ({{ selectedQuoteForView.tax_rate }}%):</span>
                <span class="font-medium text-slate-800 dark:text-slate-200">${{ Number(selectedQuoteForView.tax_amount).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-800">
                <span>Grand Total:</span>
                <span class="text-indigo-600 dark:text-indigo-400">${{ Number(selectedQuoteForView.total_amount).toFixed(2) }}</span>
              </div>
            </div>
          </div>

          <!-- Terms -->
          <div
            v-if="selectedQuoteForView.terms_conditions"
            class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 text-[11px] text-slate-500 whitespace-pre-wrap"
          >
            <span class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Rental Terms & Conditions:</span>
            {{ selectedQuoteForView.terms_conditions }}
          </div>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
