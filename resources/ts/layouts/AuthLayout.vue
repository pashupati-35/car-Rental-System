<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useSiteSettings } from '@/composable/useSiteSettings'

const page = usePage()
const { logoUrl, companyName } = useSiteSettings()
const isPortal = computed(() => Boolean(page.props.isPortal))
const mainAppUrl = computed(() => {
  const url = (page.props.mainAppUrl as string) || ''
  if (url) return url
  if (typeof window !== 'undefined') {
    const host = window.location.host.replace(/^portal\./, '')
    return `${window.location.protocol}//${host}`
  }
  return ''
})

const homeUrl = computed(() => isPortal.value ? (mainAppUrl.value || '/') : '/')
const browseUrl = computed(() => isPortal.value ? `${mainAppUrl.value}/cars` : '/cars')
</script>

<template>
  <div class="min-h-screen relative flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 via-blue-50/20 to-slate-100 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
    <!-- Top-Left Floating Home Navigation -->
    <div class="absolute top-6 left-6 z-20">
      <a
        v-if="isPortal"
        :href="homeUrl"
        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border border-gray-200/80 dark:border-gray-800 text-xs font-bold text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 dark:hover:border-blue-700 shadow-sm transition-all hover:scale-105"
      >
        <svg
          class="w-4 h-4 text-blue-600 dark:text-blue-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
          />
        </svg>
        <span>Back to Live Site</span>
      </a>
      <Link
        v-else
        href="/"
        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border border-gray-200/80 dark:border-gray-800 text-xs font-bold text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-300 dark:hover:border-blue-700 shadow-sm transition-all hover:scale-105"
      >
        <svg
          class="w-4 h-4 text-blue-600 dark:text-blue-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
          />
        </svg>
        <span>Back to Home</span>
      </Link>
    </div>

    <!-- Top-Right Live Site / Browse Fleet Shortcut (Optional convenience) -->
    <div class="absolute top-6 right-6 z-20 hidden sm:block">
      <a
        v-if="isPortal"
        :href="browseUrl"
        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border border-gray-200/80 dark:border-gray-800 text-xs font-bold text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 shadow-sm transition-all"
      >
        <span>Browse Fleet</span>
        <svg
          class="w-3.5 h-3.5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5l7 7-7 7"
          />
        </svg>
      </a>
      <Link
        v-else
        href="/cars"
        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border border-gray-200/80 dark:border-gray-800 text-xs font-bold text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 shadow-sm transition-all"
      >
        <span>Browse Fleet</span>
        <svg
          class="w-3.5 h-3.5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5l7 7-7 7"
          />
        </svg>
      </Link>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-8">
      <a
        v-if="isPortal"
        :href="homeUrl"
        class="inline-flex items-center gap-3 group"
      >
        <img
          v-if="logoUrl"
          :src="logoUrl"
          :alt="companyName"
          class="h-12 max-w-[200px] object-contain group-hover:scale-105 transition-transform"
        />
        <div
          v-else
          class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-blue-500/30 group-hover:scale-105 transition-transform"
        >
          CR
        </div>
      </a>
      <Link
        v-else
        href="/"
        class="inline-flex items-center gap-3 group"
      >
        <img
          v-if="logoUrl"
          :src="logoUrl"
          :alt="companyName"
          class="h-12 max-w-[200px] object-contain group-hover:scale-105 transition-transform"
        />
        <div
          v-else
          class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-blue-500/30 group-hover:scale-105 transition-transform"
        >
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
          {{ companyName }} Fleet Management System
        </slot>
      </p>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white dark:bg-gray-900 py-8 px-6 shadow-xl shadow-slate-200/50 dark:shadow-none sm:rounded-2xl sm:px-10 border border-gray-100 dark:border-gray-800">
        <slot />
      </div>

      <!-- Bottom Return to Home Link -->
      <div class="mt-6 text-center">
        <a
          v-if="isPortal"
          :href="homeUrl"
          class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
        >
          <svg
            class="w-3.5 h-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          <span>Return to Live Site</span>
        </a>
        <Link
          v-else
          href="/"
          class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
        >
          <svg
            class="w-3.5 h-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          <span>Return to Homepage</span>
        </Link>
      </div>
    </div>
  </div>
</template>
