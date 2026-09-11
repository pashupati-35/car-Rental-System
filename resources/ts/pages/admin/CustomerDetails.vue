<script setup lang="ts">
import CustomerShow from './customers/Show.vue'
import type { CustomerItem, CustomerStats, CustomerBookingItem, CustomerPaymentItem } from './customers/types'

const props = defineProps<{
  customer: CustomerItem
  bookings: CustomerBookingItem[]
  payments: CustomerPaymentItem[]
  availableCars: Array<any>
  stats: CustomerStats
}>()
</script>

<template>
  <CustomerShow
    :customer="props.customer"
    :bookings="props.bookings"
    :payments="props.payments"
    :available-cars="props.availableCars"
    :stats="props.stats"
  />
</template>
