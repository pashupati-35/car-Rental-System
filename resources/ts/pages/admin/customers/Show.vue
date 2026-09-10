<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import type { CustomerItem, CustomerStats, CustomerBookingItem, CustomerPaymentItem } from './types'
import CustomerStatsOverview from './components/details/CustomerStatsOverview.vue'
import CustomerBookingsTab from './components/details/CustomerBookingsTab.vue'
import CustomerPaymentsTab from './components/details/CustomerPaymentsTab.vue'
import BookingFormModal from './components/details/BookingFormModal.vue'
import PaymentFormModal from './components/details/PaymentFormModal.vue'
import CustomerFormModal from './components/CustomerFormModal.vue'

const props = defineProps<{
  customer: CustomerItem
  bookings: CustomerBookingItem[]
  payments: CustomerPaymentItem[]
  availableCars: Array<any>
  stats: CustomerStats
}>()

const activeTab = ref<'bookings' | 'payments' | 'profile'>('bookings')
const message = ref('')
const errorMessage = ref('')
const submitting = ref(false)

// Edit Profile Modal
const showEditProfileModal = ref(false)

const profileForm = ref<Partial<CustomerItem>>({
  name: props.customer.name || props.customer.full_name || '',
  email: props.customer.email,
  phone_number: props.customer.phone_number || props.customer.phone || '',
  address: props.customer.address || '',
  gender: props.customer.gender || 'male',
})

const openEditProfile = () => {
  profileForm.value = {
    name: props.customer.name || props.customer.full_name || '',
    email: props.customer.email,
    phone_number: props.customer.phone_number || props.customer.phone || '',
    address: props.customer.address || '',
    gender: props.customer.gender || 'male',
  }
  errorMessage.value = ''
  showEditProfileModal.value = true
}

