<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  owner: any
  interactions: any[]
  preference: any
  tasks: any[]
  cars: any[]
  drivers: any[]
  recent_bookings: any[]
  support_tickets: any[]
}>()

const showInteractionModal = ref(false)
const showTaskModal = ref(false)
const showPreferenceModal = ref(false)

const activeTab = ref<'fleet' | 'interactions' | 'bookings' | 'drivers' | 'tickets' | 'tasks'>('fleet')

// Log Interaction Form
const interactionForm = useForm({
  type: 'call',
  subject: '',
  details: '',
  interaction_date: new Date().toISOString().slice(0, 16),
})

const submitInteraction = () => {
  interactionForm.post(`/crm/owners/${props.owner.id}/interactions`, {
    preserveScroll: true,
    onSuccess: () => {
      showInteractionModal.value = false
      interactionForm.reset('subject', 'details')
    },
  })
}

// Add Task Form
const taskForm = useForm({
  title: '',
  description: '',
  due_date: '',
  priority: 'medium',
  to_owner: false,
})

const submitTask = () => {
  taskForm.post(`/crm/owners/${props.owner.id}/tasks`, {
    preserveScroll: true,
    onSuccess: () => {
      showTaskModal.value = false
      taskForm.reset()
    },
  })
}

// Update Partner Preference Form
const prefForm = useForm({
  partner_tier: props.preference?.partner_tier || 'Standard',
  payout_frequency: props.preference?.payout_frequency || 'Monthly',
  commission_rate: props.preference?.commission_rate || 15.00,
  payout_method: props.preference?.payout_method || 'Bank Transfer',
  bank_name: props.preference?.bank_name || '',
  account_number: props.preference?.account_number || '',
  routing_number: props.preference?.routing_number || '',
  vip_partner: Boolean(props.preference?.vip_partner),
  notes: props.preference?.notes || '',
})

const submitPreferences = () => {
  prefForm.post(`/crm/owners/${props.owner.id}/preferences`, {
    preserveScroll: true,
    onSuccess: () => {
      showPreferenceModal.value = false
    },
  })
}

const completeTask = (taskId: number) => {
  router.patch(`/crm/tasks/${taskId}/complete`, {}, {
    preserveScroll: true,
  })
}

