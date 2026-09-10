<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import axios from 'axios'
import OwnerFormModal from './owners/components/OwnerFormModal.vue'

const props = defineProps<{
  owner: any
  cars: Array<any>
  drivers: Array<any>
  availableDrivers: Array<any>
  bookings: Array<any>
  stats: {
    total_cars: number
    verified_cars: number
    pending_cars: number
    rejected_cars: number
    total_drivers: number
    total_bookings: number
    confirmed_bookings: number
    total_revenue: number
  }
}>()

const activeTab = ref<'cars' | 'drivers' | 'bookings' | 'profile'>('cars')

// Search & filter states
const carSearch = ref('')
const carStatusFilter = ref('all')
const driverSearch = ref('')
const bookingSearch = ref('')

// Modals
const showEditOwnerModal = ref(false)
const showAddCarModal = ref(false)
const showEditCarModal = ref(false)
const showInspectCarModal = ref(false)
const showAddDriverModal = ref(false)
const showEditDriverModal = ref(false)

const selectedCar = ref<any | null>(null)
const selectedDriver = ref<any | null>(null)

// Owner Form
const resolveOwnerImage = (o: any) => {
  if (!o) return null
  if (o.image_path?.original) return o.image_path.original
  if (o.image) return o.image.startsWith('http') ? o.image : `/${o.image.replace(/^\/+/, '')}`
  return null
}

const resolveDriverImage = (d: any) => {
  if (!d) return null
  if (d.image_path?.original) return d.image_path.original
  if (d.photo_path?.original) return d.photo_path.original
  if (d.photo) return d.photo.startsWith('http') ? d.photo : `/${d.photo.replace(/^\/+/, '')}`
  if (d.image) return d.image.startsWith('http') ? d.image : `/${d.image.replace(/^\/+/, '')}`
  return null
}

const ownerForm = ref({
  full_name: props.owner.full_name || '',
  email: props.owner.email || '',
  contact_number: props.owner.contact_number || '',
  address: props.owner.address || '',
  gender: props.owner.gender || 'male',
  password: '',
})

// Car Form
const carForm = ref({
  id: 0,
  car_name: '',
  car_model: '',
  car_number: '',
  number_of_seats: 5,
  car_price_per_day: 50,
  car_price_per_km: 0,
  driver_id: '',
  status: 'verified',
  car_photo: null as File | null,
  blue_book_photo: null as File | null,
})

// Driver Form
const driverForm = ref({
  id: 0,
  name: '',
  phone: '',
  email: '',
  license_number: '',
  experience_years: 2,
  status: 'active',
  address: '',
  photo: null as File | null,
})

// Computed filtered lists
const filteredCars = computed(() => {
  let list = props.cars || []
  if (carStatusFilter.value !== 'all') {
    list = list.filter((c: any) => {
      if (carStatusFilter.value === 'verified') return c.status === 'verified' || c.status === 'available'
      if (carStatusFilter.value === 'pending') return c.status === 'pending'
      if (carStatusFilter.value === 'rejected') return c.status === 'rejected'
      
      return true
    })
  }
  if (carSearch.value.trim()) {
    const q = carSearch.value.toLowerCase()

    list = list.filter((c: any) => {
      const name = (c.car_name || '') + ' ' + (c.car_model || '')
      const num = c.car_number || ''
      
      return name.toLowerCase().includes(q) || num.toLowerCase().includes(q)
    })
  }
  
  return list
})

const filteredDrivers = computed(() => {
  let list = props.drivers || []
  if (driverSearch.value.trim()) {
    const q = driverSearch.value.toLowerCase()

    list = list.filter((d: any) => {
      const name = d.name || ''
      const phone = d.phone || ''
      const lic = d.license_number || ''
      
      return name.toLowerCase().includes(q) || phone.toLowerCase().includes(q) || lic.toLowerCase().includes(q)
    })
  }
  
  return list
})

const filteredBookings = computed(() => {
  let list = props.bookings || []
  if (bookingSearch.value.trim()) {
    const q = bookingSearch.value.toLowerCase()

    list = list.filter((b: any) => {
      const id = String(b.id)
      const cust = b.customer?.name || ''
      const car = (b.car?.car_name || '') + ' ' + (b.car?.car_model || '')
      
      return id.includes(q) || cust.toLowerCase().includes(q) || car.toLowerCase().includes(q)
    })
  }
  
  return list
})

// Owner Action Handlers
const submittingOwner = ref(false)
const ownerErrorMessage = ref('')

