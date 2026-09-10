<script setup lang="ts">
import CustomersIndex from './customers/Index.vue'

const props = defineProps<{
  customers?: any
  filters?: {
    search?: string
    per_page?: number
  }
}>()
</script>

<template>
  <CustomersIndex :customers="props.customers" :filters="props.filters" />
</template>
