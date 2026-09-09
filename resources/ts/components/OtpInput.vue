<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    code: string
    length?: number
    loading?: boolean
    error?: [] | null
  }>(),
  { length: 6, loading: false, error: null },
)

const emit = defineEmits<{
  (e: "update:code", v: string): void
  (e: "submit"): void
}>()

const code = computed({
  get: () => props.code,
  set: (v: string) => emit("update:code", v.replace(/\D/g, "").slice(0, props.length)),
})

function submit() {
  if (!props.loading && code.value.length === props.length) emit("submit")
}
</script>

<template>
  <div>
    <VOtpInput
      v-model="code"
      :length="length"
      inputmode="numeric"
      autocomplete="one-time-code"
      variant="outlined"
      :disabled="loading"
      :error-message="error"
      @keyup.enter="submit"
    />
  </div>
</template>