const saveCustomerProfile = async () => {
  submitting.value = true
  errorMessage.value = ''
  try {
    const res = await axios.patch(`/admin/customers/${props.customer.id}`, profileForm.value)
    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Customer profile updated successfully.'
      showEditProfileModal.value = false
      router.reload({ only: ['customer'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to update profile.'
  } finally {
    submitting.value = false
  }
}

// Booking Modal State
const showBookingModal = ref(false)
const isEditingBooking = ref(false)

const bookingForm = ref<Partial<CustomerBookingItem>>({
  id: 0,
  car_id: props.availableCars[0]?.id || 0,
  pick_up_date: new Date().toISOString().split('T')[0],
  last_date: new Date(Date.now() + 86400000 * 3).toISOString().split('T')[0],
  pickup_location: 'Central Airport Hub',
  drop_location: 'Central Airport Hub',
  total_price: props.availableCars[0]?.price_per_day || 150,
  status: 'confirm',
  purpose: 'Personal Travel',
})

const openAddBookingModal = () => {
  isEditingBooking.value = false
  bookingForm.value = {
    id: 0,
    car_id: props.availableCars[0]?.id || 0,
    pick_up_date: new Date().toISOString().split('T')[0],
    last_date: new Date(Date.now() + 86400000 * 3).toISOString().split('T')[0],
    pickup_location: 'Central Airport Hub',
    drop_location: 'Central Airport Hub',
    total_price: props.availableCars[0]?.price_per_day || 150,
    status: 'confirm',
    purpose: 'Personal Travel',
  }
  errorMessage.value = ''
  showBookingModal.value = true
}

const openEditBookingModal = (b: CustomerBookingItem) => {
  isEditingBooking.value = true
  bookingForm.value = {
    id: b.id,
    car_id: b.car_id,
    pick_up_date: b.pick_up_date ? b.pick_up_date.split('T')[0] : '',
    last_date: b.last_date ? b.last_date.split('T')[0] : '',
    pickup_location: b.pickup_location,
    drop_location: b.drop_location,
    total_price: b.total_price,
    status: b.status,
    purpose: b.purpose || '',
  }
  errorMessage.value = ''
  showBookingModal.value = true
}

const saveBooking = async () => {
  submitting.value = true
  errorMessage.value = ''
  try {
    if (isEditingBooking.value && bookingForm.value.id) {
      const res = await axios.post(`/admin/customers/${props.customer.id}/bookings/${bookingForm.value.id}`, bookingForm.value)
      if (res.data?.status === 'success' || res.status === 200) {
        message.value = 'Booking record updated.'
        showBookingModal.value = false
        router.reload({ only: ['bookings', 'stats'] })
      }
    } else {
      const res = await axios.post(`/admin/customers/${props.customer.id}/bookings`, bookingForm.value)
      if (res.data?.status === 'success' || res.status === 200 || res.status === 201) {
        message.value = 'Vehicle booked successfully for customer.'
        showBookingModal.value = false
        router.reload({ only: ['bookings', 'stats'] })
      }
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to save booking.'
  } finally {
    submitting.value = false
  }
}

const deleteBooking = async (bookingId: number) => {
  if (!confirm('Are you sure you want to delete this booking order?')) return
  try {
    const res = await axios.delete(`/admin/customers/${props.customer.id}/bookings/${bookingId}`)
    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Booking deleted.'
      router.reload({ only: ['bookings', 'stats'] })
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete booking.')
  }
}

// Payment Modal State
const showPaymentModal = ref(false)

const paymentForm = ref({
  booking_id: props.bookings[0]?.id || '',
  car_id: props.bookings[0]?.car_id || '',
  amount: props.bookings[0]?.total_price || 150,
  card_number: '4242',
  expiry_date: '12/28',
})

const openRecordPaymentModal = () => {
  if (props.bookings.length === 0) {
    alert('Please create a rental booking order for this customer before recording a payment.')
    
    return
  }
  paymentForm.value = {
    booking_id: props.bookings[0].id,
    car_id: props.bookings[0].car_id,
    amount: props.bookings[0].total_price,
    card_number: '4242',
    expiry_date: '12/28',
  }
  errorMessage.value = ''
  showPaymentModal.value = true
}

const savePayment = async () => {
  submitting.value = true
  errorMessage.value = ''
  try {
    const res = await axios.post(`/admin/customers/${props.customer.id}/payments`, paymentForm.value)
    if (res.data?.status === 'success' || res.status === 200 || res.status === 201) {
      message.value = 'Payment transaction recorded.'
      showPaymentModal.value = false
      router.reload({ only: ['payments', 'stats'] })
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Failed to record payment.'
  } finally {
    submitting.value = false
  }
}

const deletePayment = async (paymentId: number) => {
  if (!confirm('Are you sure you want to remove this payment record?')) return
  try {
    const res = await axios.delete(`/admin/customers/${props.customer.id}/payments/${paymentId}`)
    if (res.data?.status === 'success' || res.status === 200) {
      message.value = 'Payment record removed.'
      router.reload({ only: ['payments', 'stats'] })
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete payment.')
  }
}
</script>

<template>
  <AdminLayout>
    <Head :title="`${customer.name || customer.full_name} - Customer Hub`" />

    <div class="space-y-6">
      <!-- Back Link & Title Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <Link
              href="/admin/customers"
              class="text-xs font-bold text-slate-500 hover:text-indigo-600 flex items-center gap-1 transition-colors"
            >
              <i class="ri-arrow-left-line" />
              <span>Back to Customers</span>
            </Link>
            <span class="text-slate-300 dark:text-slate-700">&bull;</span>
            <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
              Customer Hub
            </span>
          </div>

          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-lg shadow-md shadow-indigo-500/20">
              {{ (customer.name || customer.full_name || 'C').charAt(0).toUpperCase() }}
            </div>
            <div>
              <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                {{ customer.name || customer.full_name }}
              </h2>
              <p class="text-xs text-slate-500 font-mono">
                {{ customer.email }} &bull; Customer ID #CUST-{{ customer.id }}
              </p>
            </div>
          </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200 font-bold text-xs cursor-pointer flex items-center gap-1.5"
            @click="openEditProfile"
          >
            <i class="ri-edit-line" />
            <span>Edit Profile</span>
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 cursor-pointer flex items-center gap-1.5"
            @click="openAddBookingModal"
          >
            <i class="ri-add-line" />
            <span>Book Car</span>
          </button>
        </div>
      </div>

      <!-- Flash Notification -->
      <div
        v-if="message"
        class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs"
      >
        <i class="ri-checkbox-circle-fill text-emerald-600 text-base shrink-0" />
        <span>{{ message }}</span>
      </div>

      <!-- KPI Stats Overview -->
      <CustomerStatsOverview :stats="props.stats" />

      <!-- Tab Navigation -->
      <div class="flex items-center gap-2 border-b border-slate-200/80 dark:border-slate-800 pb-2">
        <button
          type="button"
          :class="activeTab === 'bookings' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50'"
          class="px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer font-semibold"
          @click="activeTab = 'bookings'"
        >
          <i class="ri-calendar-check-line" />
          <span>Rental Bookings</span>
          <span
            class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
            :class="activeTab === 'bookings' ? 'bg-white/20' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
          >
            {{ bookings.length }}
          </span>
        </button>

        <button
          type="button"
          :class="activeTab === 'payments' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50'"
          class="px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer font-semibold"
          @click="activeTab = 'payments'"
        >
          <i class="ri-money-dollar-circle-line" />
          <span>Payment History</span>
          <span
            class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
            :class="activeTab === 'payments' ? 'bg-white/20' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
          >
            {{ payments.length }}
          </span>
        </button>

        <button
          type="button"
          :class="activeTab === 'profile' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50'"
          class="px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer font-semibold"
          @click="activeTab = 'profile'"
        >
          <i class="ri-user-line" />
          <span>Customer Details</span>
        </button>
      </div>

      <!-- Tab Contents -->
      <div>
        <!-- Bookings Tab -->
        <CustomerBookingsTab
          v-if="activeTab === 'bookings'"
          :bookings="bookings"
          @add-booking="openAddBookingModal"
          @edit-booking="openEditBookingModal"
          @delete-booking="deleteBooking"
        />

        <!-- Payments Tab -->
        <CustomerPaymentsTab
          v-else-if="activeTab === 'payments'"
          :payments="payments"
          @record-payment="openRecordPaymentModal"
          @delete-payment="deletePayment"
        />

        <!-- Profile Details Tab -->
        <div
          v-else-if="activeTab === 'profile'"
          class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-6"
        >
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
              <h3 class="font-bold text-base text-slate-900 dark:text-white">
                Account Information & Address
              </h3>
              <p class="text-xs text-slate-500">
                Contact information, location, and account verification.
              </p>
            </div>
            <button
              type="button"
              class="px-3.5 py-1.5 rounded-xl bg-indigo-600 text-white font-bold text-xs cursor-pointer hover:bg-indigo-700"
              @click="openEditProfile"
            >
              Edit Details
            </button>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 space-y-1">
              <span class="text-[10px] uppercase font-bold text-slate-400">Full Name</span>
              <p class="font-bold text-sm text-slate-900 dark:text-white">
                {{ customer.name || customer.full_name }}
              </p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 space-y-1">
              <span class="text-[10px] uppercase font-bold text-slate-400">Email Address</span>
              <p class="font-semibold text-slate-800 dark:text-slate-200 font-mono">
                {{ customer.email }}
              </p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 space-y-1">
              <span class="text-[10px] uppercase font-bold text-slate-400">Phone Contact</span>
              <p class="font-semibold text-slate-800 dark:text-slate-200">
                {{ customer.phone_number || customer.phone || 'N/A' }}
              </p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 space-y-1">
              <span class="text-[10px] uppercase font-bold text-slate-400">Gender</span>
              <p class="capitalize font-semibold text-slate-800 dark:text-slate-200">
                {{ customer.gender || 'Not specified' }}
              </p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 space-y-1 sm:col-span-2">
              <span class="text-[10px] uppercase font-bold text-slate-400">Registered Address</span>
              <p class="font-semibold text-slate-800 dark:text-slate-200">
                {{ customer.address || 'No address provided' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Modals -->
      <!-- Create / Edit Booking Modal -->
      <BookingFormModal
        v-model:booking-form="bookingForm"
        :show="showBookingModal"
        :is-editing="isEditingBooking"
        :available-cars="availableCars"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showBookingModal = false"
        @save="saveBooking"
      />

      <!-- Record Payment Modal -->
      <PaymentFormModal
        v-model:payment-form="paymentForm"
        :show="showPaymentModal"
        :bookings="bookings"
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showPaymentModal = false"
        @save="savePayment"
      />

      <!-- Edit Customer Profile Modal -->
      <CustomerFormModal
        v-model:form="profileForm"
        :show="showEditProfileModal"
        is-editing
        :submitting="submitting"
        :error-message="errorMessage"
        @close="showEditProfileModal = false"
        @save="saveCustomerProfile"
      />
    </div>
  </AdminLayout>
</template>
