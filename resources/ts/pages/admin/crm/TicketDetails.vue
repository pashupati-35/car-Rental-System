<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

const props = defineProps<{
  ticket: any
}>()

const replyForm = useForm({
  message: '',
})

const submitReply = () => {
  if (!replyForm.message.trim()) return
  replyForm.post(`/admin/crm/tickets/${props.ticket.id}/reply`, {
    onSuccess: () => {
      replyForm.reset()
    },
  })
}

const updateStatus = (newStatus: string) => {
  router.patch(`/admin/crm/tickets/${props.ticket.id}/status`, {
    status: newStatus,
  }, {
    preserveScroll: true,
  })
}
</script>

<template>
  <CrmLayout>
    <Head :title="`Ticket ${ticket.ticket_number}: ${ticket.subject}`" />

    <div class="space-y-6 pb-12">
      <!-- Breadcrumbs -->
      <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <Link
          href="/crm/tickets"
          class="hover:text-indigo-600 transition flex items-center gap-1"
        >
          <i class="ri-arrow-left-line" /> Back to Support Desk
        </Link>
        <span>/</span>
        <span>Ticket {{ ticket.ticket_number }}</span>
      </div>

      <!-- Header Card -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2.5 flex-wrap">
            <span class="font-mono text-xs font-bold text-slate-400">{{ ticket.ticket_number }}</span>
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize"
              :class="{
                'bg-slate-100 text-slate-700 dark:bg-slate-800': ticket.priority === 'low',
                'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300': ticket.priority === 'medium',
                'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300': ticket.priority === 'high',
                'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300': ticket.priority === 'urgent',
              }"
            >
              {{ ticket.priority }} Priority
            </span>
            <span class="text-xs px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 capitalize font-medium text-slate-600 dark:text-slate-300">
              {{ ticket.category.replace('_', ' ') }}
            </span>
          </div>

          <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1">
            {{ ticket.subject }}
          </h1>
          <p class="text-xs text-slate-400 mt-0.5">
            Opened on {{ new Date(ticket.created_at).toLocaleString() }}
          </p>
        </div>

        <div class="flex items-center gap-3">
          <span class="text-xs font-semibold text-slate-500">Case Status:</span>
          <select
            :value="ticket.status"
            class="px-3 py-2 rounded-xl text-xs font-bold border-0 capitalize shadow-xs"
            :class="{
              'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300': ticket.status === 'open',
              'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': ticket.status === 'in_progress',
              'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300': ticket.status === 'waiting_customer',
              'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300': ticket.status === 'resolved',
              'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300': ticket.status === 'closed',
            }"
            @change="updateStatus(($event.target as HTMLSelectElement).value)"
          >
            <option value="open">
              Open
            </option>
            <option value="in_progress">
              In Progress
            </option>
            <option value="waiting_customer">
              Waiting on Customer
            </option>
            <option value="resolved">
              Resolved
            </option>
            <option value="closed">
              Closed
            </option>
          </select>
        </div>
      </div>

      <!-- Main Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Context Card -->
        <div class="space-y-6">
          <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
              Customer Information
            </h2>

            <div class="space-y-3 text-sm">
              <div v-if="ticket.customer">
                <span class="text-xs text-slate-400 block font-medium">Customer Name</span>
                <Link
                  :href="`/admin/crm/customers/${ticket.customer.id}/timeline`"
                  class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline"
                >
                  {{ ticket.customer.name }} &rarr;
                </Link>
              </div>

              <div v-if="ticket.customer?.email">
                <span class="text-xs text-slate-400 block font-medium">Email</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ ticket.customer.email }}</span>
              </div>

              <div v-if="ticket.customer?.phone_number">
                <span class="text-xs text-slate-400 block font-medium">Phone</span>
                <span class="text-slate-800 dark:text-slate-200 font-medium">{{ ticket.customer.phone_number }}</span>
              </div>
            </div>
          </div>

          <!-- Vehicle in Question -->
          <div
            v-if="ticket.car"
            class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-3"
          >
            <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
              Rented Vehicle
            </h2>
            <div class="font-bold text-slate-900 dark:text-white text-sm">
              {{ ticket.car.brand_name }} {{ ticket.car.name }}
            </div>
            <div class="text-xs text-slate-500">
              License Plate / Unit Ref: #{{ ticket.car.id }}
            </div>
          </div>
        </div>

        <!-- Discussion Thread -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
              <i class="ri-chat-3-line text-indigo-500" /> Case Communication Thread
            </h2>

            <!-- Messages List -->
            <div class="space-y-4">
              <div
                v-for="msg in ticket.messages"
                :key="msg.id"
                class="p-4 rounded-2xl text-sm"
                :class="msg.sender_type === 'admin'
                  ? 'bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/40 ml-6'
                  : 'bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-800 mr-6'"
              >
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center gap-2">
                    <span
                      class="font-bold text-xs"
                      :class="msg.sender_type === 'admin' ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-800 dark:text-slate-200'"
                    >
                      {{ msg.sender_name || (msg.sender_type === 'admin' ? 'Staff Agent' : 'Customer') }}
                    </span>
                    <span
                      class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                      :class="msg.sender_type === 'admin' ? 'bg-indigo-200 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                    >
                      {{ msg.sender_type }}
                    </span>
                  </div>
                  <span class="text-[11px] text-slate-400">
                    {{ new Date(msg.created_at).toLocaleString() }}
                  </span>
                </div>
                <p class="text-slate-700 dark:text-slate-300 text-xs leading-relaxed whitespace-pre-wrap">
                  {{ msg.message }}
                </p>
              </div>
            </div>

            <!-- Reply Box -->
            <form
              class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3"
              @submit.prevent="submitReply"
            >
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Post Agent Response</label>
              <textarea
                v-model="replyForm.message"
                required
                rows="3"
                placeholder="Type resolution notes or response to customer..."
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
              <div class="flex justify-end">
                <button
                  type="submit"
                  :disabled="replyForm.processing"
                  class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition shadow-sm flex items-center gap-1.5"
                >
                  <i class="ri-send-plane-fill" />
                  <span>{{ replyForm.processing ? 'Posting...' : 'Post Reply' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </CrmLayout>
</template>
