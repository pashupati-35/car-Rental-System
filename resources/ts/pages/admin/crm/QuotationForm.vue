<script setup lang="ts">
import { computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import CrmLayout from '@/layouts/CrmLayout.vue'

interface CarOption {
  id: number
  price_per_day?: number | string
  brand_name?: string
  name?: string
  [key: string]: any
}

const props = defineProps<{
  cars: CarOption[]
  customers: any[]
  leads: any[]
}>()

const form = useForm({
  customer_id: '',
  lead_id: '',
  car_id: '',
  start_date: new Date().toISOString().slice(0, 10),
  end_date: new Date(Date.now() + 86400000 * 3).toISOString().slice(0, 10),
  daily_rate: 0,
  tax_rate: 13,
  discount_amount: 0,
  valid_until: new Date(Date.now() + 86400000 * 14).toISOString().slice(0, 10),
  terms_conditions: "1. Full fuel to full fuel policy.\n2. Security deposit required on vehicle delivery.\n3. Standard insurance included with 24/7 roadside assistance.\n4. Valid driving license must be presented.",
  notes: '',
  status: 'draft',
  items: [] as Array<{ description: string; quantity: number; unit_price: number }>,
})

// Auto-fill car rate on selection
watch(() => form.car_id, (newCarId: string | number) => {
  const selected = props.cars.find((c: CarOption) => c.id === Number(newCarId))
  if (selected && selected.price_per_day) {
    form.daily_rate = Number(selected.price_per_day)
  }
})

const daysCount = computed(() => {
  if (!form.start_date || !form.end_date) return 1
  const start = new Date(form.start_date).getTime()
  const end = new Date(form.end_date).getTime()
  const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24))
  
  return Math.max(1, diff)
})

const rentalSubtotal = computed(() => {
  const base = Number(form.daily_rate || 0) * daysCount.value
  const itemsTotal = form.items.reduce((acc, it) => acc + (Number(it.quantity || 1) * Number(it.unit_price || 0)), 0)
  
  return base + itemsTotal
})

const taxAmount = computed(() => {
  const taxable = Math.max(0, rentalSubtotal.value - Number(form.discount_amount || 0))
  
  return Number(((taxable * Number(form.tax_rate || 0)) / 100).toFixed(2))
})

const grandTotal = computed(() => {
  const taxable = Math.max(0, rentalSubtotal.value - Number(form.discount_amount || 0))
  
  return Number((taxable + taxAmount.value).toFixed(2))
})

const addLineItem = (presetDescription = '', defaultPrice = 0) => {
  form.items.push({
    description: presetDescription || 'Additional Equipment / Service',
    quantity: 1,
    unit_price: defaultPrice,
  })
}

const removeLineItem = (index: number) => {
  form.items.splice(index, 1)
}

const submitQuotation = () => {
  form.post('/admin/crm/quotations')
}
</script>

