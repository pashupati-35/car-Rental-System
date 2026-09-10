<script setup lang="ts">
import CarsIndex from './cars/Index.vue'

const props = defineProps<{
  cars: any
  counts?: Record<string, number>
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
    :counts="props.counts"
    :filters="props.filters"
  />
</template>
