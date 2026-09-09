<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 via-blue-50/20 to-slate-100 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-8">
      <Link
        href="/"
        class="inline-flex items-center gap-3"
      >
        <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-blue-500/30">
          CR
        </div>
      </Link>
      <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
        <slot name="title">
          Welcome Back
        </slot>
      </h2>
      <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        <slot name="subtitle">
          Car Rental & Fleet Management System
        </slot>
      </p>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white dark:bg-gray-900 py-8 px-6 shadow-xl shadow-slate-200/50 dark:shadow-none sm:rounded-2xl sm:px-10 border border-gray-100 dark:border-gray-800">
        <slot />
      </div>
    </div>
  </div>
</template>
