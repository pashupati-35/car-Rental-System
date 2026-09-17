<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  customer: any
  interactions: any[]
  preference: any
  tasks: any[]
  recent_bookings: any[]
}>()

const showInteractionModal = ref(false)
const showTaskModal = ref(false)
const showPrefModal = ref(false)

const interactionForm = useForm({
  type: 'call',
  subject: '',
  details: '',
  interaction_date: new Date().toISOString().slice(0, 16),
})

const taskForm = useForm({
  title: '',
  description: '',
  due_date: '',
  priority: 'medium',
  to_customer: false,
})

const prefForm = useForm({
  preferred_car_type: props.preference?.preferred_car_type || 'SUV',
  preferred_transmission: props.preference?.preferred_transmission || 'Automatic',
  preferred_fuel_type: props.preference?.preferred_fuel_type || 'Petrol',
  needs_child_seat: props.preference?.needs_child_seat ?? false,
  needs_chauffeur: props.preference?.needs_chauffeur ?? false,
  vip_status: props.preference?.vip_status ?? false,
  loyalty_tier: props.preference?.loyalty_tier || 'Standard',
  special_requests: props.preference?.special_requests || '',
})

const submitInteraction = () => {
  interactionForm.post(`/admin/crm/customers/${props.customer.id}/interactions`, {
    onSuccess: () => {
      showInteractionModal.value = false
      interactionForm.reset()
    },
  })
}

const submitTask = () => {
  taskForm.post(`/admin/crm/customers/${props.customer.id}/tasks`, {
    onSuccess: () => {
      showTaskModal.value = false
      taskForm.reset()
    },
  })
}

const submitPreferences = () => {
  prefForm.post(`/admin/crm/customers/${props.customer.id}/preferences`, {
    onSuccess: () => {
      showPrefModal.value = false
    },
  })
}

const completeTask = (taskId: number) => {
  router.patch(`/admin/crm/tasks/${taskId}/complete`, {}, {
    preserveScroll: true,
  })
}
</script>

