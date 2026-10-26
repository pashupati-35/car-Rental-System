<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

defineProps<{
  booking: any
}>()
</script>

<template>
  <FrontendLayout>
    <Head title="Payment Confirmation" />

    <div class="max-w-xl mx-auto px-4 sm:px-6 py-16 sm:py-24 text-center">
      <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200/80 dark:border-gray-800 shadow-xl p-8 sm:p-10 space-y-6">
        <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mx-auto shadow-md shadow-emerald-500/10">
          <i class="ri-checkbox-circle-fill" />
        </div>

        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            Payment Confirmation
          </h1>
          <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
            Your payment for booking ID <span class="font-mono font-bold text-gray-900 dark:text-white">#{{ booking?.id }}</span> was completed successfully!
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/60 text-xs text-left space-y-2">
          <div class="flex justify-between">
            <span class="text-gray-400">Reserved Vehicle:</span>
            <span class="font-semibold text-gray-900 dark:text-white">
              {{ booking?.car?.car_name }} {{ booking?.car?.car_model }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400">Total Paid:</span>
            <span class="font-bold text-emerald-600 dark:text-emerald-400">
              ${{ booking?.total_price }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400">Status:</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500 text-white">
              {{ booking?.status || 'Confirmed' }}
            </span>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
          <Link
            href="/customer/dashboard"
            class="w-full sm:w-auto px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer"
          >
            <i class="ri-dashboard-line" />
            <span>Go to Customer Dashboard</span>
          </Link>

          <Link
            href="/cars"
            class="w-full sm:w-auto px-6 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-semibold text-xs text-center hover:bg-gray-50 dark:hover:bg-gray-800 transition-all"
          >
            Browse More Fleet Cars
          </Link>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>
