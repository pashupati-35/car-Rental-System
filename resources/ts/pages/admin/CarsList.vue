<script setup lang="ts">
import CarsIndex from './cars/Index.vue'

const props = defineProps<{
  cars: any
  counts?: Record<string, number>
  owners?: Array<any>
  drivers?: Array<any>
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
    :owners="props.owners"
    :drivers="props.drivers"
    :filters="props.filters"
  />
</template>
