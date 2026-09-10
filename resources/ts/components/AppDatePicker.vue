<script setup lang="ts">
import { ref, computed } from 'vue'
import FlatPickr from 'vue-flatpickr-component'

const props = withDefaults(
  defineProps<{
    modelValue?: string | null
    type?: 'date' | 'datetime-local' | 'time'
    label?: string
    placeholder?: string
    disabled?: boolean
    readonly?: boolean
    clearable?: boolean
    minDate?: string
    maxDate?: string
    error?: string
    config?: Record<string, any>
  }>(),
  {
    modelValue: '',
    type: 'date',
    label: '',
    placeholder: 'Select date...',
    disabled: false,
    readonly: false,
    clearable: true,
    error: '',
    config: () => ({}),
  },
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'change', value: string): void
  (e: 'blur'): void
}>()

const inputContainerRef = ref<HTMLElement | null>(null)
const flatpickrRef = ref<any>(null)
const isOpen = ref(false)

const hasTime = computed(() => props.type === 'datetime-local' || props.type === 'time')

const flatpickrConfig = computed(() => ({
  enableTime: hasTime.value,
  noCalendar: props.type === 'time',
  dateFormat: props.type === 'datetime-local' ? 'Y-m-d H:i' : props.type === 'time' ? 'H:i' : 'Y-m-d',
  altInput: true,
  altFormat: props.type === 'datetime-local' ? 'M j, Y h:i K' : props.type === 'time' ? 'h:i K' : 'M j, Y',
  altInputClass: 'app-date-input-styled',
  minDate: props.minDate,
  maxDate: props.maxDate,
  prevArrow: '<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>',
  nextArrow: '<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
  ...props.config,
}))

const handleInput = (val: string) => {
  emit('update:modelValue', val)
  emit('change', val)
}

const clearValue = () => {
  emit('update:modelValue', '')
  emit('change', '')
}
</script>

<template>
  <div
    ref="inputContainerRef"
    class="app-date-picker-container w-full"
  >
    <!-- Label -->
    <label
      v-if="label"
      class="block font-bold mb-1 text-slate-700 dark:text-slate-300 text-xs flex items-center justify-between"
    >
      <span>{{ label }}</span>
      <button
        v-if="clearable && modelValue && !disabled && !readonly"
        type="button"
        class="text-[10px] text-slate-400 hover:text-rose-500 transition-colors font-medium cursor-pointer"
        @click.stop="clearValue"
      >
        Clear
      </button>
    </label>

    <!-- Input Wrapper -->
    <div
      class="relative flex items-center rounded-xl border transition-all duration-150"
      :class="[
        error
          ? 'border-rose-300 dark:border-rose-700 bg-rose-50/30'
          : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20',
        disabled ? 'opacity-60 cursor-not-allowed bg-slate-100 dark:bg-slate-900' : ''
      ]"
    >
      <!-- Calendar Icon Prefix -->
      <div
        class="text-slate-400 dark:text-slate-500 pointer-events-none flex items-center"
        style="padding-left: 0.875rem; padding-right: 0.375rem"
      >
        <i class="ri-calendar-line text-sm" />
      </div>

      <!-- Flatpickr Core Component -->
      <FlatPickr
        ref="flatpickrRef"
        :model-value="modelValue || ''"
        :config="flatpickrConfig"
        :placeholder="placeholder"
        :disabled="disabled"
        class="w-full bg-transparent text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none py-2.5 font-medium cursor-pointer"
        style="padding-right: 0.75rem"
        @update:model-value="handleInput"
        @on-open="isOpen = true"
        @on-close="isOpen = false"
      />
    </div>

    <!-- Error message if any -->
    <p
      v-if="error"
      class="text-[11px] text-rose-500 mt-1 font-medium"
    >
      {{ error }}
    </p>
  </div>
</template>

<style>
@import "flatpickr/dist/flatpickr.css";

/* Custom Date Picker Dropdown theme matching screenshot */
.flatpickr-calendar {
  background: #ffffff !important;
  border-radius: 1rem !important;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
  border: 1px solid #e2e8f0 !important;
  padding: 0.75rem !important;
  width: 300px !important;
  font-family: inherit !important;
  z-index: 99999 !important;
}

