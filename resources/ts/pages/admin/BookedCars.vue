<script setup lang="ts">
import BookingsIndex from './bookings/Index.vue'

const props = defineProps<{
  bookedCars: any
  counts?: Record<string, number>
  filters?: {
    status?: string
    search?: string
    per_page?: number
  }
}>()
</script>

<template>
  <BookingsIndex
    :booked-cars="props.bookedCars"
    :counts="props.counts"
    :filters="props.filters"
  />
</template>
