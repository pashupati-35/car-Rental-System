<script setup lang="ts">
import CarsIndex from './cars/Index.vue'

const props = defineProps<{
  cars: any
  filters?: {
    status?: string
    search?: string
    per_page?: number
  }
}>()
</script>

<template>
  <CarsIndex
    :cars="props.cars"
    :filters="props.filters"
  />
</template>