.dark .flatpickr-calendar {
  background: #0f172a !important;
  border-color: #334155 !important;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
}

.flatpickr-calendar::before,
.flatpickr-calendar::after {
  display: none !important;
}

.flatpickr-months {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding-bottom: 0.5rem !important;
  border-bottom: 1px solid #f1f5f9 !important;
}

.dark .flatpickr-months {
  border-bottom-color: #1e293b !important;
}

.flatpickr-months .flatpickr-month {
  height: 36px !important;
  color: #0f172a !important;
}

.dark .flatpickr-months .flatpickr-month {
  color: #f8fafc !important;
}

.flatpickr-current-month {
  font-size: 0.9375rem !important;
  font-weight: 700 !important;
  padding: 0 !important;
}

.flatpickr-current-month .flatpickr-monthDropdown-months {
  font-weight: 700 !important;
  color: #1e293b !important;
  appearance: none !important;
}

.dark .flatpickr-current-month .flatpickr-monthDropdown-months {
  color: #f1f5f9 !important;
  background: #0f172a !important;
}

.flatpickr-current-month input.cur-year {
  font-weight: 700 !important;
  color: #1e293b !important;
}

.dark .flatpickr-current-month input.cur-year {
  color: #f1f5f9 !important;
}

.flatpickr-prev-month,
.flatpickr-next-month {
  padding: 6px !important;
  border-radius: 0.5rem !important;
  transition: all 0.15s !important;
}

.flatpickr-prev-month:hover,
.flatpickr-next-month:hover {
  background: #f1f5f9 !important;
}

.dark .flatpickr-prev-month:hover,
.dark .flatpickr-next-month:hover {
  background: #1e293b !important;
}

.flatpickr-weekdays {
  margin: 0.5rem 0 !important;
}

span.flatpickr-weekday {
  color: #64748b !important;
  font-size: 0.6875rem !important;
  font-weight: 700 !important;
  letter-spacing: 0.05em !important;
}

.dark span.flatpickr-weekday {
  color: #94a3b8 !important;
}

.flatpickr-days {
  width: 100% !important;
}

.dayContainer {
  width: 100% !important;
  min-width: 100% !important;
  max-width: 100% !important;
  justify-content: space-around !important;
}

.flatpickr-day {
  border-radius: 0.5rem !important;
  font-size: 0.8125rem !important;
  font-weight: 600 !important;
  color: #334155 !important;
  height: 36px !important;
  line-height: 36px !important;
  max-width: 36px !important;
  border: 1px solid transparent !important;
  margin: 2px 0 !important;
  transition: all 0.15s !important;
}

.dark .flatpickr-day {
  color: #cbd5e1 !important;
}

.flatpickr-day:hover {
  background: #f1f5f9 !important;
  border-color: #cbd5e1 !important;
}

.dark .flatpickr-day:hover {
  background: #1e293b !important;
  border-color: #475569 !important;
}

.flatpickr-day.today {
  border: 2px solid #0f766e !important;
  border-radius: 0.5rem !important;
  background: transparent !important;
  color: #0f766e !important;
  font-weight: 700 !important;
}

.dark .flatpickr-day.today {
  border-color: #2dd4bf !important;
  color: #2dd4bf !important;
}

.flatpickr-day.selected,
.flatpickr-day.selected:hover {
  background: #0f766e !important;
  border-color: #0f766e !important;
  color: #ffffff !important;
  font-weight: 700 !important;
  box-shadow: 0 4px 6px -1px rgba(15, 118, 110, 0.3) !important;
}

.dark .flatpickr-day.selected {
  background: #0d9488 !important;
  border-color: #0d9488 !important;
}

.flatpickr-day.prevMonthDay,
.flatpickr-day.nextMonthDay {
  color: #cbd5e1 !important;
}

.dark .flatpickr-day.prevMonthDay,
.dark .flatpickr-day.nextMonthDay {
  color: #475569 !important;
}

.flatpickr-day.disabled {
  color: #e2e8f0 !important;
  cursor: not-allowed !important;
}

.dark .flatpickr-day.disabled {
  color: #334155 !important;
}
</style>
