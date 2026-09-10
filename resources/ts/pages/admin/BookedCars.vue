<script setup lang="ts">
import BookingsIndex from './bookings/Index.vue'

const props = defineProps<{
  bookedCars: any
  filters?: {
    status?: string
    search?: string
    per_page?: number
  }
}>()
</script>

<template>
  <BookingsIndex :booked-cars="props.bookedCars" :filters="props.filters" />
</template>