const submitUpdateOwner = async (formData: FormData) => {
  submittingOwner.value = true
  ownerErrorMessage.value = ''
  try {
    const res = await axios.post(`/admin/owners/${props.owner.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    if (res.data?.status === 'success' || res.status === 200) {
      showEditOwnerModal.value = false
      router.reload({ only: ['owner'] })
    }
  } catch (err: any) {
    ownerErrorMessage.value = err.response?.data?.message || 'Failed to update owner profile.'
  } finally {
    submittingOwner.value = false
  }
}

// Car Action Handlers
const openCreateCar = () => {
  carForm.value = {
    id: 0,
    car_name: '',
    car_model: '',
    car_number: '',
    number_of_seats: 5,
    car_price_per_day: 50,
    car_price_per_km: 0,
    driver_id: props.drivers[0]?.id || '',
    status: 'verified',
    car_photo: null,
    blue_book_photo: null,
  }
  showAddCarModal.value = true
}

const openEditCar = (car: any) => {
  selectedCar.value = car
  carForm.value = {
    id: car.id,
    car_name: car.car_name || '',
    car_model: car.car_model || '',
    car_number: car.car_number || '',
    number_of_seats: car.number_of_seats || 5,
    car_price_per_day: car.car_price_per_day || 50,
    car_price_per_km: car.car_price_per_km || 0,
    driver_id: car.driver_id || '',
    status: car.status || 'verified',
    car_photo: null,
    blue_book_photo: null,
  }
  showEditCarModal.value = true
}

const openInspectCar = (car: any) => {
  selectedCar.value = car
  showInspectCarModal.value = true
}

const submitCreateCar = () => {
  const formData = new FormData()

  formData.append('car_name', carForm.value.car_name)
  formData.append('car_model', carForm.value.car_model)
  formData.append('car_number', carForm.value.car_number)
  formData.append('number_of_seats', String(carForm.value.number_of_seats))
  formData.append('car_price_per_day', String(carForm.value.car_price_per_day))
  formData.append('car_price_per_km', String(carForm.value.car_price_per_km))
  if (carForm.value.driver_id) formData.append('driver_id', String(carForm.value.driver_id))
  formData.append('status', carForm.value.status)
  if (carForm.value.car_photo) formData.append('car_photo', carForm.value.car_photo)
  if (carForm.value.blue_book_photo) formData.append('blue_book_photo', carForm.value.blue_book_photo)

  router.post(`/admin/owners/${props.owner.id}/cars`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      showAddCarModal.value = false
    },
  })
}

const submitUpdateCar = () => {
  if (!selectedCar.value) return
  const formData = new FormData()

  formData.append('car_name', carForm.value.car_name)
  formData.append('car_model', carForm.value.car_model)
  formData.append('car_number', carForm.value.car_number)
  formData.append('number_of_seats', String(carForm.value.number_of_seats))
  formData.append('car_price_per_day', String(carForm.value.car_price_per_day))
  formData.append('car_price_per_km', String(carForm.value.car_price_per_km))
  if (carForm.value.driver_id) formData.append('driver_id', String(carForm.value.driver_id))
  formData.append('status', carForm.value.status)
  if (carForm.value.car_photo) formData.append('car_photo', carForm.value.car_photo)
  if (carForm.value.blue_book_photo) formData.append('blue_book_photo', carForm.value.blue_book_photo)

  router.post(`/admin/owners/${props.owner.id}/cars/${selectedCar.value.id}`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      showEditCarModal.value = false
    },
  })
}

const deleteCar = (carId: number) => {
  if (!confirm('Are you sure you want to delete this vehicle from the owner fleet?')) return
  router.delete(`/admin/owners/${props.owner.id}/cars/${carId}`, {
    preserveScroll: true,
  })
}

const verifyCar = (carId: number) => {
  router.patch(`/admin/cars/${carId}/verify`, {}, {
    preserveScroll: true,
  })
}

const rejectCar = (carId: number) => {
  router.patch(`/admin/cars/${carId}/reject`, {}, {
    preserveScroll: true,
  })
}

// Driver Action Handlers
const openCreateDriver = () => {
  driverForm.value = {
    id: 0,
    name: '',
    phone: '',
    email: '',
    license_number: '',
    experience_years: 2,
    status: 'active',
    address: '',
    photo: null,
    license_photo: null,
  }
  showAddDriverModal.value = true
}

const openEditDriver = (driver: any) => {
  selectedDriver.value = driver
  driverForm.value = {
    id: driver.id,
    name: driver.name || '',
    phone: driver.phone || '',
    email: driver.email || '',
    license_number: driver.license_number || '',
    experience_years: driver.experience_years || 1,
    status: driver.status || 'active',
    address: driver.address || '',
    photo: null,
    license_photo: null,
  }
  showEditDriverModal.value = true
}

const submitCreateDriver = () => {
  const formData = new FormData()

  formData.append('name', driverForm.value.name)
  formData.append('phone', driverForm.value.phone)
  if (driverForm.value.email) formData.append('email', driverForm.value.email)
  formData.append('license_number', driverForm.value.license_number)
  formData.append('experience_years', String(driverForm.value.experience_years))
  formData.append('status', driverForm.value.status)
  if (driverForm.value.address) formData.append('address', driverForm.value.address)
  if (driverForm.value.photo) formData.append('photo', driverForm.value.photo)
  if (driverForm.value.license_photo) formData.append('license_photo', driverForm.value.license_photo)

  router.post(`/admin/owners/${props.owner.id}/drivers`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      showAddDriverModal.value = false
    },
  })
}

const submitUpdateDriver = () => {
  if (!selectedDriver.value) return
  const formData = new FormData()

  formData.append('name', driverForm.value.name)
  formData.append('phone', driverForm.value.phone)
  if (driverForm.value.email) formData.append('email', driverForm.value.email)
  formData.append('license_number', driverForm.value.license_number)
  formData.append('experience_years', String(driverForm.value.experience_years))
  formData.append('status', driverForm.value.status)
  if (driverForm.value.address) formData.append('address', driverForm.value.address)
  if (driverForm.value.photo) formData.append('photo', driverForm.value.photo)
  if (driverForm.value.license_photo) formData.append('license_photo', driverForm.value.license_photo)

  router.post(`/admin/owners/${props.owner.id}/drivers/${selectedDriver.value.id}`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      showEditDriverModal.value = false
    },
  })
}

const deleteDriver = (driverId: number) => {
  if (!confirm('Are you sure you want to delete this driver?')) return
  router.delete(`/admin/owners/${props.owner.id}/drivers/${driverId}`, {
    preserveScroll: true,
  })
}

// Booking actions
const confirmBooking = (bookingId: number) => {
  router.post(`/admin/bookings/${bookingId}/confirm`, {}, { preserveScroll: true })
}

const cancelBooking = (bookingId: number) => {
  router.post(`/admin/bookings/${bookingId}/cancel`, {}, { preserveScroll: true })
}
</script>

<template>
  <AdminLayout>
    <Head :title="`${owner.full_name} - Owner Fleet Hub`" />

    <div class="space-y-6">
      <!-- Breadcrumb & Top Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <Link
            href="/admin/owners"
            class="w-10 h-10 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-colors shadow-2xs"
          >
            <i class="ri-arrow-left-line text-lg" />
          </Link>
          <div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-bold text-slate-400">Fleet Partners</span>
              <span class="text-slate-300">/</span>
              <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">Owner Hub</span>
            </div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
              {{ owner.full_name }}
            </h2>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <Link
            :href="`/admin/activity-logs?search=${encodeURIComponent(owner.full_name || owner.email || '')}`"
            class="px-3 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-50 transition-colors shadow-2xs flex items-center gap-1.5"
            title="View fleet owner activity logs"
          >
            <i class="ri-history-line text-indigo-600" />
            <span>Activity Logs</span>
          </Link>

          <Link
            :href="`/admin/email-logs?to=${encodeURIComponent(owner.email || '')}`"
            class="px-3 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-50 transition-colors shadow-2xs flex items-center gap-1.5"
            title="View fleet owner email logs"
          >
            <i class="ri-mail-check-line text-emerald-600" />
            <span>Email Logs</span>
          </Link>

          <button
            type="button"
            class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-50 transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer"
            @click="showEditOwnerModal = true"
          >
            <i class="ri-edit-line text-indigo-600" />
            <span>Edit Profile</span>
          </button>

          <button
            type="button"
            class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-1.5 cursor-pointer"
            @click="openCreateCar"
          >
            <i class="ri-car-add-line" />
            <span>Add Vehicle</span>
          </button>

          <button
            type="button"
            class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all flex items-center gap-1.5 cursor-pointer"
            @click="openCreateDriver"
          >
            <i class="ri-user-add-line" />
            <span>Add Driver</span>
          </button>
        </div>
      </div>

      <!-- Owner Profile Banner Card -->
      <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-[180px] pointer-events-none">
          <i class="ri-building-4-line" />
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
          <div class="flex items-center gap-5">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 to-indigo-500 text-white font-black text-2xl sm:text-3xl flex items-center justify-center shadow-lg shrink-0 overflow-hidden">
              <img
                v-if="resolveOwnerImage(owner)"
                :src="resolveOwnerImage(owner)!"
                :alt="owner.full_name"
                class="w-full h-full object-cover"
              >
              <span v-else>{{ owner.full_name ? owner.full_name.charAt(0).toUpperCase() : 'O' }}</span>
            </div>
            <div>
              <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                  Verified Fleet Partner
                </span>
                <span
                  :class="owner.is_mfa_enabled ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : 'bg-slate-700/50 text-slate-400 border border-slate-700'"
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                >
                  {{ owner.is_mfa_enabled ? 'MFA Security Active' : 'No MFA' }}
                </span>
              </div>
              <h3 class="text-xl sm:text-2xl font-black">
                {{ owner.full_name }}
              </h3>
              <p class="text-xs text-slate-300 font-mono flex flex-wrap items-center gap-4 mt-1">
                <span><i
                  class="ri-mail-line text-indigo-400"
                  style="margin-right: 0.25rem"
                />{{ owner.email }}</span>
                <span><i
                  class="ri-phone-line text-emerald-400"
                  style="margin-right: 0.25rem"
                />{{ owner.contact_number || 'N/A' }}</span>
                <span><i
                  class="ri-map-pin-line text-amber-400"
                  style="margin-right: 0.25rem"
                />{{ owner.address || 'Location Not Specified' }}</span>
              </p>
            </div>
          </div>

          <div class="flex items-center gap-6 border-t md:border-t-0 md:border-l border-slate-700/60 pt-4 md:pt-0 md:pl-6">
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Fleet Volume</span>
              <span class="text-2xl font-black text-emerald-400">${{ stats.total_revenue.toLocaleString() }}</span>
            </div>
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Registered</span>
              <span class="text-xs font-semibold text-slate-200">
                {{ owner.created_at ? new Date(owner.created_at).toLocaleDateString() : 'Recent' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Statistic Cards Grid -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">Fleet Vehicles</span>
            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center text-sm">
              <i class="ri-car-line" />
            </div>
          </div>
          <div class="mt-2 flex items-baseline gap-2">
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_cars }}</span>
            <span class="text-[11px] font-bold text-emerald-600">{{ stats.verified_cars }} Verified</span>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">Assigned Drivers</span>
            <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 flex items-center justify-center text-sm">
              <i class="ri-user-star-line" />
            </div>
          </div>
          <div class="mt-2 flex items-baseline gap-2">
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_drivers }}</span>
            <span class="text-[11px] font-semibold text-slate-400">Licensed</span>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">Total Bookings</span>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-sm">
              <i class="ri-calendar-check-line" />
            </div>
          </div>
          <div class="mt-2 flex items-baseline gap-2">
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total_bookings }}</span>
            <span class="text-[11px] font-bold text-emerald-600">{{ stats.confirmed_bookings }} Completed</span>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500">Owner Revenue</span>
            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 flex items-center justify-center text-sm">
              <i class="ri-money-dollar-circle-line" />
            </div>
          </div>
          <div class="mt-2">
            <span class="text-2xl font-black text-slate-900 dark:text-white">${{ stats.total_revenue.toLocaleString() }}</span>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-2 border-b border-slate-200/80 dark:border-slate-800 pb-2 overflow-x-auto">
        <button
          type="button"
          :class="activeTab === 'cars' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50'"
          class="px-4 py-2.5 rounded-2xl text-xs font-semibold flex items-center gap-2 shrink-0 transition-all cursor-pointer"
          @click="activeTab = 'cars'"
        >
          <i class="ri-car-line" />
          <span>Fleet Vehicles</span>
          <span
            class="px-2 py-0.2 rounded-full text-[10px] font-mono"
            :class="activeTab === 'cars' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800'"
          >
            {{ cars.length }}
          </span>
        </button>

        <button
          type="button"
          :class="activeTab === 'drivers' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50'"
          class="px-4 py-2.5 rounded-2xl text-xs font-semibold flex items-center gap-2 shrink-0 transition-all cursor-pointer"
          @click="activeTab = 'drivers'"
        >
          <i class="ri-user-star-line" />
          <span>Assigned Drivers</span>
          <span
            class="px-2 py-0.2 rounded-full text-[10px] font-mono"
            :class="activeTab === 'drivers' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800'"
          >
            {{ drivers.length }}
          </span>
        </button>

        <button
          type="button"
          :class="activeTab === 'bookings' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-800 hover:bg-slate-50'"
          class="px-4 py-2.5 rounded-2xl text-xs font-semibold flex items-center gap-2 shrink-0 transition-all cursor-pointer"
          @click="activeTab = 'bookings'"
        >
          <i class="ri-calendar-check-line" />
          <span>Rental Trips</span>
          <span
            class="px-2 py-0.2 rounded-full text-[10px] font-mono"
            :class="activeTab === 'bookings' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800'"
          >
            {{ bookings.length }}
          </span>
        </button>
      </div>

      <!-- TAB 1: FLEET VEHICLES -->
      <div
        v-if="activeTab === 'cars'"
        class="space-y-4"
      >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <div class="flex items-center gap-2 overflow-x-auto">
            <button
              v-for="st in ['all', 'verified', 'pending', 'rejected']"
              :key="st"
              type="button"
              :class="carStatusFilter === st ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
              class="px-3 py-1.5 rounded-xl text-xs capitalize cursor-pointer transition-colors shrink-0"
              @click="carStatusFilter = st"
            >
              {{ st }}
            </button>
          </div>

          <div class="relative w-full sm:w-64">
            <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
            <input
              v-model="carSearch"
              type="text"
              placeholder="Search vehicle or plate..."
              class="w-full py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
              style="padding-left: 2rem; padding-right: 0.75rem"
            >
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
              <tr>
                <th class="py-3.5 px-5">
                  Vehicle Specs
                </th>
                <th class="py-3.5 px-5">
                  Plate Number
                </th>
                <th class="py-3.5 px-5">
                  Assigned Driver
                </th>
                <th class="py-3.5 px-5">
                  Daily Rate
                </th>
                <th class="py-3.5 px-5">
                  Status
                </th>
                <th class="py-3.5 px-5 text-right">
                  Admin Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
              <tr v-if="filteredCars.length === 0">
                <td
                  colspan="6"
                  class="py-12 text-center text-slate-400"
                >
                  <i class="ri-car-line text-3xl mb-2 inline-block text-slate-300" />
                  <p class="font-semibold text-sm">
                    No vehicles registered for this owner yet.
                  </p>
                  <button
                    type="button"
                    class="mt-2 text-indigo-600 font-bold hover:underline"
                    @click="openCreateCar"
                  >
                    + Add first car for {{ owner.full_name }}
                  </button>
                </td>
              </tr>
              <tr
                v-for="car in filteredCars"
                :key="car.id"
                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Vehicle -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <img
                      :src="car.car_photo ? '/' + car.car_photo : (car.image ? '/' + car.image : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=120&auto=format&fit=crop&q=80')"
                      class="w-12 h-10 object-cover rounded-xl border border-slate-200 dark:border-slate-700 shrink-0"
                    >
                    <div>
                      <span class="font-bold text-slate-900 dark:text-white block text-sm">
                        {{ car.car_name }} {{ car.car_model }}
                      </span>
                      <span class="text-[10px] text-slate-400">{{ car.number_of_seats || 5 }} Seats • {{ car.car_price_per_km ? '$' + car.car_price_per_km + '/km' : 'Unlimited' }}</span>
                    </div>
                  </div>
                </td>

                <!-- Plate -->
                <td class="py-4 px-5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                  {{ car.car_number || 'N/A' }}
                </td>

                <!-- Driver -->
                <td class="py-4 px-5">
                  <span class="font-semibold text-slate-800 dark:text-slate-200 block">
                    {{ car.driver?.name || car.driver_name || 'No Driver' }}
                  </span>
                  <span
                    v-if="car.driver?.phone"
                    class="text-[10px] text-slate-400 font-mono"
                  >{{ car.driver.phone }}</span>
                </td>

                <!-- Rate -->
                <td class="py-4 px-5">
                  <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm">
                    ${{ car.car_price_per_day }}<span class="text-[10px] text-slate-400 font-normal">/day</span>
                  </span>
                </td>

                <!-- Status -->
                <td class="py-4 px-5">
                  <span
                    :class="{
                      'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300': car.status === 'verified' || car.status === 'available',
                      'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300': car.status === 'pending',
                      'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300': car.status === 'rejected',
                    }"
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                  >
                    {{ car.status || 'Pending' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-4 px-5 text-right space-x-1.5">
                  <button
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="openInspectCar(car)"
                  >
                    Inspect
                  </button>

                  <button
                    v-if="car.status !== 'verified' && car.status !== 'available'"
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition-colors cursor-pointer"
                    @click="verifyCar(car.id)"
                  >
                    Approve
                  </button>

                  <button
                    v-if="car.status !== 'rejected'"
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="rejectCar(car.id)"
                  >
                    Reject
                  </button>

                  <button
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="openEditCar(car)"
                  >
                    Edit
                  </button>

                  <button
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="deleteCar(car.id)"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 2: ASSIGNED DRIVERS -->
      <div
        v-if="activeTab === 'drivers'"
        class="space-y-4"
      >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
            Drivers assigned exclusively to {{ owner.full_name }}
          </span>

          <div class="relative w-full sm:w-64">
            <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
            <input
              v-model="driverSearch"
              type="text"
              placeholder="Search driver by name, phone..."
              class="w-full py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
              style="padding-left: 2rem; padding-right: 0.75rem"
            >
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
              <tr>
                <th class="py-3.5 px-5">
                  Driver Name
                </th>
                <th class="py-3.5 px-5">
                  Contact Details
                </th>
                <th class="py-3.5 px-5">
                  License Number
                </th>
                <th class="py-3.5 px-5">
                  Experience
                </th>
                <th class="py-3.5 px-5">
                  Status
                </th>
                <th class="py-3.5 px-5 text-right">
                  Admin Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
              <tr v-if="filteredDrivers.length === 0">
                <td
                  colspan="6"
                  class="py-12 text-center text-slate-400"
                >
                  <i class="ri-user-star-line text-3xl mb-2 inline-block text-slate-300" />
                  <p class="font-semibold text-sm">
                    No drivers assigned to this owner yet.
                  </p>
                  <button
                    type="button"
                    class="mt-2 text-indigo-600 font-bold hover:underline"
                    @click="openCreateDriver"
                  >
                    + Register driver for {{ owner.full_name }}
                  </button>
                </td>
              </tr>
              <tr
                v-for="driver in filteredDrivers"
                :key="driver.id"
                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Profile -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 font-bold flex items-center justify-center text-xs overflow-hidden">
                      <img
                        v-if="resolveDriverImage(driver)"
                        :src="resolveDriverImage(driver)!"
                        class="w-full h-full object-cover"
                      >
                      <span v-else>{{ driver.name ? driver.name[0].toUpperCase() : 'D' }}</span>
                    </div>
                    <div>
                      <span class="font-bold text-slate-900 dark:text-white block text-sm">
                        {{ driver.name }}
                      </span>
                      <span class="text-[10px] text-slate-400">{{ driver.address || 'Standard Roster' }}</span>
                    </div>
                  </div>
                </td>

                <!-- Contact -->
                <td class="py-4 px-5 font-mono">
                  <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ driver.phone }}</span>
                  <span class="text-[10px] text-slate-400">{{ driver.email || 'N/A' }}</span>
                </td>

                <!-- License -->
                <td class="py-4 px-5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                  {{ driver.license_number }}
                </td>

                <!-- Experience -->
                <td class="py-4 px-5 font-semibold text-slate-700 dark:text-slate-300">
                  {{ driver.experience_years || 1 }} Years
                </td>

                <!-- Status -->
                <td class="py-4 px-5">
                  <span
                    :class="driver.status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-700'"
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                  >
                    {{ driver.status || 'Active' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-4 px-5 text-right space-x-1.5">
                  <button
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="openEditDriver(driver)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="deleteDriver(driver.id)"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 3: RENTAL BOOKINGS -->
      <div
        v-if="activeTab === 'bookings'"
        class="space-y-4"
      >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
          <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
            Booking reservations associated with {{ owner.full_name }}'s vehicles
          </span>

          <div class="relative w-full sm:w-64">
            <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-xs" />
            <input
              v-model="bookingSearch"
              type="text"
              placeholder="Search booking ID or customer..."
              class="w-full py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:ring-2 focus:ring-indigo-500"
              style="padding-left: 2rem; padding-right: 0.75rem"
            >
          </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold border-b border-slate-200/80 dark:border-slate-800">
              <tr>
                <th class="py-3.5 px-5">
                  Booking ID
                </th>
                <th class="py-3.5 px-5">
                  Customer
                </th>
                <th class="py-3.5 px-5">
                  Vehicle
                </th>
                <th class="py-3.5 px-5">
                  Duration
                </th>
                <th class="py-3.5 px-5">
                  Revenue
                </th>
                <th class="py-3.5 px-5">
                  Status
                </th>
                <th class="py-3.5 px-5 text-right">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
              <tr v-if="filteredBookings.length === 0">
                <td
                  colspan="7"
                  class="py-12 text-center text-slate-400"
                >
                  <i class="ri-calendar-line text-3xl mb-2 inline-block text-slate-300" />
                  <p class="font-semibold text-sm">
                    No rental bookings recorded for this owner yet.
                  </p>
                </td>
              </tr>
              <tr
                v-for="b in filteredBookings"
                :key="b.id"
                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
              >
                <!-- Booking ID -->
                <td class="py-4 px-5 font-mono font-bold text-slate-900 dark:text-white">
                  #BK-{{ b.id }}
                </td>

                <!-- Customer -->
                <td class="py-4 px-5">
                  <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ b.customer?.name || 'Customer' }}</span>
                  <span class="text-[10px] text-slate-400 font-mono">{{ b.customer?.email }}</span>
                </td>

                <!-- Vehicle -->
                <td class="py-4 px-5">
                  <span class="font-semibold text-slate-900 dark:text-white block">{{ b.car?.car_name }} {{ b.car?.car_model }}</span>
                  <span class="text-[10px] text-indigo-600 font-mono">{{ b.car?.car_number }}</span>
                </td>

                <!-- Duration -->
                <td class="py-4 px-5 text-slate-600 dark:text-slate-400 text-[11px]">
                  {{ b.pick_up_date ? new Date(b.pick_up_date).toLocaleDateString() : 'N/A' }} &rarr;
                  {{ b.last_date ? new Date(b.last_date).toLocaleDateString() : 'N/A' }}
                </td>

                <!-- Revenue -->
                <td class="py-4 px-5 font-black text-emerald-600 text-sm">
                  ${{ b.total_price || 0 }}
                </td>

                <!-- Status -->
                <td class="py-4 px-5">
                  <span
                    :class="{
                      'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300': b.status === 'confirm' || b.status === 'confirmed' || b.status === 'completed',
                      'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300': b.status === 'pending',
                      'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300': b.status === 'cancel' || b.status === 'cancelled',
                    }"
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                  >
                    {{ b.status || 'Pending' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-4 px-5 text-right space-x-1.5">
                  <button
                    v-if="b.status !== 'confirm' && b.status !== 'confirmed'"
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition-colors cursor-pointer"
                    @click="confirmBooking(b.id)"
                  >
                    Confirm
                  </button>
                  <button
                    v-if="b.status !== 'cancel' && b.status !== 'cancelled'"
                    type="button"
                    class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] transition-colors cursor-pointer"
                    @click="cancelBooking(b.id)"
                  >
                    Cancel
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- MODALS -->

      <!-- 1. Edit Owner Modal -->
      <OwnerFormModal
        :owner="props.owner"
        :show="showEditOwnerModal"
        is-editing
        :submitting="submittingOwner"
        :error-message="ownerErrorMessage"
        @close="showEditOwnerModal = false"
        @save="submitUpdateOwner"
      />

      <!-- 2. Add / Edit Car Modal -->
      <div
        v-if="showAddCarModal || showEditCarModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
      >
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h3 class="font-bold text-lg text-slate-900 dark:text-white">
                {{ showEditCarModal ? 'Edit Vehicle Specs' : 'Add Vehicle for ' + owner.full_name }}
              </h3>
              <p class="text-[11px] text-slate-400">
                Owner ID: #{{ owner.id }} ({{ owner.full_name }})
              </p>
            </div>
            <button
              class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
              @click="showAddCarModal = false; showEditCarModal = false"
            >
              &times;
            </button>
          </div>

          <form
            class="space-y-3.5 text-xs"
            @submit.prevent="showEditCarModal ? submitUpdateCar() : submitCreateCar()"
          >
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">Vehicle Make / Brand *</label>
                <input
                  v-model="carForm.car_name"
                  type="text"
                  required
                  placeholder="e.g. Toyota, Tesla"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
              </div>
              <div>
                <label class="block font-bold mb-1">Model Name *</label>
                <input
                  v-model="carForm.car_model"
                  type="text"
                  required
                  placeholder="e.g. Fortuner 4x4"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">License Plate Number *</label>
                <input
                  v-model="carForm.car_number"
                  type="text"
                  required
                  placeholder="BA-1-PA-1024"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono"
                >
              </div>
              <div>
                <label class="block font-bold mb-1">Seating Capacity</label>
                <input
                  v-model="carForm.number_of_seats"
                  type="number"
                  min="1"
                  max="50"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">Daily Price ($/day) *</label>
                <input
                  v-model="carForm.car_price_per_day"
                  type="number"
                  step="0.01"
                  required
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold text-emerald-600"
                >
              </div>
              <div>
                <label class="block font-bold mb-1">Rate per KM ($/km)</label>
                <input
                  v-model="carForm.car_price_per_km"
                  type="number"
                  step="0.01"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">Assign Driver</label>
                <select
                  v-model="carForm.driver_id"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
                  <option value="">
                    No Driver Assigned
                  </option>
                  <option
                    v-for="d in availableDrivers"
                    :key="d.id"
                    :value="d.id"
                  >
                    {{ d.name }} ({{ d.phone }})
                  </option>
                </select>
              </div>
              <div>
                <label class="block font-bold mb-1">Verification Status</label>
                <select
                  v-model="carForm.status"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold"
                >
                  <option value="verified">
                    Verified (Approved)
                  </option>
                  <option value="pending">
                    Pending
                  </option>
                  <option value="rejected">
                    Rejected
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">Car Photo</label>
                <input
                  type="file"
                  class="w-full text-[11px]"
                  @change="(e: any) => carForm.car_photo = e.target.files[0]"
                >
              </div>
              <div>
                <label class="block font-bold mb-1">Blue Book Photo</label>
                <input
                  type="file"
                  class="w-full text-[11px]"
                  @change="(e: any) => carForm.blue_book_photo = e.target.files[0]"
                >
              </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
              <button
                type="button"
                class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold"
                @click="showAddCarModal = false; showEditCarModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold"
              >
                {{ showEditCarModal ? 'Update Vehicle' : 'Add to Fleet' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- 3. Add / Edit Driver Modal -->
      <div
        v-if="showAddDriverModal || showEditDriverModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
      >
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
              <h3 class="font-bold text-lg text-slate-900 dark:text-white">
                {{ showEditDriverModal ? 'Edit Driver Record' : 'Register Driver for ' + owner.full_name }}
              </h3>
              <p class="text-[11px] text-slate-400">
                Assigned Fleet Owner: {{ owner.full_name }}
              </p>
            </div>
            <button
              class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
              @click="showAddDriverModal = false; showEditDriverModal = false"
            >
              &times;
            </button>
          </div>

          <form
            class="space-y-3.5 text-xs"
            @submit.prevent="showEditDriverModal ? submitUpdateDriver() : submitCreateDriver()"
          >
            <div>
              <label class="block font-bold mb-1">Driver Full Name *</label>
              <input
                v-model="driverForm.name"
                type="text"
                required
                placeholder="Driver name"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
              >
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">Phone Number *</label>
                <input
                  v-model="driverForm.phone"
                  type="text"
                  required
                  placeholder="+1 234 567 890"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
              </div>
              <div>
                <label class="block font-bold mb-1">Email Address</label>
                <input
                  v-model="driverForm.email"
                  type="email"
                  placeholder="driver@example.com"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">License Number *</label>
                <input
                  v-model="driverForm.license_number"
                  type="text"
                  required
                  placeholder="DL-98765432"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono"
                >
              </div>
              <div>
                <label class="block font-bold mb-1">Driving Experience (Years)</label>
                <input
                  v-model="driverForm.experience_years"
                  type="number"
                  min="0"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">Status</label>
                <select
                  v-model="driverForm.status"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold"
                >
                  <option value="active">
                    Active
                  </option>
                  <option value="inactive">
                    Inactive
                  </option>
                </select>
              </div>
              <div>
                <label class="block font-bold mb-1">Address / Region</label>
                <input
                  v-model="driverForm.address"
                  type="text"
                  placeholder="City, Region"
                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-bold mb-1">Driver Photo</label>
                <input
                  type="file"
                  accept="image/*"
                  class="w-full text-[11px]"
                  @change="(e: any) => driverForm.photo = e.target.files[0]"
                >
              </div>
              <div>
                <label class="block font-bold mb-1">License Document</label>
                <input
                  type="file"
                  accept="image/*,.pdf,.doc,.docx"
                  class="w-full text-[11px]"
                  @change="(e: any) => driverForm.license_photo = e.target.files[0]"
                >
              </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
              <button
                type="button"
                class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold"
                @click="showAddDriverModal = false; showEditDriverModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold"
              >
                {{ showEditDriverModal ? 'Update Driver' : 'Register Driver' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- 4. Inspect Car Modal -->
      <div
        v-if="showInspectCarModal && selectedCar"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
      >
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">
              Inspection Details: {{ selectedCar.car_name }} {{ selectedCar.car_model }}
            </h3>
            <button
              class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
              @click="showInspectCarModal = false"
            >
              &times;
            </button>
          </div>

          <div class="flex items-center gap-4">
            <img
              :src="selectedCar.car_photo ? '/' + selectedCar.car_photo : (selectedCar.image ? '/' + selectedCar.image : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=200&auto=format&fit=crop&q=80')"
              class="w-28 h-20 object-cover rounded-2xl border border-slate-200"
            >
            <div>
              <h4 class="font-bold text-base text-slate-900 dark:text-white">
                {{ selectedCar.car_name }} {{ selectedCar.car_model }}
              </h4>
              <p class="text-xs text-indigo-600 font-bold font-mono">
                Plate: {{ selectedCar.car_number }}
              </p>
              <p class="text-xs text-slate-500">
                Fleet Owner: {{ owner.full_name }}
              </p>
            </div>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
            <div class="p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Daily Rate</span>
              <span class="font-black text-emerald-600 text-sm">${{ selectedCar.car_price_per_day }}/d</span>
            </div>
            <div class="p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Seats</span>
              <span class="font-bold text-slate-900 dark:text-white">{{ selectedCar.number_of_seats || 5 }} Seats</span>
            </div>
            <div class="p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60">
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Driver</span>
              <span class="font-bold text-slate-900 dark:text-white truncate block">{{ selectedCar.driver?.name || 'None' }}</span>
            </div>
          </div>

          <div
            v-if="selectedCar.blue_book_photo"
            class="space-y-1"
          >
            <span class="text-[11px] font-bold text-slate-500 block">Bluebook / Registration Document:</span>
            <img
              :src="'/' + selectedCar.blue_book_photo"
              class="max-h-40 rounded-xl border border-slate-200 object-contain w-full bg-slate-50"
            >
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-xs"
              @click="showInspectCarModal = false"
            >
              Close
            </button>
            <button
              v-if="selectedCar.status !== 'verified' && selectedCar.status !== 'available'"
              type="button"
              class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs"
              @click="verifyCar(selectedCar.id); showInspectCarModal = false"
            >
              Approve Listing
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
