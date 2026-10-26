<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  tickets: any
  customers: any[]
  cars: any[]
  filters: {
    search?: string
    status?: string
    priority?: string
    category?: string
  }
}>()

const activeStatus = ref(props.filters.status || 'all')
const searchQuery = ref(props.filters.search || '')
const selectedPriority = ref(props.filters.priority || 'all')
const selectedCategory = ref(props.filters.category || 'all')

const showCreateModal = ref(false)

const ticketForm = useForm({
  customer_id: '',
  car_id: '',
  subject: '',
  category: 'general',
  priority: 'medium',
  initial_message: '',
})

const applyFilters = () => {
  router.get('/admin/crm/tickets', {
    status: activeStatus.value === 'all' ? undefined : activeStatus.value,
    search: searchQuery.value || undefined,
    priority: selectedPriority.value === 'all' ? undefined : selectedPriority.value,
    category: selectedCategory.value === 'all' ? undefined : selectedCategory.value,
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

const setStatusTab = (status: string) => {
  activeStatus.value = status
  applyFilters()
}

const submitCreateTicket = () => {
  ticketForm.post('/admin/crm/tickets', {
    onSuccess: () => {
      showCreateModal.value = false
      ticketForm.reset()
    },
  })
}
</script>

<template>
  <CrmLayout>
    <Head title="Support & Incident Desk - CRM" />

    <div class="space-y-6 pb-12">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            Customer Support & Incidents
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Resolve roadside breakdowns, rental extensions, vehicle complaints, and billing issues.
          </p>
        </div>

        <button
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-sm"
          @click="showCreateModal = true"
        >
          <i class="ri-customer-service-2-line text-base" />
          <span>New Support Ticket</span>
        </button>
      </div>

      <!-- Status Tabs -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200 dark:border-slate-800">
        <button
          type="button"
          class="px-4 py-2 text-sm font-medium rounded-xl transition"
          :class="activeStatus === 'all' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
          @click="setStatusTab('all')"
        >
          All Tickets
        </button>
        <button
          v-for="st in ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed']"
          :key="st"
          type="button"
          class="px-4 py-2 text-sm font-medium rounded-xl transition capitalize"
          :class="activeStatus === st ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'"
          @click="setStatusTab(st)"
        >
          {{ st.replace('_', ' ') }}
        </button>
      </div>

      <!-- Toolbar -->
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="relative w-full md:w-80">
          <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search tickets or customer..."
            class="w-full ps-10 pe-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @input="onSearch"
          >
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
          <!-- Priority Filter -->
          <select
            v-model="selectedPriority"
            class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
            @change="applyFilters"
          >
            <option value="all">
              All Priorities
            </option>
            <option value="low">
              Low
            </option>
            <option value="medium">
              Medium
            </option>
            <option value="high">
              High
            </option>
            <option value="urgent">
              Urgent
            </option>
          </select>

          <!-- Category Filter -->
          <select
            v-model="selectedCategory"
            class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
            @change="applyFilters"
          >
            <option value="all">
              All Categories
            </option>
            <option value="roadside_assistance">
              Roadside Assistance
            </option>
            <option value="billing">
              Billing & Deposits
            </option>
            <option value="extension">
              Rental Extension
            </option>
            <option value="vehicle_complaint">
              Vehicle Complaint
            </option>
            <option value="general">
              General Inquiries
            </option>
          </select>
        </div>
      </div>

      <!-- Tickets Table -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="px-5 py-3.5">
                  Ticket #
                </th>
                <th class="px-5 py-3.5">
                  Subject & Category
                </th>
                <th class="px-5 py-3.5">
                  Customer
                </th>
                <th class="px-5 py-3.5">
                  Vehicle
                </th>
                <th class="px-5 py-3.5">
                  Priority
                </th>
                <th class="px-5 py-3.5">
                  Status
                </th>
                <th class="px-5 py-3.5 text-right">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-if="!tickets?.data?.length">
                <td
                  colspan="7"
                  class="text-center py-10 text-slate-400 text-sm"
                >
                  No support tickets found.
                </td>
              </tr>
              <tr
                v-for="ticket in tickets?.data || []"
                :key="ticket.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition"
              >
                <!-- Ticket # -->
                <td class="px-5 py-4 font-mono font-bold text-xs text-slate-900 dark:text-white">
                  {{ ticket.ticket_number }}
                </td>

                <!-- Subject & Category -->
                <td class="px-5 py-4">
                  <Link
                    :href="`/admin/crm/tickets/${ticket.id}`"
                    class="font-semibold text-slate-900 dark:text-white hover:text-indigo-600 transition"
                  >
                    {{ ticket.subject }}
                  </Link>
                  <div class="text-[11px] text-slate-400 mt-0.5 capitalize flex items-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500" />
                    {{ ticket.category.replace('_', ' ') }}
                  </div>
                </td>

                <!-- Customer -->
                <td class="px-5 py-4 text-xs font-medium text-slate-800 dark:text-slate-200">
                  <span v-if="ticket.customer">{{ ticket.customer.name }}</span>
                  <span
                    v-else
                    class="text-slate-400"
                  >Anonymous</span>
                </td>

                <!-- Vehicle -->
                <td class="px-5 py-4 text-xs">
                  <span
                    v-if="ticket.car"
                    class="font-medium text-slate-700 dark:text-slate-300"
                  >
                    {{ ticket.car.brand_name }} {{ ticket.car.name }}
                  </span>
                  <span
                    v-else
                    class="text-slate-400"
                  >-</span>
                </td>

                <!-- Priority -->
                <td class="px-5 py-4">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold capitalize"
                    :class="{
                      'bg-slate-100 text-slate-700 dark:bg-slate-800': ticket.priority === 'low',
                      'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': ticket.priority === 'medium',
                      'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300': ticket.priority === 'high',
                      'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300': ticket.priority === 'urgent',
                    }"
                  >
                    {{ ticket.priority }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-5 py-4">
                  <span
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold capitalize"
                    :class="{
                      'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': ticket.status === 'open',
                      'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': ticket.status === 'in_progress',
                      'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300': ticket.status === 'waiting_customer',
                      'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': ticket.status === 'resolved',
                      'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300': ticket.status === 'closed',
                    }"
                  >
                    {{ ticket.status.replace('_', ' ') }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-5 py-4 text-right">
                  <Link
                    :href="`/admin/crm/tickets/${ticket.id}`"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-semibold text-xs hover:bg-indigo-100 transition"
                  >
                    <span>Open Case</span>
                    <i class="ri-arrow-right-line" />
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Ticket Modal -->
      <div
        v-if="showCreateModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              Create Support Ticket
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
            @submit.prevent="submitCreateTicket"
          >
            <div>
              <label class="block text-xs font-semibold mb-1">Customer *</label>
              <select
                v-model="ticketForm.customer_id"
                required
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
                <option value="">
                  Select customer
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
              <label class="block text-xs font-semibold mb-1">Related Car (Optional)</label>
              <select
                v-model="ticketForm.car_id"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
                <option value="">
                  None / Not vehicle specific
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

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Category</label>
                <select
                  v-model="ticketForm.category"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="roadside_assistance">
                    Roadside Breakdown / Flat Tire
                  </option>
                  <option value="billing">
                    Deposit & Billing Query
                  </option>
                  <option value="extension">
                    Rental Extension
                  </option>
                  <option value="vehicle_complaint">
                    Vehicle Cleanliness / Issue
                  </option>
                  <option value="general">
                    General Support
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold mb-1">Priority</label>
                <select
                  v-model="ticketForm.priority"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="low">
                    Low
                  </option>
                  <option value="medium">
                    Medium
                  </option>
                  <option value="high">
                    High
                  </option>
                  <option value="urgent">
                    Urgent
                  </option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Subject / Issue Summary *</label>
              <input
                v-model="ticketForm.subject"
                required
                type="text"
                placeholder="e.g. Battery dead near airport parking lot"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Initial Incident Description *</label>
              <textarea
                v-model="ticketForm.initial_message"
                required
                rows="3"
                placeholder="Provide complete incident details..."
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              />
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
              <button
                type="button"
                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
                @click="showCreateModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="ticketForm.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm"
              >
                {{ ticketForm.processing ? 'Opening...' : 'Open Case Ticket' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
