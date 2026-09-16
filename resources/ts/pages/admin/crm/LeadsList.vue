<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  leads: any
  counts: Record<string, number>
  cars: any[]
  filters: {
    search?: string
    status?: string
    source?: string
    priority?: string
  }
}>()

const activeTab = ref(props.filters.status || 'all')
const searchQuery = ref(props.filters.search || '')
const selectedSource = ref(props.filters.source || 'all')
const selectedPriority = ref(props.filters.priority || 'all')

const showCreateModal = ref(false)
const showConvertModal = ref(false)
const selectedLeadForConvert = ref<any>(null)

const leadForm = useForm({
  first_name: '',
  last_name: '',
  company_name: '',
  email: '',
  phone: '',
  source: 'website',
  status: 'new',
  priority: 'medium',
  estimated_value: 0,
  interested_car_id: '',
  pickup_date: '',
  return_date: '',
  notes: '',
})

const convertForm = useForm({
  create_deal: true,
  deal_title: '',
})

const leadsList = computed(() => {
  return Array.isArray(props.leads) ? props.leads : (props.leads?.data || [])
})

const applyFilters = () => {
  router.get('/admin/crm/leads', {
    status: activeTab.value === 'all' ? undefined : activeTab.value,
    search: searchQuery.value || undefined,
    source: selectedSource.value === 'all' ? undefined : selectedSource.value,
    priority: selectedPriority.value === 'all' ? undefined : selectedPriority.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const setTab = (tab: string) => {
  activeTab.value = tab
  applyFilters()
}

let searchTimer: any = null
const onSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    applyFilters()
  }, 350)
}

const openCreateModal = () => {
  leadForm.reset()
  showCreateModal.value = true
}

const submitCreateLead = () => {
  leadForm.post('/admin/crm/leads', {
    onSuccess: () => {
      showCreateModal.value = false
      leadForm.reset()
    },
  })
}

const openConvertModal = (lead: any) => {
  selectedLeadForConvert.value = lead
  convertForm.deal_title = `Rental Contract for ${lead.first_name} ${lead.last_name || ''}`.trim()
  showConvertModal.value = true
}

const submitConvertLead = () => {
  if (!selectedLeadForConvert.value) return
  convertForm.post(`/admin/crm/leads/${selectedLeadForConvert.value.id}/convert`, {
    onSuccess: () => {
      showConvertModal.value = false
      selectedLeadForConvert.value = null
    },
  })
}

const deleteLead = (lead: any) => {
  if (confirm(`Are you sure you want to delete lead for "${lead.first_name} ${lead.last_name || ''}"?`)) {
    router.delete(`/admin/crm/leads/${lead.id}`, {
      preserveScroll: true,
    })
  }
}
</script>

