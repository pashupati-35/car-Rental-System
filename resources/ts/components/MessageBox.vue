<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, computed } from 'vue'

const props = withDefaults(
  defineProps<{
    modelValue?: string | boolean | null
    message?: string | null
    type?: 'success' | 'error' | 'warning' | 'info'
    duration?: number
    dismissible?: boolean
    showIcon?: boolean
  }>(),
  {
    modelValue: undefined,
    message: undefined,
    type: 'success',
    duration: 5000,
    dismissible: true,
    showIcon: true,
  },
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | boolean): void
  (e: 'close'): void
}>()

let timer: any = null

const isVisible = computed(() => {
  if (typeof props.modelValue === 'string') {
    return Boolean(props.modelValue.trim())
  }
  if (typeof props.modelValue === 'boolean') {
    return props.modelValue
  }
  if (typeof props.message === 'string') {
    return Boolean(props.message.trim())
  }
  return false
})

const displayMessage = computed(() => {
  if (typeof props.modelValue === 'string' && props.modelValue.trim()) {
    return props.modelValue
  }
  if (typeof props.message === 'string') {
    return props.message
  }
  return ''
})

const startTimer = () => {
  stopTimer()
  if (props.duration > 0 && isVisible.value) {
    timer = setTimeout(() => {
      close()
    }, props.duration)
  }
}

const stopTimer = () => {
  if (timer) {
    clearTimeout(timer)
    timer = null
  }
}

const close = () => {
  stopTimer()
  if (typeof props.modelValue === 'string') {
    emit('update:modelValue', '')
  } else if (typeof props.modelValue === 'boolean') {
    emit('update:modelValue', false)
  }
  emit('close')
}

watch(
  () => [props.modelValue, props.message],
  () => {
    if (isVisible.value) {
      startTimer()
    } else {
      stopTimer()
    }
  },
  { immediate: true, deep: true },
)

onMounted(() => {
  if (isVisible.value) {
    startTimer()
  }
})

onUnmounted(() => {
  stopTimer()
})

const typeConfig = computed(() => {
  switch (props.type) {
    case 'error':
      return {
        wrapper: 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200',
        icon: 'ri-error-warning-fill text-rose-600 dark:text-rose-400',
        closeBtn: 'text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40',
      }
    case 'warning':
      return {
        wrapper: 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-200',
        icon: 'ri-alert-fill text-amber-600 dark:text-amber-400',
        closeBtn: 'text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40',
      }
    case 'info':
      return {
        wrapper: 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200 dark:border-indigo-800/60 text-indigo-800 dark:text-indigo-200',
        icon: 'ri-information-fill text-indigo-600 dark:text-indigo-400',
        closeBtn: 'text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40',
      }
    case 'success':
    default:
      return {
        wrapper: 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-200',
        icon: 'ri-checkbox-circle-fill text-emerald-600 dark:text-emerald-400',
        closeBtn: 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40',
      }
  }
})
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 -translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-2"
  >
    <div
      v-if="isVisible"
      :class="[typeConfig.wrapper]"
      class="p-4 rounded-2xl border text-xs font-semibold flex items-center justify-between gap-3 shadow-xs transition-all"
      role="alert"
    >
      <div class="flex items-center gap-2.5 min-w-0">
        <i
          v-if="showIcon"
          :class="[typeConfig.icon]"
          class="text-base shrink-0"
        />
        <div class="leading-relaxed break-words">
          <slot>{{ displayMessage }}</slot>
        </div>
      </div>

      <button
        v-if="dismissible"
        type="button"
        :class="[typeConfig.closeBtn]"
        class="p-1 rounded-lg cursor-pointer transition-colors shrink-0 focus:outline-none"
        title="Close message"
        aria-label="Close"
        @click="close"
      >
        <i class="ri-close-line text-base leading-none block" />
      </button>
    </div>
  </Transition>
</template>