<template>
  <CrmLayout>
    <Head :title="`Customer 360: ${customer.name}`" />

    <div class="space-y-6 pb-12">
      <!-- Breadcrumbs -->
      <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <Link
          href="/crm/customers"
          class="hover:text-indigo-600 transition flex items-center gap-1"
        >
          <i class="ri-arrow-left-line" /> Back to Customers
        </Link>
        <span>/</span>
        <span>Customer 360 Profile</span>
      </div>

      <!-- Customer 360 Header Card -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center text-xl shrink-0">
            {{ customer.name?.[0] || 'C' }}
          </div>
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">
                {{ customer.name }}
              </h1>
              <span
                v-if="preference?.vip_status"
                class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 flex items-center gap-1"
              >
                <i class="ri-vip-crown-fill" /> VIP Client
              </span>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                {{ preference?.loyalty_tier || 'Standard' }} Tier
              </span>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 mt-1">
              <span v-if="customer.email"><i class="ri-mail-line text-indigo-500" /> {{ customer.email }}</span>
              <span v-if="customer.phone_number"><i class="ri-phone-line text-indigo-500" /> {{ customer.phone_number }}</span>
              <span v-if="customer.address"><i class="ri-map-pin-line text-indigo-500" /> {{ customer.address }}</span>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <button
            type="button"
            class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition flex items-center gap-1.5 shadow-sm"
            @click="showInteractionModal = true"
          >
            <i class="ri-chat-new-line" />
            <span>Log Interaction</span>
          </button>

          <button
            type="button"
            class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 transition flex items-center gap-1.5"
            @click="showTaskModal = true"
          >
            <i class="ri-calendar-check-line" />
            <span>Add Task</span>
          </button>

          <button
            type="button"
            class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 transition flex items-center gap-1.5"
            @click="showPrefModal = true"
          >
            <i class="ri-settings-3-line" />
            <span>Preferences</span>
          </button>
        </div>
      </div>

      <!-- Main Columns -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Preferences & CRM Tasks -->
        <div class="space-y-6">
          <!-- Preferences Summary Card -->
          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
              <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-heart-3-line text-rose-500" /> Rental Preferences
              </h2>
              <button
                class="text-xs font-semibold text-indigo-600 hover:underline"
                @click="showPrefModal = true"
              >
                Edit
              </button>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
              <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800">
                <span class="text-slate-400 block mb-0.5">Car Category</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ preference?.preferred_car_type || 'SUV' }}</span>
              </div>
              <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800">
                <span class="text-slate-400 block mb-0.5">Transmission</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ preference?.preferred_transmission || 'Automatic' }}</span>
              </div>
              <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800">
                <span class="text-slate-400 block mb-0.5">Fuel Type</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ preference?.preferred_fuel_type || 'Petrol' }}</span>
              </div>
              <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800">
                <span class="text-slate-400 block mb-0.5">Chauffeur</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ preference?.needs_chauffeur ? 'Required' : 'Self Drive' }}</span>
              </div>
            </div>

            <div
              v-if="preference?.special_requests"
              class="pt-2 text-xs"
            >
              <span class="text-slate-400 block mb-1 font-semibold">Special Requests</span>
              <p class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                {{ preference.special_requests }}
              </p>
            </div>
          </div>

          <!-- Follow-up Tasks Card -->
          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
              <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="ri-checkbox-circle-line text-emerald-500" /> Follow-Up Tasks
              </h2>
              <button
                class="text-xs font-semibold text-indigo-600 hover:underline"
                @click="showTaskModal = true"
              >
                + Add Task
              </button>
            </div>

            <div
              v-if="!tasks?.length"
              class="text-center py-6 text-xs text-slate-400"
            >
              No follow-up tasks scheduled.
            </div>
            <div
              v-else
              class="space-y-2"
            >
              <div
                v-for="task in tasks"
                :key="task.id"
                class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start justify-between gap-2 text-xs"
              >
                <div class="space-y-1">
                  <div
                    class="font-bold text-slate-900 dark:text-white"
                    :class="{ 'line-through text-slate-400': task.status === 'completed' }"
                  >
                    {{ task.title }}
                  </div>
                  <div
                    v-if="task.description"
                    class="text-slate-500"
                  >
                    {{ task.description }}
                  </div>
                  <div class="flex items-center gap-2 flex-wrap mt-1">
                    <span
                      v-if="task.notify_recipient"
                      class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 flex items-center gap-1"
                    >
                      <i class="ri-mail-send-line" /> To Customer
                    </span>
                    <span
                      v-else
                      class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700 flex items-center gap-1"
                    >
                      <i class="ri-shield-user-line" /> Admin Task
                    </span>
                    <span
                      v-if="task.due_date"
                      class="text-[11px] text-amber-600 dark:text-amber-400 font-medium"
                    >
                      Due: {{ new Date(task.due_date).toLocaleDateString() }}
                    </span>
                  </div>
                </div>

                <button
                  v-if="task.status !== 'completed'"
                  type="button"
                  class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition"
                  title="Mark Completed"
                  @click="completeTask(task.id)"
                >
                  <i class="ri-checkbox-blank-circle-line text-base" />
                </button>
                <span
                  v-else
                  class="text-emerald-500"
                >
                  <i class="ri-checkbox-circle-fill text-base" />
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Touchpoint Interactions Timeline & Recent Rentals -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Interactions Timeline Card -->
          <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
              <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                  <i class="ri-history-line text-indigo-500" /> Touchpoints & Activity Timeline
                </h2>
                <p class="text-xs text-slate-500">
                  Chronological history of calls, emails, notes and meetings
                </p>
              </div>

              <button
                type="button"
                class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition"
                @click="showInteractionModal = true"
              >
                + Log Touchpoint
              </button>
            </div>

            <div
              v-if="!interactions?.length"
              class="text-center py-10 text-slate-400 text-sm"
            >
              No interactions logged yet. Click "Log Interaction" to record calls, emails, or notes.
            </div>

            <div
              v-else
              class="relative border-l-2 border-slate-100 dark:border-slate-800 ms-4 space-y-6 py-2"
            >
              <div
                v-for="interaction in interactions"
                :key="interaction.id"
                class="relative ps-6"
              >
                <!-- Dot Icon -->
                <div class="absolute -left-3.5 top-0.5 w-7 h-7 rounded-full bg-white dark:bg-slate-900 border-2 border-indigo-500 flex items-center justify-center text-indigo-600 text-xs shadow-xs">
                  <i
                    :class="{
                      'ri-phone-line': interaction.type === 'call',
                      'ri-mail-line': interaction.type === 'email',
                      'ri-team-line': interaction.type === 'meeting',
                      'ri-file-text-line': interaction.type === 'note',
                      'ri-whatsapp-line': interaction.type === 'whatsapp',
                      'ri-message-3-line': interaction.type === 'sms',
                    }"
                  />
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-800 text-sm space-y-1.5">
                  <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-900 dark:text-white capitalize">
                      [{{ interaction.type }}] {{ interaction.subject }}
                    </span>
                    <span class="text-xs text-slate-400">
                      {{ new Date(interaction.interaction_date).toLocaleString() }}
                    </span>
                  </div>
                  <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed whitespace-pre-wrap">
                    {{ interaction.details }}
                  </p>
                  <div
                    v-if="interaction.admin"
                    class="text-[11px] text-slate-400 pt-1"
                  >
                    Logged by <span class="font-medium text-slate-700 dark:text-slate-300">{{ interaction.admin.name }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Rental Bookings -->
          <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
              <i class="ri-car-line text-emerald-500" /> Recent Rental Bookings
            </h2>

            <div
              v-if="!recent_bookings?.length"
              class="text-center py-6 text-sm text-slate-400"
            >
              No booking records found for this customer.
            </div>

            <div
              v-else
              class="divide-y divide-slate-100 dark:divide-slate-800"
            >
              <div
                v-for="b in recent_bookings"
                :key="b.id"
                class="py-3 flex items-center justify-between text-sm"
              >
                <div>
                  <div class="font-semibold text-slate-900 dark:text-white">
                    {{ b.car ? `${b.car.brand_name} ${b.car.name}` : 'Rental Vehicle' }}
                  </div>
                  <div class="text-xs text-slate-500">
                    {{ b.start_date || b.pickup_date }} to {{ b.end_date || b.return_date }}
                  </div>
                </div>
                <div class="text-right">
                  <span class="text-xs font-semibold px-2 py-0.5 rounded-full capitalize bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ b.status || 'Confirmed' }}
                  </span>
                  <div class="text-xs font-bold text-emerald-600 mt-1">
                    ${{ Number(b.total_price || 0).toFixed(2) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Log Interaction Modal -->
      <div
        v-if="showInteractionModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              Log Customer Interaction
            </h3>
            <button
              class="text-slate-400 hover:text-slate-600"
              @click="showInteractionModal = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <form
            class="space-y-4"
            @submit.prevent="submitInteraction"
          >
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold mb-1">Interaction Type</label>
                <select
                  v-model="interactionForm.type"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="call">
                    Phone Call
                  </option>
                  <option value="email">
                    Email Sent/Received
                  </option>
                  <option value="meeting">
                    In-Person Meeting
                  </option>
                  <option value="note">
                    Internal Staff Note
                  </option>
                  <option value="whatsapp">
                    WhatsApp Chat
                  </option>
                  <option value="sms">
                    SMS Message
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Date & Time</label>
                <input
                  v-model="interactionForm.interaction_date"
                  type="datetime-local"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Subject / Summary *</label>
              <input
                v-model="interactionForm.subject"
                required
                type="text"
                placeholder="e.g. Call regarding SUV availability next weekend"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Detailed Discussion Notes *</label>
              <textarea
                v-model="interactionForm.details"
                required
                rows="4"
                placeholder="Client requested automatic transmission, asked for weekend discount rate..."
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              />
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
              <button
                type="button"
                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
                @click="showInteractionModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="interactionForm.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm"
              >
                {{ interactionForm.processing ? 'Saving...' : 'Record Touchpoint' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Add Task Modal -->
      <div
        v-if="showTaskModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              Schedule Follow-up Task
            </h3>
            <button
              class="text-slate-400 hover:text-slate-600"
              @click="showTaskModal = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <form
            class="space-y-4"
            @submit.prevent="submitTask"
          >
            <div>
              <label class="block text-xs font-semibold mb-1">Task Title *</label>
              <input
                v-model="taskForm.title"
                required
                type="text"
                placeholder="e.g. Call customer to verify flight arrival time"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold mb-1">Due Date</label>
                <input
                  v-model="taskForm.due_date"
                  type="date"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Priority</label>
                <select
                  v-model="taskForm.priority"
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
              <label class="block text-xs font-semibold mb-1">Instructions / Description</label>
              <textarea
                v-model="taskForm.description"
                rows="3"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              />
            </div>

            <!-- Toggle: To Customer -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
              <div class="space-y-0.5">
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                  <i class="ri-mail-send-line text-indigo-500" />
                  <span>To Customer</span>
                  <span
                    class="ms-1 px-1.5 py-0.2 rounded text-[10px] font-semibold"
                    :class="taskForm.to_customer ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300'"
                  >
                    {{ taskForm.to_customer ? 'Email Enabled' : 'Internal Admin' }}
                  </span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                  {{ taskForm.to_customer ? 'An email notification with task instructions will be dispatched to this customer.' : 'Internal admin task only. No email will be sent.' }}
                </p>
              </div>

              <button
                type="button"
                role="switch"
                :aria-checked="taskForm.to_customer"
                :class="taskForm.to_customer ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-600'"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                @click="taskForm.to_customer = !taskForm.to_customer"
              >
                <span
                  :class="taskForm.to_customer ? 'translate-x-5' : 'translate-x-0'"
                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                />
              </button>
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
              <button
                type="button"
                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
                @click="showTaskModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="taskForm.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm"
              >
                Schedule Task
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Edit Preferences Modal -->
      <div
        v-if="showPrefModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              Customer Rental Preferences
            </h3>
            <button
              class="text-slate-400 hover:text-slate-600"
              @click="showPrefModal = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <form
            class="space-y-4"
            @submit.prevent="submitPreferences"
          >
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold mb-1">Preferred Car Type</label>
                <select
                  v-model="prefForm.preferred_car_type"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="SUV">
                    SUV
                  </option>
                  <option value="Sedan">
                    Sedan
                  </option>
                  <option value="Luxury">
                    Luxury
                  </option>
                  <option value="Hatchback">
                    Hatchback
                  </option>
                  <option value="Van">
                    Van / Minibus
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Transmission</label>
                <select
                  v-model="prefForm.preferred_transmission"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="Automatic">
                    Automatic
                  </option>
                  <option value="Manual">
                    Manual
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold mb-1">Fuel Preference</label>
                <select
                  v-model="prefForm.preferred_fuel_type"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="Petrol">
                    Petrol
                  </option>
                  <option value="Diesel">
                    Diesel
                  </option>
                  <option value="Electric">
                    Electric
                  </option>
                  <option value="Hybrid">
                    Hybrid
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-semibold mb-1">Loyalty Tier</label>
                <select
                  v-model="prefForm.loyalty_tier"
                  class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
                >
                  <option value="Standard">
                    Standard
                  </option>
                  <option value="Silver">
                    Silver
                  </option>
                  <option value="Gold">
                    Gold
                  </option>
                  <option value="Platinum">
                    Platinum
                  </option>
                </select>
              </div>
            </div>

            <div class="flex items-center gap-6 py-2">
              <label class="flex items-center gap-2 cursor-pointer text-xs font-medium">
                <input
                  v-model="prefForm.needs_child_seat"
                  type="checkbox"
                  class="rounded text-indigo-600"
                >
                <span>Child Seat Needed</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-xs font-medium">
                <input
                  v-model="prefForm.needs_chauffeur"
                  type="checkbox"
                  class="rounded text-indigo-600"
                >
                <span>Requires Driver</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-xs font-medium">
                <input
                  v-model="prefForm.vip_status"
                  type="checkbox"
                  class="rounded text-indigo-600"
                >
                <span>VIP Renter</span>
              </label>
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Special Requests</label>
              <textarea
                v-model="prefForm.special_requests"
                rows="2"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              />
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
              <button
                type="button"
                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm"
                @click="showPrefModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="prefForm.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm"
              >
                Save Preferences
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