<template>
  <CrmLayout>
    <Head title="Leads & Inquiries - CRM" />

    <div class="space-y-6 pb-12">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Leads & Inquiries</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Capture, track, qualify and convert prospective car rental clients.
          </p>
        </div>

        <button
          type="button"
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-sm"
        >
          <i class="ri-user-add-line text-base" />
          <span>New Lead</span>
        </button>
      </div>

      <!-- Status Tabs Bar -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200 dark:border-slate-800">
        <button
          type="button"
          @click="setTab('all')"
          class="px-4 py-2 text-sm font-medium rounded-xl transition flex items-center gap-2 shrink-0"
          :class="activeTab === 'all' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
        >
          <span>All Leads</span>
          <span class="text-xs px-2 py-0.5 rounded-full" :class="activeTab === 'all' ? 'bg-indigo-700 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300'">
            {{ counts.all || 0 }}
          </span>
        </button>

        <button
          v-for="statusKey in ['new', 'contacted', 'qualified', 'proposal_sent', 'converted', 'lost']"
          :key="statusKey"
          type="button"
          @click="setTab(statusKey)"
          class="px-4 py-2 text-sm font-medium rounded-xl transition flex items-center gap-2 shrink-0 capitalize"
          :class="activeTab === statusKey ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
        >
          <span>{{ statusKey.replace('_', ' ') }}</span>
          <span class="text-xs px-2 py-0.5 rounded-full" :class="activeTab === statusKey ? 'bg-indigo-700 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300'">
            {{ counts[statusKey] || 0 }}
          </span>
        </button>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="relative w-full md:w-80">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            @input="onSearch"
            type="text"
            placeholder="Search leads by name, email, phone..."
            class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
          <!-- Source Filter -->
          <select
            v-model="selectedSource"
            @change="applyFilters"
            class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="all">All Sources</option>
            <option value="website">Website</option>
            <option value="phone">Phone Call</option>
            <option value="walk_in">Walk In</option>
            <option value="referral">Referral</option>
            <option value="corporate">Corporate</option>
            <option value="ai_chat">AI Chatbot</option>
            <option value="other">Other</option>
          </select>

          <!-- Priority Filter -->
          <select
            v-model="selectedPriority"
            @change="applyFilters"
            class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="all">All Priorities</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
      </div>

      <!-- Leads Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-5 py-3.5">Lead Contact</th>
                <th class="px-5 py-3.5">Vehicle Interest</th>
                <th class="px-5 py-3.5">Source</th>
                <th class="px-5 py-3.5">Priority</th>
                <th class="px-5 py-3.5">Status</th>
                <th class="px-5 py-3.5">Estimated Value</th>
                <th class="px-5 py-3.5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-if="!leadsList.length">
                <td colspan="7" class="text-center py-10 text-slate-400 text-sm">
                  No leads found matching your criteria.
                </td>
              </tr>
              <tr
                v-for="lead in leadsList"
                :key="lead.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition"
              >
                <!-- Lead Contact -->
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center text-xs shrink-0">
                      {{ lead.first_name?.[0] || 'L' }}
                    </div>
                    <div>
                      <Link
                        :href="`/admin/crm/leads/${lead.id}`"
                        class="font-semibold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                      >
                        {{ lead.first_name }} {{ lead.last_name }}
                      </Link>
                      <div v-if="lead.company_name" class="text-xs text-slate-400 font-medium">
                        {{ lead.company_name }}
                      </div>
                      <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                        <span v-if="lead.email">{{ lead.email }}</span>
                        <span v-if="lead.phone">&bull; {{ lead.phone }}</span>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Vehicle Interest -->
                <td class="px-5 py-4">
                  <div v-if="lead.interested_car" class="font-medium text-slate-800 dark:text-slate-200">
                    {{ lead.interested_car.brand_name }} {{ lead.interested_car.name }}
                  </div>
                  <div v-else class="text-xs text-slate-400">
                    Any available vehicle
                  </div>
                  <div v-if="lead.pickup_date" class="text-xs text-slate-500 mt-0.5">
                    {{ lead.pickup_date }} <span v-if="lead.return_date">to {{ lead.return_date }}</span>
                  </div>
                </td>

                <!-- Source -->
                <td class="px-5 py-4 capitalize font-medium text-xs text-slate-700 dark:text-slate-300">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800">
                    <i class="ri-radar-line text-indigo-500" />
                    {{ lead.source.replace('_', ' ') }}
                  </span>
                </td>

                <!-- Priority -->
                <td class="px-5 py-4">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                    :class="{
                      'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300': lead.priority === 'low',
                      'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': lead.priority === 'medium',
                      'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300': lead.priority === 'high',
                      'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300': lead.priority === 'urgent',
                    }"
                  >
                    {{ lead.priority }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-5 py-4">
                  <span
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize"
                    :class="{
                      'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': lead.status === 'new',
                      'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': lead.status === 'contacted',
                      'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300': lead.status === 'qualified',
                      'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300': lead.status === 'proposal_sent',
                      'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': lead.status === 'converted',
                      'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': lead.status === 'lost',
                    }"
                  >
                    {{ lead.status.replace('_', ' ') }}
                  </span>
                </td>

                <!-- Estimated Value -->
                <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">
                  ${{ Number(lead.estimated_value || 0).toFixed(2) }}
                </td>

                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- 1-Click Convert Button -->
                    <button
                      v-if="lead.status !== 'converted'"
                      type="button"
                      @click="openConvertModal(lead)"
                      title="Convert to Customer & Deal"
                      class="px-2.5 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-semibold text-xs transition flex items-center gap-1"
                    >
                      <i class="ri-user-follow-line" />
                      <span>Convert</span>
                    </button>

                    <Link
                      :href="`/admin/crm/leads/${lead.id}`"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                      title="View Lead Details"
                    >
                      <i class="ri-eye-line text-base" />
                    </Link>

                    <button
                      type="button"
                      @click="deleteLead(lead)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                      title="Delete Lead"
                    >
                      <i class="ri-delete-bin-line text-base" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="leads?.links && leads.links.length > 3" class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
          <div class="text-xs text-slate-500">
            Showing {{ leads.from || 0 }} to {{ leads.to || 0 }} of {{ leads.total || 0 }} leads
          </div>
          <div class="flex items-center gap-1">
            <Link
              v-for="(link, i) in leads.links"
              :key="i"
              :href="link.url || '#'"
              v-html="link.label"
              class="px-3 py-1.5 text-xs rounded-lg font-medium transition"
              :class="link.active ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
              :disabled="!link.url"
            />
          </div>
        </div>
      </div>

      <!-- Create Lead Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Create New Lead</h2>
            <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <form @submit.prevent="submitCreateLead" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">First Name *</label>
                <input v-model="leadForm.first_name" required type="text" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Last Name</label>
                <input v-model="leadForm.last_name" type="text" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input v-model="leadForm.email" type="email" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone</label>
                <input v-model="leadForm.phone" type="tel" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Company Name (Optional)</label>
              <input v-model="leadForm.company_name" type="text" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Source</label>
                <select v-model="leadForm.source" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm">
                  <option value="website">Website</option>
                  <option value="phone">Phone</option>
                  <option value="walk_in">Walk-in</option>
                  <option value="referral">Referral</option>
                  <option value="corporate">Corporate</option>
                  <option value="ai_chat">AI Chat</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status</label>
                <select v-model="leadForm.status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm">
                  <option value="new">New</option>
                  <option value="contacted">Contacted</option>
                  <option value="qualified">Qualified</option>
                  <option value="proposal_sent">Proposal Sent</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Priority</label>
                <select v-model="leadForm.priority" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Estimated Value ($)</label>
                <input v-model="leadForm.estimated_value" type="number" step="0.01" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Vehicle Interest</label>
                <select v-model="leadForm.interested_car_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm">
                  <option value="">Select a Vehicle</option>
                  <option v-for="car in cars" :key="car.id" :value="car.id">
                    {{ car.brand_name }} {{ car.name }} (${{ car.price_per_day }}/day)
                  </option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Notes / Requirements</label>
              <textarea v-model="leadForm.notes" rows="3" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm" placeholder="Rental requirements, special requests, timeline..." />
            </div>

            <div class="flex justify-end gap-3 pt-3">
              <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm">
                Cancel
              </button>
              <button :disabled="leadForm.processing" type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium">
                {{ leadForm.processing ? 'Saving...' : 'Save Lead' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- 1-Click Convert Modal -->
      <div v-if="showConvertModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0">
              <i class="ri-user-follow-line" />
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900 dark:text-white">Convert Lead to Customer</h3>
              <p class="text-xs text-slate-500">Transform this inquiry into an active client record</p>
            </div>
          </div>

          <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-xs text-slate-600 dark:text-slate-300 space-y-1">
            <p><strong>Name:</strong> {{ selectedLeadForConvert?.first_name }} {{ selectedLeadForConvert?.last_name }}</p>
            <p v-if="selectedLeadForConvert?.email"><strong>Email:</strong> {{ selectedLeadForConvert?.email }}</p>
            <p v-if="selectedLeadForConvert?.phone"><strong>Phone:</strong> {{ selectedLeadForConvert?.phone }}</p>
          </div>

          <div class="space-y-3">
            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-800 dark:text-slate-200">
              <input v-model="convertForm.create_deal" type="checkbox" class="w-4 h-4 rounded text-indigo-600" />
              <span>Also create an active Deal in sales pipeline</span>
            </label>

            <div v-if="convertForm.create_deal">
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Deal Title</label>
              <input v-model="convertForm.deal_title" type="text" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm" />
            </div>
          </div>

          <div class="flex justify-end gap-2.5 pt-3">
            <button type="button" @click="showConvertModal = false" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm">
              Cancel
            </button>
            <button
              type="button"
              @click="submitConvertLead"
              :disabled="convertForm.processing"
              class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm"
            >
              {{ convertForm.processing ? 'Converting...' : 'Confirm Conversion' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
