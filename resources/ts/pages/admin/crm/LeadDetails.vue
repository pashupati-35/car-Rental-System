<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  lead: any
  cars: any[]
}>()

const showEditModal = ref(false)
const showConvertModal = ref(false)

const editForm = useForm({
  first_name: props.lead.first_name || '',
  last_name: props.lead.last_name || '',
  company_name: props.lead.company_name || '',
  email: props.lead.email || '',
  phone: props.lead.phone || '',
  source: props.lead.source || 'website',
  status: props.lead.status || 'new',
  priority: props.lead.priority || 'medium',
  estimated_value: props.lead.estimated_value || 0,
  interested_car_id: props.lead.interested_car_id || '',
  pickup_date: props.lead.pickup_date || '',
  return_date: props.lead.return_date || '',
  notes: props.lead.notes || '',
})

const convertForm = useForm({
  create_deal: true,
  deal_title: `Rental Contract for ${props.lead.first_name} ${props.lead.last_name || ''}`.trim(),
})

const submitEdit = () => {
  editForm.put(`/admin/crm/leads/${props.lead.id}`, {
    onSuccess: () => {
      showEditModal.value = false
    },
  })
}

const submitConvert = () => {
  convertForm.post(`/admin/crm/leads/${props.lead.id}/convert`, {
    onSuccess: () => {
      showConvertModal.value = false
    },
  })
}

const deleteLead = () => {
  if (confirm(`Are you sure you want to delete this lead?`)) {
    router.delete(`/admin/crm/leads/${props.lead.id}`)
  }
}
</script>

