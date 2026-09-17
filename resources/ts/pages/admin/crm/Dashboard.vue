<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

interface DashboardData {
  metrics: {
    total_leads: number
    new_leads_this_month: number
    conversion_rate: number
    total_pipeline_value: number
    won_deals_value: number
    open_tickets: number
    urgent_tickets: number
    active_corporate_accounts: number
  }
  deals_by_stage: Record<string, { stage: string; count: number; total_value: string | number }>
  leads_by_source: Record<string, number>
  recent_leads: any[]
  recent_interactions: any[]
  recent_quotations: any[]
}

defineProps<{
  data: DashboardData
}>()

const formatCurrency = (val: number | string) => {
  const num = Number(val) || 0
  
  return '$' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const stageLabels: Record<string, { label: string; color: string }> = {
  lead_in: { label: 'Lead In', color: 'bg-blue-500' },
  needs_analysis: { label: 'Needs Analysis', color: 'bg-indigo-500' },
  vehicle_proposed: { label: 'Vehicle Proposed', color: 'bg-purple-500' },
  negotiation: { label: 'Negotiation', color: 'bg-amber-500' },
  won: { label: 'Won / Confirmed', color: 'bg-emerald-500' },
  lost: { label: 'Lost', color: 'bg-rose-500' },
}

const sourceIcons: Record<string, string> = {
  website: 'ri-global-line',
  phone: 'ri-phone-line',
  walk_in: 'ri-walk-line',
  referral: 'ri-user-shared-line',
  corporate: 'ri-building-line',
  ai_chat: 'ri-robot-2-line',
  other: 'ri-more-line',
}
</script>

<template>
  <CrmLayout>
    <Head title="CRM Command Hub & Intelligence" />

    <div class="space-y-6 pb-12">
      <!-- Header Banner -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 dark:from-slate-950 dark:via-indigo-950 dark:to-slate-900 p-6 rounded-2xl border border-indigo-900/40 text-white shadow-xl">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold uppercase tracking-wider mb-2">
            <i class="ri-shield-user-line text-sm" /> Enterprise CRM Suite
          </div>
          <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
            Car Rental CRM Intelligence
          </h1>
          <p class="text-slate-300 text-sm mt-1">
            Manage your leads, rental pipeline, customer 360 interactions, quotations & support desk.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <Link
            href="/admin/crm/leads"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition shadow-sm"
          >
            <i class="ri-user-add-line" />
            <span>Manage Leads</span>
          </Link>
          <Link
            href="/admin/crm/deals"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-medium border border-white/10 backdrop-blur-sm transition"
          >
            <i class="ri-kanban-view" />
            <span>Sales Pipeline</span>
          </Link>
          <Link
            href="/admin/crm/quotations/create"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition shadow-sm"
          >
            <i class="ri-file-add-line" />
            <span>New Quote</span>
          </Link>
        </div>
      </div>

      <!-- Key Metrics Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Leads -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Leads</span>
            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
              {{ data.metrics.total_leads }}
            </div>
            <div class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1 mt-1 font-medium">
              <i class="ri-arrow-up-line" />
              <span>+{{ data.metrics.new_leads_this_month }} this month</span>
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold border border-blue-100 dark:border-blue-900/50">
            <i class="ri-contacts-book-line" />
          </div>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Lead Conversion Rate</span>
            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
              {{ data.metrics.conversion_rate }}%
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              From lead to booked client
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-bold border border-purple-100 dark:border-purple-900/50">
            <i class="ri-funds-line" />
          </div>
        </div>

        <!-- Active Pipeline Value -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Pipeline Value</span>
            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
              {{ formatCurrency(data.metrics.total_pipeline_value) }}
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              Won: {{ formatCurrency(data.metrics.won_deals_value) }}
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-bold border border-indigo-100 dark:border-indigo-900/50">
            <i class="ri-money-dollar-circle-line" />
          </div>
        </div>

        <!-- Support Tickets Desk -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
          <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Open Tickets</span>
            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
              {{ data.metrics.open_tickets }}
            </div>
            <div class="text-xs text-rose-600 dark:text-rose-400 flex items-center gap-1 mt-1 font-medium">
              <i class="ri-alarm-warning-line" />
              <span>{{ data.metrics.urgent_tickets }} urgent tickets</span>
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-bold border border-rose-100 dark:border-rose-900/50">
            <i class="ri-customer-service-2-line" />
          </div>
        </div>
      </div>

      <!-- Pipeline Distribution & Sources Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Deals Pipeline Distribution -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="flex items-center justify-between mb-5">
            <div>
              <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                Deal Pipeline Stages
              </h2>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Volume and projected revenue by rental contract stage
              </p>
            </div>
            <Link
              href="/admin/crm/deals"
              class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1"
            >
              Open Kanban Board <i class="ri-arrow-right-line" />
            </Link>
          </div>

          <div class="space-y-4">
            <div
              v-for="(stageInfo, stageKey) in stageLabels"
              :key="stageKey"
              class="space-y-1.5"
            >
              <div class="flex items-center justify-between text-xs">
                <span class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                  <span
                    class="w-2.5 h-2.5 rounded-full"
                    :class="stageInfo.color"
                  />
                  {{ stageInfo.label }}
                </span>
                <span class="text-slate-500 dark:text-slate-400 font-semibold">
                  {{ data.deals_by_stage[stageKey]?.count || 0 }} deals &bull;
                  {{ formatCurrency(data.deals_by_stage[stageKey]?.total_value || 0) }}
                </span>
              </div>
              <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-500"
                  :class="stageInfo.color"
                  :style="{
                    width: data.metrics.total_pipeline_value > 0
                      ? `${Math.min(100, Math.max(4, ((Number(data.deals_by_stage[stageKey]?.total_value || 0) / data.metrics.total_pipeline_value) * 100)))}%`
                      : '0%'
                  }"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Lead Inflow Sources -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                Lead Channels
              </h2>
              <span class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-2 py-1 rounded-lg">Sources</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
              <div
                v-for="(count, source) in data.leads_by_source"
                :key="source"
                class="py-3 flex items-center justify-between text-sm"
              >
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i :class="sourceIcons[source] || 'ri-record-circle-line'" />
                  </div>
                  <span class="capitalize text-slate-800 dark:text-slate-200 font-medium">
                    {{ source.replace('_', ' ') }}
                  </span>
                </div>
                <span class="font-bold text-slate-900 dark:text-white px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-xs">
                  {{ count }}
                </span>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
            <span>Active B2B Accounts:</span>
            <span class="font-bold text-slate-800 dark:text-slate-200">
              {{ data.metrics.active_corporate_accounts }} Companies
            </span>
          </div>
        </div>
      </div>

      <!-- Tables Grid: Recent Leads & Recent Customer Interactions -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Inquiries / Leads -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                Recent Leads & Inquiries
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Newly captured rental prospects
              </p>
            </div>
            <Link
              href="/admin/crm/leads"
              class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
            >
              View All
            </Link>
          </div>

          <div
            v-if="!data.recent_leads?.length"
            class="text-center py-8 text-sm text-slate-400"
          >
            No leads captured yet.
          </div>

          <div
            v-else
            class="divide-y divide-slate-100 dark:divide-slate-800"
          >
            <div
              v-for="lead in data.recent_leads"
              :key="lead.id"
              class="py-3 flex items-center justify-between text-sm"
            >
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 font-semibold flex items-center justify-center text-xs">
                  {{ lead.first_name?.[0] || 'L' }}{{ lead.last_name?.[0] || '' }}
                </div>
                <div>
                  <Link
                    :href="`/admin/crm/leads/${lead.id}`"
                    class="font-medium text-slate-900 dark:text-white hover:text-indigo-600 transition"
                  >
                    {{ lead.first_name }} {{ lead.last_name }}
                  </Link>
                  <div class="text-xs text-slate-500 flex items-center gap-2">
                    <span>{{ lead.phone || lead.email || 'No contact info' }}</span>
                    <span
                      v-if="lead.interested_car"
                      class="text-indigo-500 font-medium"
                    >
                      &bull; {{ lead.interested_car.brand_name }} {{ lead.interested_car.name }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="text-right">
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize"
                  :class="{
                    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': lead.status === 'new',
                    'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300': lead.status === 'contacted',
                    'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300': lead.status === 'qualified',
                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300': lead.status === 'converted',
                    'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300': lead.status === 'lost',
                  }"
                >
                  {{ lead.status }}
                </span>
                <div class="text-xs text-slate-400 mt-1 font-semibold">
                  {{ formatCurrency(lead.estimated_value) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Customer Interactions -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                Customer Interactions Timeline
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Latest client touchpoints logged by staff
              </p>
            </div>
            <Link
              href="/admin/customers"
              class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
            >
              Customer Directory
            </Link>
          </div>

          <div
            v-if="!data.recent_interactions?.length"
            class="text-center py-8 text-sm text-slate-400"
          >
            No customer interactions logged yet.
          </div>

          <div
            v-else
            class="space-y-3"
          >
            <div
              v-for="interaction in data.recent_interactions"
              :key="interaction.id"
              class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-sm flex items-start gap-3"
            >
              <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 mt-0.5">
                <i
                  :class="{
                    'ri-phone-fill': interaction.type === 'call',
                    'ri-mail-fill': interaction.type === 'email',
                    'ri-team-fill': interaction.type === 'meeting',
                    'ri-file-text-fill': interaction.type === 'note',
                    'ri-whatsapp-fill': interaction.type === 'whatsapp',
                    'ri-message-3-fill': interaction.type === 'sms',
                  }"
                />
              </div>

              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                  <span class="font-semibold text-slate-900 dark:text-white truncate">
                    {{ interaction.subject }}
                  </span>
                  <span class="text-xs text-slate-400 shrink-0">
                    {{ new Date(interaction.interaction_date).toLocaleDateString() }}
                  </span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                  {{ interaction.details }}
                </div>
                <div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-1 font-medium flex items-center gap-2">
                  <span v-if="interaction.customer">
                    <i class="ri-user-line" /> {{ interaction.customer.name }}
                  </span>
                  <span
                    v-if="interaction.admin"
                    class="text-slate-400"
                  >
                    &bull; by {{ interaction.admin.name }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
