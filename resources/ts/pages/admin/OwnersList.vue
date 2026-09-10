<script setup lang="ts">
import OwnersIndex from './owners/Index.vue'

const props = defineProps<{
  owners?: any
  filters?: {
    search?: string
    per_page?: number
  }
}>()
</script>

<template>
  <OwnersIndex :owners="props.owners" :filters="props.filters" />
</template>
