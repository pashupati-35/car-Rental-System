<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'

const portals = [
  {
    title: 'Customer Portal',
    description: 'Book verified cars, view rental dates, manage bookings and download PDF invoices.',
    href: '/customer/login',
    badge: 'Renters',
    color: 'from-blue-600 to-indigo-600',
    iconBg: 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400',
    border: 'hover:border-blue-500/50',
  },
  {
    title: 'Car Owner Portal',
    description: 'List cars, manage fleet availability, register drivers, and track booking revenue.',
    href: '/owner/login',
    badge: 'Fleet Owners',
    color: 'from-emerald-600 to-teal-600',
    iconBg: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400',
    border: 'hover:border-emerald-500/50',
  },
  {
    title: 'Admin Portal',
    description: 'System administration, car approval/rejections, driver oversight, and analytics.',
    href: '/admin/login',
    badge: 'System Admin',
    color: 'from-indigo-600 to-purple-600',
    iconBg: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400',
    border: 'hover:border-indigo-500/50',
  },
]
</script>

<template>
  <AuthLayout>
    <Head title="Select Sign In Portal" />

    <template #title>
      Choose Your Portal
    </template>
    <template #subtitle>
      Select your dedicated login gateway below to continue
    </template>

    <div class="space-y-4">
      <Link
        v-for="portal in portals"
        :key="portal.title"
        :href="portal.href"
        class="block p-4 rounded-2xl border border-gray-200 dark:border-gray-800 hover:shadow-lg transition-all duration-200 bg-white dark:bg-gray-800/80 group"
        :class="portal.border"
      >
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            {{ portal.badge }}
          </span>
          <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
            Access &rarr;
          </span>
        </div>
        <h4 class="font-bold text-gray-900 dark:text-white text-base group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
          {{ portal.title }}
        </h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
          {{ portal.description }}
        </p>
      </Link>

      <div class="pt-2 text-center">
        <Link href="/" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
          &larr; Return to Public Car Showroom
        </Link>
      </div>
    </div>
  </AuthLayout>
</template>
