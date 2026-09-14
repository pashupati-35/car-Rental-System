<script setup lang="ts">
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'
import axios from 'axios'
import MessageBox from '@/components/MessageBox.vue'

defineProps<{
  drivers: Array<any>
}>()

const showModal = ref(false)
const editingDriver = ref<any>(null)
const submitting = ref(false)
const feedbackMsg = ref('')
const errorMsg = ref('')

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

const photoPreview = ref<string | null>(null)
const licensePhotoPreview = ref<string | null>(null)

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
  photoPreview.value = null
  licensePhotoPreview.value = null
  showModal.value = true
}

const openEditModal = (driver: any) => {
  editingDriver.value = driver
  form.value = {
    name: driver.name,
    phone: driver.phone,
    email: driver.email || '',
    license_number: driver.license_number,
    experience_years: String(driver.experience_years || '3'),
    status: driver.status || 'active',
    photo: null,
    license_photo: null,
  }
  photoPreview.value = driver.photo ? '/' + driver.photo : null
  licensePhotoPreview.value = driver.license_photo ? '/' + driver.license_photo : null
  showModal.value = true
}

const handlePhotoUpload = (e: any) => {
  if (e.target.files && e.target.files[0]) {
    form.value.photo = e.target.files[0]
    photoPreview.value = URL.createObjectURL(e.target.files[0])
  }
}

const handleLicenseUpload = (e: any) => {
  if (e.target.files && e.target.files[0]) {
    form.value.license_photo = e.target.files[0]
    licensePhotoPreview.value = URL.createObjectURL(e.target.files[0])
  }
}

const saveDriver = async () => {
  if (!form.value.name || !form.value.phone || !form.value.license_number) {
    errorMsg.value = 'Please fill in required fields: Name, Phone, and License Number.'
    
    return
  }

  submitting.value = true
  errorMsg.value = ''
  feedbackMsg.value = ''

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
      await axios.post(`/owner/drivers/${editingDriver.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      feedbackMsg.value = 'Driver details updated successfully.'
    } else {
      await axios.post('/owner/drivers', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      feedbackMsg.value = 'New driver registered successfully.'
    }
    showModal.value = false
    router.reload({ only: ['drivers'] })
  } catch (err: any) {
    errorMsg.value = err.response?.data?.message || err.response?.data?.errors?.name?.[0] || 'Error saving driver.'
  } finally {
    submitting.value = false
  }
}

const deleteDriver = async (id: number, name: string) => {
  if (!confirm(`Are you sure you want to remove driver "${name}"?`)) return

  try {
    await axios.delete(`/owner/drivers/${id}`)
    feedbackMsg.value = `Driver ${name} removed from roster.`
    router.reload({ only: ['drivers'] })
  } catch (err: any) {
    errorMsg.value = err.response?.data?.message || 'Error deleting driver.'
  }
}
</script>

<template>
  <OwnerLayout>
    <Head title="Owner - Driver Roster Management" />

    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
            Driver Roster & Staff Management
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Register professional chauffeurs, verify driving licenses, and assign them to your fleet cars.
          </p>
        </div>
        <button
          type="button"
          class="px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer self-start sm:self-auto"
          @click="openCreateModal"
        >
          <i class="ri-add-circle-line text-base" />
          <span>Add New Driver</span>
        </button>
      </div>

      <!-- Feedback Alerts -->
      <MessageBox
        v-model="feedbackMsg"
        type="success"
      />
      <MessageBox
        v-model="errorMsg"
        type="error"
      />

      <!-- Driver Cards List -->
      <div
        v-if="drivers && drivers.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <div
          v-for="driver in drivers"
          :key="driver.id"
          class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs hover:shadow-md transition-all flex flex-col justify-between space-y-4"
        >
          <div>
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-base overflow-hidden shrink-0">
                  <img
                    v-if="driver.photo"
                    :src="'/' + driver.photo"
                    class="w-full h-full object-cover"
                  >
                  <span v-else>{{ (driver.name || 'D')[0].toUpperCase() }}</span>
                </div>
                <div>
                  <h3 class="font-bold text-base text-slate-900 dark:text-white">
                    {{ driver.name }}
                  </h3>
                  <span class="text-[11px] text-slate-400 font-mono">
                    Lic: {{ driver.license_number }}
                  </span>
                </div>
              </div>
              <span
                :class="driver.status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shrink-0"
              >
                {{ driver.status }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
              <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="text-[10px] text-slate-400 block">Phone</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ driver.phone }}</span>
              </div>
              <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="text-[10px] text-slate-400 block">Experience</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ driver.experience_years }} Years</span>
              </div>
            </div>

            <div
              v-if="driver.email"
              class="mt-2 text-xs text-slate-500 truncate"
            >
              ✉️ {{ driver.email }}
            </div>

            <!-- Assigned Vehicles Count -->
            <div
              v-if="driver.cars && driver.cars.length > 0"
              class="mt-3 p-2.5 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 text-[11px] text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5"
            >
              <i class="ri-car-line text-xs" />
              <span>Assigned to <strong>{{ driver.cars.length }}</strong> vehicle(s)</span>
            </div>
          </div>

          <div class="flex items-center gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              class="flex-1 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all cursor-pointer"
              @click="openEditModal(driver)"
            >
              Edit Details
            </button>
            <button
              type="button"
              class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 text-xs font-bold transition-all cursor-pointer"
              @click="deleteDriver(driver.id, driver.name)"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <div
        v-else
        class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3"
      >
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-2xl mx-auto">
          <i class="ri-user-star-line" />
        </div>
        <h3 class="font-bold text-base text-slate-900 dark:text-white">
          No Drivers Registered Yet
        </h3>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">
          Add your trusted chauffeurs so you can allocate them to your rental vehicles.
        </p>
        <button
          type="button"
          class="mt-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
          @click="openCreateModal"
        >
          + Add First Driver
        </button>
      </div>
    </div>

    <!-- Modal for Create / Edit Driver -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/75 backdrop-blur-sm p-4 overflow-y-auto"
    >
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-5 relative">
        <button
          class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold cursor-pointer"
          @click="showModal = false"
        >
          &times;
        </button>

        <div>
          <h3 class="text-xl font-black text-slate-900 dark:text-white">
            {{ editingDriver ? 'Edit Driver Information' : 'Register New Driver' }}
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Chauffeur contact and verification details
          </p>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="saveDriver"
        >
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name *</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              placeholder="e.g. Ram Bahadur Thapa"
            >
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Phone Number *</label>
              <input
                v-model="form.phone"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                placeholder="+977 9801234567"
              >
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email (Optional)</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                placeholder="driver@gmail.com"
              >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">License Number *</label>
              <input
                v-model="form.license_number"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                placeholder="01-06-0098231"
              >
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Experience (Years)</label>
              <input
                v-model="form.experience_years"
                type="number"
                min="0"
                max="50"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status</label>
              <select
                v-model="form.status"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none"
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
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Driver Photo</label>
              <input
                type="file"
                class="w-full text-xs text-slate-500 file:me-2 file:py-1.5 file:px-2.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300"
                @change="handlePhotoUpload"
              >
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">License Photo</label>
              <input
                type="file"
                class="w-full text-xs text-slate-500 file:me-2 file:py-1.5 file:px-2.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300"
                @change="handleLicenseUpload"
              >
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button
              type="button"
              class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all cursor-pointer"
              @click="showModal = false"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/25 transition-all cursor-pointer"
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
