<script setup lang="ts">
import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

onMounted(() => {
  // Directly route to Customer Login
  router.visit('/customer/login', { replace: true })
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <div class="w-4 h-4 rounded-full border-2 border-blue-600 border-t-transparent animate-spin" />
      <span>Loading sign in portal...</span>
    </div>
  </div>
</template>
