<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'
import axios from 'axios'

const props = defineProps<{
  drivers?: Array<any>
}>()

const driversList = ref<Array<any>>(props.drivers || [])
const loading = ref(false)
const showModal = ref(false)
const editingDriver = ref<any>(null)
const submitting = ref(false)

const form = ref({
  name: '',
  phone: '',
  email: '',
  license_number: '',
  experience_years: '3',
  status: 'active',
  photo: null as any,
  license_photo: null as any,
})

const fetchDrivers = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/owner/drivers')
    if (res.data.status === 'success') {
      driversList.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to load drivers:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (!driversList.value.length) {
    fetchDrivers()
  }
})

const openCreateModal = () => {
  editingDriver.value = null
  form.value = {
    name: '',
    phone: '',
    email: '',
    license_number: '',
    experience_years: '3',
    status: 'active',
    photo: null,
    license_photo: null,
  }
  showModal.value = true
}

const openEditModal = (driver: any) => {
  editingDriver.value = driver
  form.value = {
    name: driver.name,
    phone: driver.phone,
    email: driver.email || '',
    license_number: driver.license_number,
    experience_years: driver.experience_years || '3',
    status: driver.status || 'active',
    photo: null,
    license_photo: null,
  }
  showModal.value = true
}

const handlePhotoUpload = (e: any) => {
  form.value.photo = e.target.files[0]
}


const saveDriver = async () => {
  if (!form.value.name || !form.value.phone || !form.value.license_number) {
    alert('Please fill in required fields: Name, Phone, and License Number.')
    
    return
  }

  submitting.value = true

  const formData = new FormData()

  formData.append('name', form.value.name)
  formData.append('phone', form.value.phone)
  if (form.value.email) formData.append('email', form.value.email)
  formData.append('license_number', form.value.license_number)
  formData.append('experience_years', form.value.experience_years)
  formData.append('status', form.value.status)
  if (form.value.photo) formData.append('photo', form.value.photo)
  if (form.value.license_photo) formData.append('license_photo', form.value.license_photo)

  try {
    if (editingDriver.value) {
      formData.append('_method', 'PATCH')
      await axios.post(`/api/owner/drivers/${editingDriver.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    } else {
      await axios.post('/api/owner/drivers', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    }
    showModal.value = false
    await fetchDrivers()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Error saving driver.')
  } finally {
    submitting.value = false
  }
}

const deleteDriver = async (id: number) => {
  if (!confirm('Are you sure you want to delete this driver?')) return

  try {
    await axios.delete(`/api/owner/drivers/${id}`)
    await fetchDrivers()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Error deleting driver.')
  }
}
</script>

<template>
  <OwnerLayout>
    <Head title="Owner - Driver Roster Management" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            Driver Roster & Staff Management
          </h1>
          <p class="text-xs text-gray-500 mt-1">
            Add, update, and manage your licensed drivers stored in the dedicated drivers system
          </p>
        </div>
        <button
          class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2"
          @click="openCreateModal"
        >
          <span>+ Add New Driver</span>
        </button>
      </div>

      <!-- Driver Cards List -->
      <div
        v-if="loading"
        class="py-16 text-center"
      >
        <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-2" />
        <p class="text-xs text-gray-500">
          Loading drivers...
        </p>
      </div>

      <div
        v-else-if="driversList.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <div
          v-for="driver in driversList"
          :key="driver.id"
          class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-4"
        >
          <div>
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-base overflow-hidden">
                  <img
                    v-if="driver.photo"
                    :src="'/' + driver.photo"
                    class="w-full h-full object-cover"
                  >
                  <span v-else>{{ driver.name[0] }}</span>
                </div>
                <div>
                  <h3 class="font-bold text-base text-gray-900 dark:text-white">
                    {{ driver.name }}
                  </h3>
                  <span class="text-[11px] text-gray-500 font-mono">
                    Lic: {{ driver.license_number }}
                  </span>
                </div>
              </div>
              <span
                :class="driver.status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'"
                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
              >
                {{ driver.status }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
              <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60">
                <span class="text-[10px] text-gray-400 block">Phone</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ driver.phone }}</span>
              </div>
              <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60">
                <span class="text-[10px] text-gray-400 block">Experience</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ driver.experience_years }} Years</span>
              </div>
            </div>

            <div
              v-if="driver.email"
              class="mt-2 text-xs text-gray-500"
            >
              ✉️ {{ driver.email }}
            </div>
          </div>

          <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
            <button
              class="flex-1 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-semibold transition-all"
              @click="openEditModal(driver)"
            >
              Edit Details
            </button>
            <button
              class="px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition-all"
              @click="deleteDriver(driver.id)"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <div
        v-else
        class="p-12 text-center bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800"
      >
        <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-3">
          👨‍✈️
        </div>
        <h3 class="font-bold text-base text-gray-900 dark:text-white">
          No Drivers Registered Yet
        </h3>
        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
          Add your trusted chauffeurs and drivers so you can assign them to your fleet cars.
        </p>
        <button
          class="mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold"
          @click="openCreateModal"
        >
          + Add First Driver
        </button>
      </div>
    </div>

    <!-- Modal for Create / Edit Driver -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto"
    >
      <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-5 relative">
        <button
          class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-bold"
          @click="showModal = false"
        >
          &times;
        </button>

        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
          {{ editingDriver ? 'Edit Driver Information' : 'Register New Driver' }}
        </h3>

        <form
          class="space-y-4"
          @submit.prevent="saveDriver"
        >
          <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Full Name *</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              placeholder="e.g. Ram Bahadur Thapa"
            >
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Phone Number *</label>
              <input
                v-model="form.phone"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                placeholder="+977 9801234567"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email (Optional)</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                placeholder="driver@gmail.com"
              >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">License Number *</label>
              <input
                v-model="form.license_number"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                placeholder="01-06-0098231"
              >
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Experience (Years)</label>
              <input
                v-model="form.experience_years"
                type="number"
                min="0"
                max="50"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Status</label>
              <select
                v-model="form.status"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none"
              >
                <option value="active">
                  Active (Available)
                </option>
                <option value="inactive">
                  Inactive
                </option>
                <option value="on_trip">
                  On Trip
                </option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Driver Photo</label>
              <input
                type="file"
                class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                @change="handlePhotoUpload"
              >
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-3">
            <button
              type="button"
              class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition-all"
              @click="showModal = false"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-500/25 transition-all"
            >
              <span v-if="submitting">Saving...</span>
              <span v-else>{{ editingDriver ? 'Update Driver' : 'Save Driver' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </OwnerLayout>
</template>