<template>
  <CrmLayout>
    <Head :title="`Lead: ${lead.first_name} ${lead.last_name || ''}`" />

    <div class="space-y-6 pb-12">
      <!-- Breadcrumbs & Back -->
      <div class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
        <Link
          href="/admin/crm/leads"
          class="hover:text-indigo-600 transition flex items-center gap-1"
        >
          <i class="ri-arrow-left-line" /> Back to Leads
        </Link>
        <span>/</span>
        <span>Lead #{{ lead.id }}</span>
      </div>

      <!-- Header Card -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center text-xl shrink-0">
            {{ lead.first_name?.[0] || 'L' }}{{ lead.last_name?.[0] || '' }}
          </div>
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">
                {{ lead.first_name }} {{ lead.last_name }}
              </h1>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
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
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                Priority: {{ lead.priority }}
              </span>
            </div>
            <p
              v-if="lead.company_name"
              class="text-sm text-slate-500 mt-0.5 font-medium"
            >
              {{ lead.company_name }}
            </p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <button
            v-if="lead.status !== 'converted'"
            type="button"
            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm transition flex items-center gap-1.5 shadow-sm"
            @click="showConvertModal = true"
          >
            <i class="ri-user-follow-line" />
            <span>Convert to Customer</span>
          </button>

          <button
            type="button"
            class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 text-sm font-medium transition flex items-center gap-1.5"
            @click="showEditModal = true"
          >
            <i class="ri-edit-line" />
            <span>Edit</span>
          </button>

          <button
            type="button"
            class="px-3.5 py-2 rounded-xl border border-rose-200 dark:border-rose-900/40 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-sm font-medium transition"
            @click="deleteLead"
          >
            <i class="ri-delete-bin-line" />
          </button>
        </div>
      </div>

      <!-- Grid Content -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lead Information -->
        <div class="space-y-6">
          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
              Lead Contact Details
            </h2>

            <div class="space-y-3 text-sm">
              <div>
                <span class="text-xs text-slate-400 block font-medium">Email Address</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ lead.email || 'Not provided' }}</span>
              </div>
              <div>
                <span class="text-xs text-slate-400 block font-medium">Phone Number</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ lead.phone || 'Not provided' }}</span>
              </div>
              <div>
                <span class="text-xs text-slate-400 block font-medium">Lead Acquisition Channel</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium capitalize">{{ lead.source.replace('_', ' ') }}</span>
              </div>
              <div>
                <span class="text-xs text-slate-400 block font-medium">Estimated Rental Value</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold text-base">
                  ${{ Number(lead.estimated_value || 0).toFixed(2) }}
                </span>
              </div>
              <div>
                <span class="text-xs text-slate-400 block font-medium">Created On</span>
                <span class="text-slate-600 dark:text-slate-400 text-xs">
                  {{ new Date(lead.created_at).toLocaleString() }}
                </span>
              </div>
            </div>
          </div>

          <!-- Converted Customer reference if converted -->
          <div
            v-if="lead.converted_customer"
            class="bg-emerald-50 dark:bg-emerald-950/40 p-5 rounded-2xl border border-emerald-200 dark:border-emerald-900/40 space-y-2"
          >
            <div class="flex items-center gap-2 text-emerald-800 dark:text-emerald-300 font-semibold text-sm">
              <i class="ri-checkbox-circle-fill text-base" /> Converted Customer Record
            </div>
            <p class="text-xs text-emerald-700 dark:text-emerald-400">
              This lead was converted into customer <strong>{{ lead.converted_customer.name }}</strong>.
            </p>
            <div class="pt-2">
              <Link
                :href="`/admin/crm/customers/${lead.converted_customer.id}/timeline`"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-medium hover:bg-emerald-700 transition"
              >
                <span>View Customer 360</span>
                <i class="ri-arrow-right-line" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Vehicle Interest & Notes & Linked Deals -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Vehicle Interest Card -->
          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-4">
              Vehicle & Rental Interest
            </h2>

            <div
              v-if="lead.interested_car"
              class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 gap-4"
            >
              <div>
                <div class="font-bold text-slate-900 dark:text-white text-base">
                  {{ lead.interested_car.brand_name }} {{ lead.interested_car.name }}
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                  Model: {{ lead.interested_car.model || 'Standard' }} &bull; ${{ lead.interested_car.price_per_day }}/day
                </div>
                <div
                  v-if="lead.pickup_date"
                  class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mt-1"
                >
                  Requested Dates: {{ lead.pickup_date }} to {{ lead.return_date || 'TBD' }}
                </div>
              </div>

              <Link
                :href="`/admin/cars/${lead.interested_car.id}`"
                class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 text-center"
              >
                View Vehicle
              </Link>
            </div>
            <div
              v-else
              class="text-sm text-slate-400 py-4 text-center"
            >
              No specific vehicle requested. Open to suggestions.
            </div>

            <!-- Notes -->
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
              <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                Requirements & Notes
              </h3>
              <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">
                {{ lead.notes || 'No special requirements noted for this lead.' }}
              </div>
            </div>
          </div>

          <!-- Associated Pipeline Deals -->
          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-base font-semibold text-slate-900 dark:text-white">
                Associated Deals
              </h2>
              <Link
                href="/admin/crm/deals"
                class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
              >
                View Sales Pipeline
              </Link>
            </div>

            <div
              v-if="!lead.deals?.length"
              class="text-center py-6 text-sm text-slate-400"
            >
              No active deals created for this lead yet.
            </div>
            <div
              v-else
              class="divide-y divide-slate-100 dark:divide-slate-800"
            >
              <div
                v-for="deal in lead.deals"
                :key="deal.id"
                class="py-3 flex items-center justify-between text-sm"
              >
                <div>
                  <div class="font-semibold text-slate-900 dark:text-white">
                    {{ deal.title }}
                  </div>
                  <div class="text-xs text-slate-500">
                    {{ deal.deal_number }} &bull; Stage: {{ deal.stage }}
                  </div>
                </div>
                <div class="font-bold text-indigo-600 dark:text-indigo-400">
                  ${{ Number(deal.value).toFixed(2) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div
        v-if="showEditModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              Edit Lead Information
            </h3>
            <button
              class="text-slate-400 hover:text-slate-600"
              @click="showEditModal = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <form
            class="space-y-4"
            @submit.prevent="submitEdit"
          >
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">First Name</label>
                <input
                  v-model="editForm.first_name"
                  required
                  type="text"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Last Name</label>
                <input
                  v-model="editForm.last_name"
                  type="text"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Email</label>
                <input
                  v-model="editForm.email"
                  type="email"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Phone</label>
                <input
                  v-model="editForm.phone"
                  type="tel"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold mb-1">Status</label>
                <select
                  v-model="editForm.status"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="new">
                    New
                  </option>
                  <option value="contacted">
                    Contacted
                  </option>
                  <option value="qualified">
                    Qualified
                  </option>
                  <option value="proposal_sent">
                    Proposal Sent
                  </option>
                  <option value="converted">
                    Converted
                  </option>
                  <option value="lost">
                    Lost
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Priority</label>
                <select
                  v-model="editForm.priority"
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
              <div>
                <label class="block text-xs font-semibold mb-1">Est. Value ($)</label>
                <input
                  v-model="editForm.estimated_value"
                  type="number"
                  step="0.01"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Notes</label>
              <textarea
                v-model="editForm.notes"
                rows="3"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              />
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
              <button
                type="button"
                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
                @click="showEditModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="editForm.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium"
              >
                {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Convert Modal -->
      <div
        v-if="showConvertModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
              <i class="ri-user-follow-line" />
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900 dark:text-white">
                Convert to Customer
              </h3>
              <p class="text-xs text-slate-500">
                Create client profile from lead details
              </p>
            </div>
          </div>

          <div class="space-y-3">
            <label class="flex items-center gap-2 text-sm font-medium">
              <input
                v-model="convertForm.create_deal"
                type="checkbox"
                class="w-4 h-4 rounded text-indigo-600"
              >
              <span>Create active deal in sales pipeline</span>
            </label>
            <div v-if="convertForm.create_deal">
              <label class="block text-xs font-semibold mb-1">Deal Title</label>
              <input
                v-model="convertForm.deal_title"
                type="text"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>
          </div>

          <div class="flex justify-end gap-2.5 pt-3">
            <button
              type="button"
              class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
              @click="showConvertModal = false"
            >
              Cancel
            </button>
            <button
              type="button"
              :disabled="convertForm.processing"
              class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm"
              @click="submitConvert"
            >
              Confirm Conversion
            </button>
          </div>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
