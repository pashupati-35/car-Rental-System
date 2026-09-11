<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import OwnerLayout from '@/layouts/OwnerLayout.vue'

defineProps<{
  car: any
}>()
</script>

<template>
  <OwnerLayout>
    <Head :title="`${car.car_name} ${car.car_model} - Vehicle Details`" />

    <div class="max-w-5xl mx-auto space-y-6 py-4">
      <!-- Breadcrumb & Top Bar -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
          <Link
            href="/owner/cars"
            class="hover:text-emerald-600 font-semibold"
          >
            My Fleet
          </Link>
          <span>/</span>
          <span class="text-slate-900 dark:text-white font-bold">{{ car.car_name }} {{ car.car_model }}</span>
        </div>

        <div class="flex items-center gap-2">
          <Link
            :href="`/owner/cars/${car.id}/edit`"
            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5"
          >
            <i class="ri-edit-line" />
            <span>Edit Vehicle</span>
          </Link>
          <Link
            href="/owner/cars"
            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all"
          >
            &larr; Back
          </Link>
        </div>
      </div>

      <!-- Vehicle Overview Hero Card -->
      <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Photo Container -->
        <div class="space-y-4">
          <div class="h-64 sm:h-80 rounded-2xl bg-slate-100 dark:bg-slate-800 overflow-hidden relative shadow-inner">
            <img
              :src="car.car_photo ? '/' + car.car_photo : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&auto=format&fit=crop&q=80'"
              :alt="car.car_name"
              class="w-full h-full object-cover"
            >
            <div class="absolute top-3 left-3">
              <span
                v-if="car.status === 'approved' || car.status === 'active'"
                class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-md flex items-center gap-1"
              >
                <i class="ri-checkbox-circle-fill" /> Approved Listing
              </span>
              <span
                v-else-if="car.status === 'rejected'"
                class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500 text-white shadow-md flex items-center gap-1"
              >
                <i class="ri-close-circle-fill" /> Verification Rejected
              </span>
              <span
                v-else
                class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-md flex items-center gap-1"
              >
                <i class="ri-time-fill" /> Pending Admin Approval
              </span>
            </div>
          </div>

          <!-- Blue Book Document Link -->
          <div
            v-if="car.blue_book_photo"
            class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs"
          >
            <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300">
              <i class="ri-file-text-line text-lg text-emerald-600" />
              <span class="font-bold">Official Registration / Blue Book</span>
            </div>
            <a
              :href="'/' + car.blue_book_photo"
              target="_blank"
              rel="noopener noreferrer"
              class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline"
            >
              View Document &rarr;
            </a>
          </div>
        </div>

        <!-- Specs & Pricing Info -->
        <div class="space-y-6 flex flex-col justify-between">
          <div class="space-y-4">
            <div>
              <span class="text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                Vehicle Specifications
              </span>
              <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5">
                {{ car.car_name }} {{ car.car_model }}
              </h2>
              <p class="text-xs font-mono text-slate-400 mt-1">
                License Plate: <span class="font-bold text-slate-800 dark:text-slate-200">{{ car.car_number }}</span>
              </p>
            </div>

            <!-- Pricing Badges -->
            <div class="grid grid-cols-2 gap-3">
              <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/40">
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Daily Rate</span>
                <span class="text-2xl font-black text-emerald-800 dark:text-emerald-200">${{ car.car_price_per_day }}</span>
                <span class="text-xs text-emerald-600 dark:text-emerald-400"> / 24 hours</span>
              </div>

              <div class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-950/40 border border-teal-100 dark:border-teal-900/40">
                <span class="text-[11px] font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider block">Per KM Rate</span>
                <span class="text-2xl font-black text-teal-800 dark:text-teal-200">${{ car.car_price_per_km || 0 }}</span>
                <span class="text-xs text-teal-600 dark:text-teal-400"> / extra km</span>
              </div>
            </div>

            <!-- Key Specs Grid -->
            <div class="grid grid-cols-2 gap-3 text-xs">
              <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="text-slate-400 block text-[11px]">Seat Capacity</span>
                <span class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ car.number_of_seats }} Passengers</span>
              </div>

              <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                <span class="text-slate-400 block text-[11px]">Availability</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                  {{ car.available ? 'Ready for Booking' : 'Not Available' }}
                </span>
              </div>
            </div>

            <!-- Assigned Driver Profile Box -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2 text-xs">
              <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">
                Dedicated Chauffeur Allocation
              </span>
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                    {{ car.driver?.name || car.driver_name || 'No Chauffeur Assigned' }}
                  </h4>
                  <p
                    v-if="car.driver?.phone || car.driver_number"
                    class="text-xs text-slate-500 font-mono"
                  >
                    📞 {{ car.driver?.phone || car.driver_number }}
                  </p>
                </div>
                <Link
                  href="/owner/drivers"
                  class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline"
                >
                  Manage Roster &rarr;
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Description Section -->
      <div
        v-if="car.description"
        class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3"
      >
        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
          Description & Amenities
        </h3>
        <div
          class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 prose dark:prose-invert max-w-none"
          v-html="car.description"
        />
      </div>
    </div>
  </OwnerLayout>
</template>