<template>
  <CrmLayout>
    <Head title="Generate Rental Quotation - CRM" />

    <div class="max-w-4xl mx-auto space-y-6 pb-12">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <Link
          href="/crm/quotations"
          class="hover:text-indigo-600 transition flex items-center gap-1"
        >
          <i class="ri-arrow-left-line" /> Back to Quotations
        </Link>
        <span>/</span>
        <span>Create Rental Quotation</span>
      </div>

      <!-- Header Card -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
            Rental Proposal Builder (CPQ)
          </h1>
          <p class="text-sm text-slate-500 mt-0.5">
            Configure vehicle, rental duration, insurance and custom equipment to generate an official quote.
          </p>
        </div>
      </div>

      <!-- Form -->
      <form
        class="space-y-6"
        @submit.prevent="submitQuotation"
      >
        <!-- 1. Recipient Selection -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ri-user-3-line text-indigo-500" /> 1. Client / Recipient Information
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold mb-1">Select Registered Customer</label>
              <select
                v-model="form.customer_id"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
                <option value="">
                  Choose Customer (or choose Lead)
                </option>
                <option
                  v-for="c in customers"
                  :key="c.id"
                  :value="c.id"
                >
                  {{ c.name }} ({{ c.email || c.phone_number }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Or Select Inquiring Lead</label>
              <select
                v-model="form.lead_id"
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
        </div>

        <!-- 2. Vehicle & Period -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ri-car-line text-emerald-500" /> 2. Vehicle & Rental Period
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold mb-1">Select Fleet Vehicle *</label>
              <select
                v-model="form.car_id"
                required
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
                  {{ car.brand_name }} {{ car.name }} (${{ car.price_per_day }}/day)
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold mb-1">Agreed Daily Rental Rate ($) *</label>
              <input
                v-model="form.daily_rate"
                required
                type="number"
                step="0.01"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold mb-1">Start Date *</label>
              <input
                v-model="form.start_date"
                required
                type="date"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">End Date *</label>
              <input
                v-model="form.end_date"
                required
                type="date"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Total Duration</label>
              <div class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-sm font-bold text-indigo-600 dark:text-indigo-400">
                {{ daysCount }} Days
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Add-ons & Equipment -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <i class="ri-add-circle-line text-purple-500" /> 3. Add-ons, Insurance & Extras
            </h2>

            <div class="flex items-center gap-2">
              <button
                type="button"
                class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium hover:bg-slate-200"
                @click="addLineItem('Zero Excess Damage Waiver Insurance', 25)"
              >
                + Insurance Waiver
              </button>
              <button
                type="button"
                class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium hover:bg-slate-200"
                @click="addLineItem('Child Safety Seat', 10)"
              >
                + Child Seat
              </button>
              <button
                type="button"
                class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium hover:bg-slate-200"
                @click="addLineItem('Professional Chauffeur Service', 40)"
              >
                + Chauffeur
              </button>
            </div>
          </div>

          <div
            v-if="!form.items.length"
            class="text-center py-6 text-xs text-slate-400 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl"
          >
            No extra add-ons selected. Use buttons above or click "Custom Item" to add accessories.
          </div>

          <div
            v-else
            class="space-y-3"
          >
            <div
              v-for="(item, idx) in form.items"
              :key="idx"
              class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800"
            >
              <input
                v-model="item.description"
                type="text"
                placeholder="Description"
                class="flex-1 px-3 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-xs"
              >
              <div class="w-24">
                <input
                  v-model="item.quantity"
                  type="number"
                  min="1"
                  placeholder="Qty"
                  class="w-full px-2 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-xs text-center"
                >
              </div>
              <div class="w-28">
                <input
                  v-model="item.unit_price"
                  type="number"
                  step="0.01"
                  placeholder="Price"
                  class="w-full px-2 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-xs text-right"
                >
              </div>
              <div class="w-24 text-right text-xs font-bold text-slate-800 dark:text-slate-200">
                ${{ (Number(item.quantity || 1) * Number(item.unit_price || 0)).toFixed(2) }}
              </div>
              <button
                type="button"
                class="text-slate-400 hover:text-rose-500 transition"
                @click="removeLineItem(idx)"
              >
                <i class="ri-delete-bin-line" />
              </button>
            </div>
          </div>

          <button
            type="button"
            class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline flex items-center gap-1"
            @click="addLineItem('', 0)"
          >
            <i class="ri-add-line" /> Add Custom Line Item
          </button>
        </div>

        <!-- 4. Totals & Tax Calculation -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="ri-calculator-line text-indigo-500" /> 4. Pricing & Tax Calculation
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold mb-1">Promotional Discount ($)</label>
              <input
                v-model="form.discount_amount"
                type="number"
                step="0.01"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">VAT / Tax Rate (%)</label>
              <input
                v-model="form.tax_rate"
                type="number"
                step="0.01"
                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm"
              >
            </div>
          </div>

          <!-- Live Totals Review -->
          <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 space-y-2 text-sm">
            <div class="flex justify-between text-slate-500">
              <span>Rental Base + Add-ons Subtotal:</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200">${{ rentalSubtotal.toFixed(2) }}</span>
            </div>
            <div
              v-if="Number(form.discount_amount) > 0"
              class="flex justify-between text-rose-500"
            >
              <span>Discount Applied:</span>
              <span>-${{ Number(form.discount_amount).toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-slate-500">
              <span>Tax / VAT ({{ form.tax_rate }}%):</span>
              <span class="font-semibold text-slate-800 dark:text-slate-200">${{ taxAmount.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between text-lg font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700">
              <span>Total Quotation Amount:</span>
              <span class="text-indigo-600 dark:text-indigo-400">${{ grandTotal.toFixed(2) }}</span>
            </div>
          </div>
        </div>

        <!-- 5. Terms & Actions -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
          <div>
            <label class="block text-xs font-semibold mb-1">Terms & Conditions</label>
            <textarea
              v-model="form.terms_conditions"
              rows="3"
              class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono"
            />
          </div>

          <div class="flex items-center justify-end gap-3 pt-3">
            <Link
              href="/admin/crm/quotations"
              class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-medium"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition shadow-sm"
            >
              {{ form.processing ? 'Generating...' : 'Save & Issue Quotation' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </CrmLayout>
</template>
