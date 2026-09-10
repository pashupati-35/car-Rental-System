<script setup lang="ts">
import { ref, watch } from 'vue'
import type { DriverItem } from '../types'

const props = defineProps<{
  show: boolean
  isEditing: boolean
  driver?: DriverItem | null
  owners: Array<any>
  submitting: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', formData: FormData): void
}>()

const form = ref<{
  name: string
  phone: string
  email: string
  license_number: string
  experience_years: number | string
  status: string
  owner_id: number | string
  address: string
}>({
  name: '',
  phone: '',
  email: '',
  license_number: '',
  experience_years: 1,
  status: 'active',
  owner_id: '',
  address: '',
})

const photoFile = ref<File | null>(null)
const photoPreview = ref<string | null>(null)
const licensePhotoFile = ref<File | null>(null)
const licensePhotoPreview = ref<string | null>(null)

const resolveImageUrl = (img?: string | null, imagePath?: any) => {
  if (imagePath?.original) return imagePath.original
  if (img) {
    return img.startsWith('http') ? img : `/${img.replace(/^\/+/, '')}`
  }
  return null
}

watch(
  () => props.driver,
  (newDriver: DriverItem | null | undefined) => {
    if (newDriver && props.isEditing) {
      form.value = {
        name: newDriver.name || '',
        phone: newDriver.phone || '',
        email: newDriver.email || '',
        license_number: newDriver.license_number || '',
        experience_years: newDriver.experience_years ?? 1,
        status: newDriver.status || 'active',
        owner_id: newDriver.owner_id || newDriver.owner?.id || '',
        address: newDriver.address || '',
      }
      photoPreview.value = resolveImageUrl(newDriver.image || newDriver.photo, newDriver.image_path)
      licensePhotoPreview.value = resolveImageUrl(newDriver.license_photo_url || newDriver.license_photo)
    } else {
      form.value = {
        name: '',
        phone: '',
        email: '',
        license_number: '',
        experience_years: 1,
        status: 'active',
        owner_id: '',
        address: '',
      }
      photoPreview.value = null
      licensePhotoPreview.value = null
    }
    photoFile.value = null
    licensePhotoFile.value = null
  },
  { immediate: true },
)

const handlePhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    photoFile.value = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

const handleLicensePhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    licensePhotoFile.value = file
    licensePhotoPreview.value = URL.createObjectURL(file)
  }
}

const handleSubmit = () => {
  const data = new FormData()
  data.append('name', form.value.name)
  data.append('phone', form.value.phone)
  data.append('email', form.value.email || '')
  data.append('license_number', form.value.license_number)
  data.append('experience_years', String(form.value.experience_years || 1))
  data.append('status', form.value.status || 'active')
  if (form.value.owner_id) {
    data.append('owner_id', String(form.value.owner_id))
  }
  data.append('address', form.value.address || '')

  if (photoFile.value) {
    data.append('photo', photoFile.value)
  }

  if (licensePhotoFile.value) {
    data.append('license_photo', licensePhotoFile.value)
  }

  emit('save', data)
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
  >
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl relative space-y-4 my-8 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
          <i class="ri-steering-2-line text-indigo-600" />
          <span>{{ isEditing ? 'Edit Chauffeur / Driver Profile' : 'Register New Chauffeur / Driver' }}</span>
        </h3>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer"
          @click="emit('close')"
        >
          &times;
        </button>
      </div>

      <div
        v-if="errorMessage"
        class="p-3 rounded-2xl bg-rose-50 text-rose-800 text-xs font-semibold"
      >
        {{ errorMessage }}
      </div>

      <form
        class="space-y-4 text-xs"
        @submit.prevent="handleSubmit"
      >
        <div>
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Driver Full Name *</label>
          <input
            v-model="form.name"
            type="text"
            required
            placeholder="e.g. Samuel Rodriguez"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
          >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Phone Number *</label>
            <input
              v-model="form.phone"
              type="text"
              required
              placeholder="+1 (555) 456-7890"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Email Address (Optional)</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="driver@example.com"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Commercial License # *</label>
            <input
              v-model="form.license_number"
              type="text"
              required
              placeholder="DL-928172648"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-mono"
            >
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Years of Experience</label>
            <input
              v-model="form.experience_years"
              type="number"
              min="0"
              placeholder="3"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Fleet Owner Affiliation</label>
            <select
              v-model="form.owner_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
            >
              <option value="">
                Independent / Platform Driver
              </option>
              <option
                v-for="owner in owners"
                :key="owner.id"
                :value="owner.id"
              >
                {{ owner.full_name || owner.name }} (ID: {{ owner.id }})
              </option>
            </select>
          </div>
          <div>
            <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Status</label>
            <select
              v-model="form.status"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold"
            >
              <option value="active">
                Active & Available
              </option>
              <option value="inactive">
                Inactive / On Leave
              </option>
            </select>
          </div>
        </div>

        <div>
          <label class="block font-bold mb-1 text-slate-700 dark:text-slate-300">Residential Address</label>
          <input
            v-model="form.address"
            type="text"
            placeholder="San Diego, CA"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
          >
        </div>

        <!-- Driver Photo & License Document -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">Driver Profile Photo</label>
            <div class="flex items-center gap-3">
              <div
                v-if="photoPreview"
                class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0"
              >
                <img
                  :src="photoPreview"
                  class="w-full h-full object-cover"
                >
              </div>
              <div
                v-else
                class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 shrink-0"
              >
                <i class="ri-user-line text-lg" />
              </div>
              <input
                type="file"
                class="text-xs text-slate-500 file:me-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                @change="handlePhotoChange"
              >
            </div>
          </div>

          <div>
            <label class="block font-bold mb-1.5 text-slate-700 dark:text-slate-300">License Document Photo</label>
            <div class="flex items-center gap-3">
              <div
                v-if="licensePhotoPreview"
                class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0"
              >
                <img
                  :src="licensePhotoPreview"
                  class="w-full h-full object-cover"
                >
              </div>
              <div
                v-else
                class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 shrink-0"
              >
                <i class="ri-file-text-line text-lg" />
              </div>
              <input
                type="file"
                class="text-xs text-slate-500 file:me-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                @change="handleLicensePhotoChange"
              >
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer transition-colors"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 disabled:opacity-50 cursor-pointer transition-colors"
          >
            {{ submitting ? 'Saving...' : (isEditing ? 'Save Changes' : 'Register Driver') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
