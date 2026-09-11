<script setup lang="ts">
import DriversIndex from './drivers/Index.vue'

const props = defineProps<{
  drivers: any
  owners?: Array<any>
  counts?: {
    all?: number
    active?: number
    inactive?: number
  }
  filters?: {
    search?: string
    owner_id?: string
    status?: string
    per_page?: number
  }
}>()
</script>

<template>
  <DriversIndex
    :drivers="props.drivers"
    :owners="props.owners"
    :counts="props.counts"
    :filters="props.filters"
  />
</template>
