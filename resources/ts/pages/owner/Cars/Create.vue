<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'
import RichTextEditor from '@/components/RichTextEditor.vue'

const props = defineProps<{
  drivers?: Array<{ id: number; name: string; phone: string }>
}>()

const form = useForm({
  car_name: '',
  car_model: '',
  car_number: '',
  number_of_seats: 5,
  car_price_per_km: 15,
  car_price_per_day: 65,
  available: true,
  description: '',
  driver_id: '' as any,
  driver_name: '',
  driver_number: '',
  car_photo: null as File | null,
  blue_book_photo: null as File | null,
})

const photoPreview = ref<string | null>(null)
const bluebookPreview = ref<string | null>(null)

const onPhotoChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.car_photo = target.files[0]
    photoPreview.value = URL.createObjectURL(target.files[0])
  }
}

const onBluebookChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.blue_book_photo = target.files[0]
    bluebookPreview.value = URL.createObjectURL(target.files[0])
  }
}

const onDriverSelected = () => {
  if (!form.driver_id) {
    form.driver_name = ''
    form.driver_number = ''
    
    return
  }
  const selected = props.drivers?.find(d => d.id == form.driver_id)
  if (selected) {
    form.driver_name = selected.name
    form.driver_number = selected.phone
  }
}

const submit = () => {
  form.post('/owner/cars', {
    forceFormData: true,
  })
}
</script>

<template>
  <OwnerLayout>
    <Head title="Owner - Register New Car" />

    <div class="max-w-4xl mx-auto space-y-6 py-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
            Register New Vehicle
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
            Add a car to your fleet, set day & km pricing, and assign a dedicated chauffeur.
          </p>
        </div>
        <Link
          href="/owner/cars"
          class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors flex items-center gap-1.5"
        >
          <i class="ri-arrow-left-line" />
          <span>Back to Fleet</span>
        </Link>
      </div>

      <!-- Main Form -->
      <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form
          class="space-y-6"
          @submit.prevent="submit"
        >
          <!-- Vehicle Details -->
          <div>
            <h3 class="text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-3 flex items-center gap-1.5">
              <i class="ri-information-line" />
              <span>Vehicle Information</span>
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Car Brand / Make *</label>
                <input
                  v-model="form.car_name"
                  type="text"
                  required
                  placeholder="e.g. Hyundai, Toyota, Tesla"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
                >
                <span
                  v-if="form.errors.car_name"
                  class="text-xs text-rose-500 mt-1 block"
                >{{ form.errors.car_name }}</span>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Model *</label>
                <input
                  v-model="form.car_model"
                  type="text"
                  required
                  placeholder="e.g. Creta SX, Fortuner 4x4, Model Y"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
                >
                <span
                  v-if="form.errors.car_model"
                  class="text-xs text-rose-500 mt-1 block"
                >{{ form.errors.car_model }}</span>
              </div>
            </div>
          </div>

          <!-- Number Plate & Seat Capacity -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">License Plate / Registration No *</label>
              <input
                v-model="form.car_number"
                type="text"
                required
                placeholder="e.g. BA 1 PA 1234 or NY-8823"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
              >
              <span
                v-if="form.errors.car_number"
                class="text-xs text-rose-500 mt-1 block"
              >{{ form.errors.car_number }}</span>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Seating Capacity *</label>
              <input
                v-model.number="form.number_of_seats"
                type="number"
                min="1"
                max="60"
                required
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
              >
              <span
                v-if="form.errors.number_of_seats"
                class="text-xs text-rose-500 mt-1 block"
              >{{ form.errors.number_of_seats }}</span>
            </div>
          </div>

          <!-- Pricing -->
          <div>
            <h3 class="text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-3 flex items-center gap-1.5">
              <i class="ri-money-dollar-circle-line" />
              <span>Rental Pricing Rates</span>
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Price Per Day ($) *</label>
                <input
                  v-model.number="form.car_price_per_day"
                  type="number"
                  step="0.01"
                  required
                  placeholder="65.00"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
                >
                <span
                  v-if="form.errors.car_price_per_day"
                  class="text-xs text-rose-500 mt-1 block"
                >{{ form.errors.car_price_per_day }}</span>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Price Per Extra KM ($) (Optional)</label>
                <input
                  v-model.number="form.car_price_per_km"
                  type="number"
                  step="0.01"
                  placeholder="15.00"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all"
                >
                <span
                  v-if="form.errors.car_price_per_km"
                  class="text-xs text-rose-500 mt-1 block"
                >{{ form.errors.car_price_per_km }}</span>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Vehicle Description & Features (Rich Text)</label>
            <RichTextEditor
              v-model="form.description"
              placeholder="List vehicle features like Bluetooth, AC, Sunroof, safety ratings, luggage capacity..."
              min-height="160px"
            />
          </div>

          <!-- Driver Assignment Box -->
          <div class="p-5 rounded-3xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-emerald-800 dark:text-emerald-300">
                  Assign Driver from Your Roster
                </h4>
                <p class="text-[11px] text-slate-500">
                  Select a registered chauffeur from your driver team or assign later
                </p>
              </div>
              <Link
                href="/owner/drivers"
                class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1"
              >
                <span>Manage Drivers</span>
                <i class="ri-arrow-right-line" />
              </Link>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Select Roster Driver</label>
              <select
                v-model="form.driver_id"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                @change="onDriverSelected"
              >
                <option value="">
                  -- No Driver Assigned (Self-Drive / Assign Later) --
                </option>
                <option
                  v-for="d in drivers"
                  :key="d.id"
                  :value="d.id"
                >
                  {{ d.name }} &bull; {{ d.phone }}
                </option>
              </select>
            </div>

            <div
              v-if="form.driver_id"
              class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1"
            >
              <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Driver Name</label>
                <input
                  v-model="form.driver_name"
                  type="text"
                  class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                >
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Driver Phone Number</label>
                <input
                  v-model="form.driver_number"
                  type="text"
                  class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs"
                >
              </div>
            </div>
          </div>

          <!-- Document & Photo Uploads -->
          <div>
            <h3 class="text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-3 flex items-center gap-1.5">
              <i class="ri-image-add-line" />
              <span>Photos & Verification Documents</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Car Photo Upload -->
              <div class="p-4 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-center space-y-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Car Main Photo</label>
                <img
                  v-if="photoPreview"
                  :src="photoPreview"
                  class="w-full h-32 object-cover rounded-xl mx-auto"
                >
                <input
                  type="file"
                  accept="image/*"
                  class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300"
                  @change="onPhotoChange"
                >
                <span
                  v-if="form.errors.car_photo"
                  class="text-xs text-rose-500 block"
                >{{ form.errors.car_photo }}</span>
              </div>

              <!-- Blue Book Upload -->
              <div class="p-4 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-center space-y-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Blue Book / Registration Document</label>
                <img
                  v-if="bluebookPreview"
                  :src="bluebookPreview"
                  class="w-full h-32 object-cover rounded-xl mx-auto"
                >
                <input
                  type="file"
                  class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300"
                  @change="onBluebookChange"
                >
                <span
                  v-if="form.errors.blue_book_photo"
                  class="text-xs text-rose-500 block"
                >{{ form.errors.blue_book_photo }}</span>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
            <Link
              href="/owner/cars"
              class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-8 py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 disabled:opacity-50 transition-all flex items-center gap-2 cursor-pointer"
            >
              <i
                v-if="form.processing"
                class="ri-loader-4-line animate-spin text-sm"
              />
              <span>{{ form.processing ? 'Registering Vehicle...' : 'Register & Submit for Approval' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </OwnerLayout>
</template>
