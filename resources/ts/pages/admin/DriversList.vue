<script setup lang="ts">
import DriversIndex from './drivers/Index.vue'

const props = defineProps<{
  drivers: any
  owners?: Array<any>
  filters?: {
    search?: string
    per_page?: number
  }
}>()
</script>

<template>
  <DriversIndex :drivers="props.drivers" :owners="props.owners" :filters="props.filters" />
</template>
