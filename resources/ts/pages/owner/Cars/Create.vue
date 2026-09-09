<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const form = useForm({
  brand: '',
  model: '',
  year: new Date().getFullYear(),
  car_number: '',
  price_per_day: 50,
  transmission: 'Automatic',
  fuel_type: 'Petrol',
  seats: 5,
  description: '',
  image: null as File | null,
})

const submit = () => {
  form.post('/owner/cars', {
    forceFormData: true,
  })
}
</script>

<template>
  <AppLayout>
    <Head title="Add New Car" />

    <template #header>
      Register New Vehicle
    </template>

    <div class="max-w-3xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-xl text-gray-900 dark:text-white">List Your Vehicle</h3>
          <p class="text-xs text-gray-500 mt-0.5">Provide vehicle specifications, pricing, and photos</p>
        </div>
        <Link
          href="/owner/cars"
          class="px-3.5 py-2 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        >
          &larr; Back to Cars
        </Link>
      </div>

      <div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form @submit.prevent="submit" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand / Make</label>
              <input
                v-model="form.brand"
                type="text"
                required
                placeholder="e.g. Toyota"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model Name</label>
              <input
                v-model="form.model"
                type="text"
                required
                placeholder="e.g. Camry, RAV4"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">License Plate / Car Number</label>
              <input
                v-model="form.car_number"
                type="text"
                required
                placeholder="BA-1-PA-1234"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manufacturing Year</label>
              <input
                v-model="form.year"
                type="number"
                required
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price Per Day ($)</label>
              <input
                v-model="form.price_per_day"
                type="number"
                required
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transmission</label>
              <select
                v-model="form.transmission"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
              >
                <option value="Automatic">Automatic</option>
                <option value="Manual">Manual</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fuel Type</label>
              <select
                v-model="form.fuel_type"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
              >
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
                <option value="Hybrid">Hybrid</option>
                <option value="Electric">Electric</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Seat Capacity</label>
              <input
                v-model="form.seats"
                type="number"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vehicle Photo</label>
            <input
              type="file"
              @input="form.image = ($event.target as HTMLInputElement).files?.[0] || null"
              accept="image/*"
              class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description / Features</label>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
              placeholder="Key vehicle features (Bluetooth, GPS, Leather seats...)"
            ></textarea>
          </div>

          <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
            <button
              type="submit"
              :disabled="form.processing"
              class="py-2.5 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 disabled:opacity-50 transition-all"
            >
              List Car Now
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