const interactionIcons: Record<string, string> = {
  call: 'ri-phone-line text-blue-500 bg-blue-50 dark:bg-blue-950/50',
  email: 'ri-mail-line text-indigo-500 bg-indigo-50 dark:bg-indigo-950/50',
  meeting: 'ri-team-line text-purple-500 bg-purple-50 dark:bg-purple-950/50',
  whatsapp: 'ri-whatsapp-line text-emerald-500 bg-emerald-50 dark:bg-emerald-950/50',
  note: 'ri-sticky-note-line text-amber-500 bg-amber-50 dark:bg-amber-950/50',
  sms: 'ri-message-2-line text-sky-500 bg-sky-50 dark:bg-sky-950/50',
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
    <Head :title="`Fleet Owner 360: ${owner.full_name || owner.first_name}`" />

    <div class="space-y-6 pb-12">
      <!-- Breadcrumbs -->
      <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <Link
          href="/crm/owners"
          class="hover:text-emerald-600 transition flex items-center gap-1"
        >
          <i class="ri-arrow-left-line" /> Back to Fleet Owners
        </Link>
        <span>/</span>
        <span>Fleet Owner 360 Dossier</span>
      </div>

      <!-- Owner 360 Header Card -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-700 text-white font-bold flex items-center justify-center text-2xl shadow-sm shrink-0">
            {{ (owner.full_name || owner.first_name || 'O').charAt(0).toUpperCase() }}
          </div>
          <div>
            <div class="flex items-center gap-3">
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                {{ owner.full_name || `${owner.first_name || ''} ${owner.last_name || ''}`.trim() }}
              </h1>
              <span
                class="px-2.5 py-1 rounded-md text-xs"
                :class="partnerBadges[preference?.partner_tier || 'Standard']?.class"
              >
                {{ partnerBadges[preference?.partner_tier || 'Standard']?.label || 'Standard' }}
              </span>
              <span
                v-if="preference?.vip_partner"
                class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300"
              >
                VIP Partner
              </span>
            </div>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-1">
              <span class="flex items-center gap-1"><i class="ri-mail-line" /> {{ owner.email }}</span>
              <span
                v-if="owner.contact_number || owner.mobile"
                class="flex items-center gap-1"
              >
                <i class="ri-phone-line" /> {{ owner.contact_number || owner.mobile }}
              </span>
              <span
                v-if="owner.address"
                class="flex items-center gap-1"
              >
                <i class="ri-map-pin-line" /> {{ owner.address }}
              </span>
              <span class="text-slate-400">ID: {{ owner.unique_identifier || '#' + owner.id }}</span>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
            @click="showInteractionModal = true"
          >
            <i class="ri-chat-new-line" /> Log Interaction
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-medium text-xs transition flex items-center gap-1.5 cursor-pointer"
            @click="showTaskModal = true"
          >
            <i class="ri-task-line" /> Add Task
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium text-xs transition flex items-center gap-1.5 cursor-pointer"
            @click="showPreferenceModal = true"
          >
            <i class="ri-settings-3-line" /> Partner Terms
          </button>
        </div>
      </div>

      <!-- Partner Metrics Highlight Bar -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
          <span class="text-xs text-slate-500 font-medium block">Fleet Vehicles</span>
          <span class="text-xl font-bold text-slate-900 dark:text-white mt-1 block">{{ cars.length }}</span>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
          <span class="text-xs text-slate-500 font-medium block">System Drivers</span>
          <span class="text-xl font-bold text-slate-900 dark:text-white mt-1 block">{{ drivers.length }}</span>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
          <span class="text-xs text-slate-500 font-medium block">Commission Rate</span>
          <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 block">{{ preference?.commission_rate || 15 }}%</span>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
          <span class="text-xs text-slate-500 font-medium block">Payout Schedule</span>
          <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1 block">{{ preference?.payout_frequency || 'Monthly' }}</span>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 overflow-x-auto pb-px">
        <button
          v-for="tab in [
            { id: 'fleet', label: 'Fleet Vehicles', icon: 'ri-car-line', count: cars.length },
            { id: 'interactions', label: 'Interaction Logs', icon: 'ri-chat-history-line', count: interactions.length },
            { id: 'bookings', label: 'Fleet Bookings', icon: 'ri-calendar-check-line', count: recent_bookings.length },
            { id: 'drivers', label: 'Drivers', icon: 'ri-user-star-line', count: drivers.length },
            { id: 'tickets', label: 'Support & Incidents', icon: 'ri-customer-service-2-line', count: support_tickets.length },
            { id: 'tasks', label: 'Follow-Up Tasks', icon: 'ri-checkbox-circle-line', count: tasks.length },
          ]"
          :key="tab.id"
          type="button"
          class="px-4 py-2.5 text-xs font-semibold flex items-center gap-2 border-b-2 transition whitespace-nowrap cursor-pointer"
          :class="activeTab === tab.id ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
          @click="activeTab = tab.id as any"
        >
          <i :class="tab.icon" />
          <span>{{ tab.label }}</span>
          <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
            {{ tab.count }}
          </span>
        </button>
      </div>

      <!-- Tab Content 1: Fleet Vehicles -->
      <div
        v-if="activeTab === 'fleet'"
        class="space-y-4"
      >
        <div
          v-if="cars.length > 0"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
        >
          <div
            v-for="car in cars"
            :key="car.id"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs flex flex-col justify-between"
          >
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-mono font-bold text-slate-500">{{ car.car_registration_number || car.plate_number || 'No Plate' }}</span>
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase"
                  :class="car.status === 'verified' || car.is_verified ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                >
                  {{ car.status || (car.is_verified ? 'Verified' : 'Pending') }}
                </span>
              </div>
              <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                {{ car.car_name || car.name }} {{ car.car_model || car.model || '' }}
              </h4>
              <p class="text-xs text-slate-400 mt-0.5">
                Brand: {{ car.brand?.name || 'Standard' }}
              </p>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
              <span class="font-bold text-slate-900 dark:text-white">${{ car.car_price_per_day || car.price_per_day || 0 }}/day</span>
              <span class="text-slate-400">{{ car.transmission_type || 'Automatic' }} &bull; {{ car.fuel_type || 'Petrol' }}</span>
            </div>
          </div>
        </div>
        <div
          v-else
          class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 text-slate-500"
        >
          <i class="ri-car-line text-3xl text-slate-400 mb-2 block" />
          No vehicles registered under this fleet owner yet.
        </div>
      </div>

      <!-- Tab Content 2: Interaction Logs -->
      <div
        v-if="activeTab === 'interactions'"
        class="space-y-4"
      >
        <div
          v-if="interactions.length > 0"
          class="space-y-3"
        >
          <div
            v-for="interaction in interactions"
            :key="interaction.id"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs flex items-start gap-4"
          >
            <div
              class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
              :class="interactionIcons[interaction.type] || 'ri-message-line'"
            >
              <i :class="interactionIcons[interaction.type]?.split(' ')[0] || 'ri-message-line'" />
            </div>
            <div class="flex-1">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                  {{ interaction.subject }}
                </h4>
                <span class="text-[11px] text-slate-400">{{ new Date(interaction.interaction_date).toLocaleString() }}</span>
              </div>
              <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 whitespace-pre-wrap">
                {{ interaction.details }}
              </p>
              <div
                v-if="interaction.admin"
                class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-2 font-medium"
              >
                Logged by: {{ interaction.admin.name }}
              </div>
            </div>
          </div>
        </div>
        <div
          v-else
          class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 text-slate-500"
        >
          <i class="ri-chat-history-line text-3xl text-slate-400 mb-2 block" />
          No interactions logged for this partner yet. Click "Log Interaction" above.
        </div>
      </div>

      <!-- Tab Content 3: Fleet Bookings -->
      <div
        v-if="activeTab === 'bookings'"
        class="space-y-4"
      >
        <div
          v-if="recent_bookings.length > 0"
          class="overflow-x-auto bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs"
        >
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-semibold border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="p-3.5">
                  Booking #
                </th>
                <th class="p-3.5">
                  Vehicle
                </th>
                <th class="p-3.5">
                  Customer
                </th>
                <th class="p-3.5">
                  Dates
                </th>
                <th class="p-3.5">
                  Revenue
                </th>
                <th class="p-3.5">
                  Status
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr
                v-for="b in recent_bookings"
                :key="b.id"
                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50"
              >
                <td class="p-3.5 font-mono font-bold">
                  {{ b.booking_number || '#' + b.id }}
                </td>
                <td class="p-3.5 font-semibold text-slate-900 dark:text-white">
                  {{ b.car?.car_name || b.car?.name }}
                </td>
                <td class="p-3.5">
                  {{ b.customer?.name || 'Client #' + b.customer_id }}
                </td>
                <td class="p-3.5 text-slate-500">
                  {{ b.start_date }} &rarr; {{ b.end_date }}
                </td>
                <td class="p-3.5 font-bold text-emerald-600">
                  ${{ b.total_price || b.amount || 0 }}
                </td>
                <td class="p-3.5">
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                    :class="b.status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'"
                  >
                    {{ b.status || 'Pending' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div
          v-else
          class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 text-slate-500"
        >
          No rental bookings recorded for this owner's fleet.
        </div>
      </div>

      <!-- Tab Content 4: System Drivers -->
      <div
        v-if="activeTab === 'drivers'"
        class="space-y-4"
      >
        <div
          v-if="drivers.length > 0"
          class="grid grid-cols-1 md:grid-cols-2 gap-4"
        >
          <div
            v-for="d in drivers"
            :key="d.id"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs flex items-center justify-between"
          >
            <div>
              <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                {{ d.name || `${d.first_name || ''} ${d.last_name || ''}`.trim() }}
              </h4>
              <p class="text-xs text-slate-400 mt-0.5">
                {{ d.email }} &bull; {{ d.phone || d.mobile }}
              </p>
            </div>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
              License: {{ d.license_number || 'Verified' }}
            </span>
          </div>
        </div>
        <div
          v-else
          class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 text-slate-500"
        >
          No drivers registered under this owner.
        </div>
      </div>

      <!-- Tab Content 5: Support Tickets -->
      <div
        v-if="activeTab === 'tickets'"
        class="space-y-4"
      >
        <div
          v-if="support_tickets.length > 0"
          class="space-y-3"
        >
          <div
            v-for="t in support_tickets"
            :key="t.id"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs flex items-center justify-between"
          >
            <div>
              <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-bold text-indigo-600">{{ t.ticket_number }}</span>
                <span
                  class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                  :class="t.status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                >
                  {{ t.status }}
                </span>
              </div>
              <h4 class="font-bold text-slate-900 dark:text-white text-sm mt-1">
                {{ t.subject }}
              </h4>
            </div>
            <Link
              :href="`/crm/tickets/${t.id}`"
              class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950 hover:bg-indigo-100"
            >
              View Ticket &rarr;
            </Link>
          </div>
        </div>
        <div
          v-else
          class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 text-slate-500"
        >
          No incidents or support tickets for this owner.
        </div>
      </div>

      <!-- Tab Content 6: Follow-Up Tasks -->
      <div
        v-if="activeTab === 'tasks'"
        class="space-y-4"
      >
        <div
          v-if="tasks.length > 0"
          class="space-y-3"
        >
          <div
            v-for="task in tasks"
            :key="task.id"
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs flex items-center justify-between"
          >
            <div>
              <h4
                class="font-bold text-slate-900 dark:text-white text-sm"
                :class="{ 'line-through text-slate-400': task.status === 'completed' }"
              >
                {{ task.title }}
              </h4>
              <p
                v-if="task.description"
                class="text-xs text-slate-500 mt-0.5"
              >
                {{ task.description }}
              </p>
              <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1 flex-wrap">
                <span
                  v-if="task.notify_recipient"
                  class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 flex items-center gap-1"
                >
                  <i class="ri-mail-send-line" /> To Owner
                </span>
                <span
                  v-else
                  class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex items-center gap-1"
                >
                  <i class="ri-shield-user-line" /> Admin Task
                </span>
                <span v-if="task.due_date">Due: {{ new Date(task.due_date).toLocaleDateString() }}</span>
                <span class="uppercase font-semibold text-amber-600">{{ task.priority }} priority</span>
              </div>
            </div>

            <button
              v-if="task.status !== 'completed'"
              type="button"
              class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 text-xs font-semibold transition cursor-pointer flex items-center gap-1"
              @click="completeTask(task.id)"
            >
              <i class="ri-check-line" /> Mark Done
            </button>
            <span
              v-else
              class="text-xs font-bold text-emerald-600 flex items-center gap-1"
            >
              <i class="ri-checkbox-circle-fill" /> Completed
            </span>
          </div>
        </div>
        <div
          v-else
          class="text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 text-slate-500"
        >
          No tasks scheduled for this owner.
        </div>
      </div>
    </div>

    <!-- Modal: Log Interaction -->
    <div
      v-if="showInteractionModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
    >
      <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-slate-900 dark:text-white text-base">
            Log Partner Interaction
          </h3>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer"
            @click="showInteractionModal = false"
          >
            <i class="ri-close-line text-lg" />
          </button>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="submitInteraction"
        >
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Type</label>
              <select
                v-model="interactionForm.type"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
                <option value="call">
                  Phone Call
                </option>
                <option value="meeting">
                  Fleet Meeting
                </option>
                <option value="email">
                  Email
                </option>
                <option value="whatsapp">
                  WhatsApp
                </option>
                <option value="note">
                  Internal Note
                </option>
                <option value="sms">
                  SMS
                </option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Date & Time</label>
              <input
                v-model="interactionForm.interaction_date"
                type="datetime-local"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Subject</label>
            <input
              v-model="interactionForm.subject"
              type="text"
              required
              placeholder="e.g. Monthly Payout Audit, New Vehicle Inspection"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            >
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Notes / Details</label>
            <textarea
              v-model="interactionForm.details"
              rows="4"
              required
              placeholder="Provide details of the conversation..."
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            />
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs"
              @click="showInteractionModal = false"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="interactionForm.processing"
              class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs"
            >
              Save Log
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal: Add Task -->
    <div
      v-if="showTaskModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
    >
      <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-slate-900 dark:text-white text-base">
            Schedule Partner Task
          </h3>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer"
            @click="showTaskModal = false"
          >
            <i class="ri-close-line text-lg" />
          </button>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="submitTask"
        >
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Task Title</label>
            <input
              v-model="taskForm.title"
              type="text"
              required
              placeholder="e.g. Verify vehicle insurance renewal"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            >
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Due Date</label>
              <input
                v-model="taskForm.due_date"
                type="datetime-local"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Priority</label>
              <select
                v-model="taskForm.priority"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
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
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Description</label>
            <textarea
              v-model="taskForm.description"
              rows="3"
              placeholder="Additional task instructions..."
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            />
          </div>

          <!-- Toggle: To Owner -->
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
            <div class="space-y-0.5">
              <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                <i class="ri-mail-send-line text-indigo-500" />
                <span>To Owner</span>
                <span
                  class="ms-1 px-1.5 py-0.2 rounded text-[10px] font-semibold"
                  :class="taskForm.to_owner ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300'"
                >
                  {{ taskForm.to_owner ? 'Email Enabled' : 'Internal Admin' }}
                </span>
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                {{ taskForm.to_owner ? 'An email notification with task instructions will be dispatched to this fleet owner.' : 'Internal admin task only. No email will be sent.' }}
              </p>
            </div>

            <button
              type="button"
              role="switch"
              :aria-checked="taskForm.to_owner"
              :class="taskForm.to_owner ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-600'"
              class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
              @click="taskForm.to_owner = !taskForm.to_owner"
            >
              <span
                :class="taskForm.to_owner ? 'translate-x-5' : 'translate-x-0'"
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
              />
            </button>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs"
              @click="showTaskModal = false"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="taskForm.processing"
              class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs"
            >
              Schedule Task
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal: Partner Terms & Preferences -->
    <div
      v-if="showPreferenceModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
    >
      <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-slate-900 dark:text-white text-base">
            Partner Terms & Payout Settings
          </h3>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer"
            @click="showPreferenceModal = false"
          >
            <i class="ri-close-line text-lg" />
          </button>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="submitPreferences"
        >
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Partner Tier</label>
              <select
                v-model="prefForm.partner_tier"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
                <option value="Standard">
                  Standard
                </option>
                <option value="Silver Partner">
                  Silver Partner
                </option>
                <option value="Gold Partner">
                  Gold Partner
                </option>
                <option value="Platinum Partner">
                  Platinum Partner
                </option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Commission Rate (%)</label>
              <input
                v-model="prefForm.commission_rate"
                type="number"
                step="0.5"
                min="0"
                max="100"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Payout Frequency</label>
              <select
                v-model="prefForm.payout_frequency"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
                <option value="Weekly">
                  Weekly
                </option>
                <option value="Bi-weekly">
                  Bi-weekly
                </option>
                <option value="Monthly">
                  Monthly
                </option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Payout Method</label>
              <input
                v-model="prefForm.payout_method"
                type="text"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
                placeholder="Bank Transfer, PayPal, etc."
              >
            </div>
          </div>

          <div class="grid grid-cols-3 gap-2">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Bank Name</label>
              <input
                v-model="prefForm.bank_name"
                type="text"
                placeholder="Chase, Wells, etc."
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Account #</label>
              <input
                v-model="prefForm.account_number"
                type="text"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Routing #</label>
              <input
                v-model="prefForm.routing_number"
                type="text"
                class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
              >
            </div>
          </div>

          <div class="flex items-center gap-2 pt-1">
            <input
              id="vip_partner_check"
              v-model="prefForm.vip_partner"
              type="checkbox"
              class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
            >
            <label
              for="vip_partner_check"
              class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer"
            >
              Mark as Priority VIP Partner
            </label>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Special Contract Terms & Notes</label>
            <textarea
              v-model="prefForm.notes"
              rows="3"
              placeholder="Agreed vehicle inspection schedules, bonuses..."
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            />
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs"
              @click="showPreferenceModal = false"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="prefForm.processing"
              class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs"
            >
              Save Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  </CrmLayout>
</template>
