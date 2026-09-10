<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    modelValue?: boolean | number | string | null
    label?: string
    description?: string
    activeText?: string
    inactiveText?: string
    disabled?: boolean
    size?: 'sm' | 'md' | 'lg'
  }>(),
  {
    modelValue: false,
    label: '',
    description: '',
    activeText: 'Active',
    inactiveText: 'Inactive',
    disabled: false,
    size: 'md',
  },
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | boolean): void
}>()

const isChecked = computed(() => {
  if (typeof props.modelValue === 'number') {
    return props.modelValue === 1
  }
  if (typeof props.modelValue === 'string') {
    return props.modelValue === '1' || props.modelValue === 'true'
  }
  
  return Boolean(props.modelValue)
})

const toggle = () => {
  if (props.disabled) return
  const nextVal = !isChecked.value

  // If original was number (e.g. 1/0 for MySQL boolean), emit number, else boolean
  if (typeof props.modelValue === 'number') {
    emit('update:modelValue', nextVal ? 1 : 0)
  } else {
    emit('update:modelValue', nextVal)
  }
}
</script>

<template>
  <div
    class="flex items-center justify-between gap-3 p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 transition-colors"
    :class="{ 'opacity-60 cursor-not-allowed': disabled, 'cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/60': !disabled }"
    @click="toggle"
  >
    <div class="flex-1 select-none">
      <div class="flex items-center gap-2">
        <span
          v-if="label"
          class="font-bold text-xs text-slate-800 dark:text-slate-200"
        >{{ label }}</span>
        <span
          class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors"
          :class="isChecked ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-200/80 dark:bg-slate-700 text-slate-600 dark:text-slate-400'"
        >
          {{ isChecked ? activeText : inactiveText }}
        </span>
      </div>
      <p
        v-if="description"
        class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5"
      >
        {{ description }}
      </p>
    </div>

    <!-- Toggle Pill -->
    <button
      type="button"
      role="switch"
      :aria-checked="isChecked"
      :disabled="disabled"
      class="relative inline-flex flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
      :class="[
        size === 'sm' ? 'h-5 w-9' : size === 'lg' ? 'h-7 w-13' : 'h-6 w-11',
        isChecked ? 'bg-indigo-600 dark:bg-indigo-500' : 'bg-slate-300 dark:bg-slate-700'
      ]"
      @click.stop="toggle"
    >
      <span
        aria-hidden="true"
        class="pointer-events-none inline-block transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"
        :class="[
          size === 'sm' ? 'h-4 w-4 my-0.5' : size === 'lg' ? 'h-6 w-6 my-0.5' : 'h-5 w-5 my-0.5',
          isChecked
            ? (size === 'sm' ? 'translate-x-4.5 ms-0.5' : size === 'lg' ? 'translate-x-6.5 ms-0.5' : 'translate-x-5.5 ms-0.5')
            : 'translate-x-0.5'
        ]"
      />
    </button>
  </div>
</template>
