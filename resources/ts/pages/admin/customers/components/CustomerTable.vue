<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Pagination from '@/components/Pagination.vue'
import { resolveMediaUrl } from '@/utils/helpers'
import type { CustomerItem } from '../types'

defineProps<{
  customers: CustomerItem[]
  pagination?: any
}>()

const emit = defineEmits<{
  (e: 'view', customer: CustomerItem): void
  (e: 'edit', customer: CustomerItem): void
  (e: 'delete', id: number): void
}>()

const getCustomerImage = (c: CustomerItem) => {
  return resolveMediaUrl(c.image, c.image_path, 'customer')
}
</script>

<template>
  <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
    <!-- Empty State -->
    <div
      v-if="customers.length === 0"
      class="py-16 text-center px-4"
    >
      <i class="ri-user-heart-line text-4xl text-slate-300 dark:text-slate-700 mb-2 inline-block" />
      <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
        No Customers found
      </p>
      <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
        Click "Add New Customer" to register a customer profile.
      </p>
    </div>

    <template v-else>
      <!-- Mobile Cards (xs, sm) -->
      <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
        <div
          v-for="customer in customers"
          :key="customer.id"
          class="p-4 space-y-3 cursor-pointer hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
          @click="emit('view', customer)"
        >
          <div class="flex items-start justify-between gap-2">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden border border-slate-200 dark:border-slate-800">
                <img
                  v-if="getCustomerImage(customer)"
                  :src="getCustomerImage(customer)"
                  :alt="customer.name || customer.full_name"
                  class="w-full h-full object-cover"
                  @error="(e) => (e.target as HTMLElement).style.display = 'none'"
                >
                <span>{{ (customer.name || customer.full_name || 'C').charAt(0).toUpperCase() }}</span>
              </div>
              <div>
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">
                  {{ customer.name || customer.full_name }}
                </h4>
                <p class="text-[11px] text-slate-400 font-mono">
                  {{ customer.email }}
                </p>
              </div>
            </div>

            <span
              class="px-2 py-0.5 rounded-full text-[10px] font-bold"
              :class="customer.is_active !== false ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-700'"
            >
              {{ customer.is_active !== false ? 'Active' : 'Inactive' }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs py-2 px-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl">
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Contact</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200">{{ customer.phone_number || customer.phone || 'N/A' }}</span>
            </div>
            <div>
              <span class="text-[10px] text-slate-400 uppercase font-bold block">Rental History</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200">
                {{ customer.bookings_count ?? 0 }} Bookings
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs" @click.stop>
            <button
              type="button"
              class="font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 hover:underline cursor-pointer"
              @click="emit('view', customer)"
            >
              <i class="ri-eye-line" />
              <span>View Profile</span>
            </button>
            <div class="flex items-center gap-1.5">
              <Link
                :href="`/admin/customers/${customer.id}`"
                class="px-2.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs"
              >
                Hub
              </Link>
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200 font-bold text-xs cursor-pointer"
                @click="emit('edit', customer)"
              >
                Edit
              </button>
              <button
                type="button"
                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs cursor-pointer"
                @click="emit('delete', customer.id)"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Desktop Table (md and up) -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
            <tr>
              <th class="py-3.5 px-5">
                Customer Name & UID
              </th>
              <th class="py-3.5 px-5">
                Email & Phone
              </th>
              <th class="py-3.5 px-5">
                Gender
              </th>
              <th class="py-3.5 px-5">
                Registered Address
              </th>
              <th class="py-3.5 px-5 text-center">
                Rental Bookings
              </th>
              <th class="py-3.5 px-5 text-right">
                Admin Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
            <tr
              v-for="customer in customers"
              :key="customer.id"
              class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
              @click="emit('view', customer)"
            >
              <td class="py-4 px-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden border border-slate-200 dark:border-slate-800">
                    <img
                      v-if="getCustomerImage(customer)"
                      :src="getCustomerImage(customer)"
                      :alt="customer.name || customer.full_name"
                      class="w-full h-full object-cover"
                      @error="(e) => (e.target as HTMLElement).style.display = 'none'"
                    >
                    <span>{{ (customer.name || customer.full_name || 'C').charAt(0).toUpperCase() }}</span>
                  </div>
                  <div>
                    <span class="font-bold text-slate-900 dark:text-white block text-sm group-hover:text-indigo-600 transition-colors">
                      {{ customer.name || customer.full_name }}
                    </span>
                    <span class="text-slate-400 font-mono text-[10px]">UID: #{{ customer.unique_identifier || `CUS-${customer.id}` }}</span>
                  </div>
                </div>
              </td>

              <td class="py-4 px-5">
                <span class="font-mono text-slate-800 dark:text-slate-200 block">{{ customer.email }}</span>
                <span class="text-slate-400 text-[11px]">{{ customer.phone_number || customer.phone || 'N/A' }}</span>
              </td>

              <td class="py-4 px-5">
                <span class="capitalize px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                  {{ customer.gender || 'Not specified' }}
                </span>
              </td>

              <td class="py-4 px-5">
                <span class="text-slate-600 dark:text-slate-400 truncate max-w-xs block">{{ customer.address || 'No address on file' }}</span>
              </td>

              <td class="py-4 px-5 text-center">
                <span class="px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold font-mono">
                  {{ customer.bookings_count ?? 0 }} Bookings
                </span>
              </td>

              <td class="py-4 px-5 text-right space-x-1.5" @click.stop>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-[11px] transition-colors cursor-pointer inline-flex items-center gap-1"
                  @click="emit('view', customer)"
                >
                  <i class="ri-eye-line text-xs" />
                  <span>View</span>
                </button>
                <Link
                  :href="`/admin/customers/${customer.id}`"
                  class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-[11px] transition-colors inline-flex items-center gap-1 shadow-xs"
                >
                  <span>Customer Hub</span>
                  <i class="ri-arrow-right-line" />
                </Link>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-[11px] cursor-pointer"
                  @click="emit('edit', customer)"
                >
                  Edit
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] cursor-pointer"
                  @click="emit('delete', customer.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="pagination && (pagination.links || pagination.total)"
        class="border-t border-slate-100 dark:border-slate-800"
      >
        <Pagination
          :links="pagination.links"
          :from="pagination.from"
          :to="pagination.to"
          :total="pagination.total"
          :current-page="pagination.current_page"
          :last-page="pagination.last_page"
          :per-page="pagination.per_page"
        />
      </div>
    </template>
  </div>
</template>
