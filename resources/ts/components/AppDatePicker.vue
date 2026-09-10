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
    minDate?: string | Date
    maxDate?: string | Date
    disabledDates?: Array<string | Date | { from: string; to: string }>
    bookedDates?: Array<string | Date | { from: string; to: string }>
    showLegend?: boolean
    required?: boolean
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
    minDate: 'today',
    maxDate: undefined,
    disabledDates: () => [],
    bookedDates: () => [],
    showLegend: true,
    required: false,
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

const isRequired = computed(() => props.required || (props.label ? props.label.includes('*') : false))

const cleanLabelText = computed(() => {
  if (!props.label) {
    return ''
  }

  return props.label.replace(/\s*\*\s*$/, '').trim()
})

const combinedDisabledDates = computed(() => {
  const list: Array<string | Date | { from: string; to: string }> = [
    ...(props.disabledDates || []),
    ...(props.bookedDates || []),
    ...((props.config?.disable as any) || []),
  ]

  return list
})

const flatpickrConfig = computed(() => ({
  enableTime: hasTime.value,
  noCalendar: props.type === 'time',
  dateFormat: props.type === 'datetime-local' ? 'Y-m-d H:i' : props.type === 'time' ? 'H:i' : 'Y-m-d',
  altInput: true,
  altFormat: props.type === 'datetime-local' ? 'M j, Y h:i K' : props.type === 'time' ? 'h:i K' : 'M j, Y',
  altInputClass: 'app-date-input-styled w-full bg-transparent text-sm font-semibold text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none py-3 pe-3 cursor-pointer min-w-0 truncate',
  minDate: props.minDate ?? 'today',
  maxDate: props.maxDate,
  disable: combinedDisabledDates.value,
  prevArrow: '<svg class="w-4 h-4 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>',
  nextArrow: '<svg class="w-4 h-4 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>',
  onDayCreate: (_dObj: any, _dStr: any, _fp: any, dayElem: HTMLElement) => {
    const dateObj = (dayElem as any).dateObj
    if (!dateObj) return

    const year = dateObj.getFullYear()
    const month = String(dateObj.getMonth() + 1).padStart(2, '0')
    const day = String(dateObj.getDate()).padStart(2, '0')
    const ymd = `${year}-${month}-${day}`

    const isBooked = combinedDisabledDates.value.some((d: any) => {
      if (typeof d === 'string') {
        return d.startsWith(ymd)
      }

      return false
    })

    if (isBooked) {
      dayElem.classList.add('is-booked-date')
      dayElem.setAttribute('data-booked', 'true')
      dayElem.title = '🔒 Already Booked / Reserved'
    }
  },
  onReady: (_selectedDates: any, _dateStr: any, instance: any) => {
    const calendarContainer = instance.calendarContainer
    if (calendarContainer && !calendarContainer.querySelector('.app-calendar-legend')) {
      const legend = document.createElement('div')

      legend.className = 'app-calendar-legend'
      legend.innerHTML = `
        <div class="legend-item"><span class="legend-dot dot-booked"></span><span>Booked</span></div>
        <div class="legend-item"><span class="legend-dot dot-today"></span><span>Today</span></div>
        <div class="legend-item"><span class="legend-dot dot-available"></span><span>Available</span></div>
      `
      calendarContainer.appendChild(legend)
    }
  },
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
    class="app-date-picker-container w-full min-w-0"
  >
    <!-- Label -->
    <label
      v-if="label"
      class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-1.5 flex items-center justify-between tracking-tight"
    >
      <span class="flex items-center gap-1">
        <span>{{ cleanLabelText }}</span>
        <span
          v-if="isRequired"
          class="text-rose-500 font-extrabold text-sm leading-none"
          title="Required field"
        >*</span>
      </span>
      <button
        v-if="clearable && modelValue && !disabled && !readonly"
        type="button"
        class="text-xs text-slate-400 hover:text-rose-500 transition-colors font-medium cursor-pointer px-1.5 py-0.5 rounded hover:bg-rose-50 dark:hover:bg-rose-950/40"
        @click.stop="clearValue"
      >
        Clear
      </button>
    </label>

    <!-- Input Wrapper -->
    <div
      class="relative flex items-center rounded-xl border transition-all duration-200 shadow-xs min-w-0 overflow-hidden"
      :class="[
        error
          ? 'border-rose-400 dark:border-rose-600 bg-rose-50/40 ring-2 ring-rose-500/20'
          : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 focus-within:border-blue-600 dark:focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/25',
        disabled ? 'opacity-60 cursor-not-allowed bg-slate-100 dark:bg-slate-900' : ''
      ]"
    >
      <!-- Calendar Icon Prefix -->
      <div class="text-blue-600 dark:text-blue-400 pointer-events-none flex items-center shrink-0 ps-3.5 pe-2">
        <svg
          class="w-5 h-5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
      </div>

      <!-- Flatpickr Core Component -->
      <FlatPickr
        ref="flatpickrRef"
        :model-value="modelValue || ''"
        :config="flatpickrConfig"
        :placeholder="placeholder"
        :disabled="disabled"
        class="w-full bg-transparent text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none py-3 pe-3 font-semibold cursor-pointer min-w-0 truncate"
        @update:model-value="handleInput"
        @on-open="isOpen = true"
        @on-close="isOpen = false"
      />
    </div>

    <!-- Error message if any -->
    <p
      v-if="error"
      class="text-[11px] text-rose-500 mt-1 font-semibold flex items-center gap-1"
    >
      <svg
        class="w-3.5 h-3.5 shrink-0"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        />
      </svg>
      {{ error }}
    </p>
  </div>
</template>

<style>
@import "flatpickr/dist/flatpickr.css";

/* Style generated altInput from flatpickr */
.app-date-input-styled {
  width: 100% !important;
  background: transparent !important;
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
  font-size: 0.875rem !important;
  line-height: 1.25rem !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  cursor: pointer !important;
  min-width: 0 !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  padding-top: 0.75rem !important;
  padding-bottom: 0.75rem !important;
  padding-inline-end: 0.75rem !important;
}

.dark .app-date-input-styled {
  color: #f8fafc !important;
}

.app-date-input-styled::placeholder {
  color: #94a3b8 !important;
  opacity: 1 !important;
  font-weight: 500 !important;
}

.dark .app-date-input-styled::placeholder {
  color: #64748b !important;
}

/* Modern Dropdown & Calendar Styling */
.flatpickr-calendar {
  background: #ffffff !important;
  border-radius: 1.25rem !important;
  box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
  border: none !important;
  padding: 1rem !important;
  width: 320px !important;
  font-family: inherit !important;
  z-index: 99999 !important;
  animation: fpDropdownSlide 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

@keyframes fpDropdownSlide {
  from {
    opacity: 0;
    transform: translateY(6px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.dark .flatpickr-calendar {
  background: #0f172a !important;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
}

.flatpickr-calendar::before,
.flatpickr-calendar::after {
  display: none !important;
}

/* Calendar Header / Months */
.flatpickr-months {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding-bottom: 0.75rem !important;
  border-bottom: 1px solid #f1f5f9 !important;
}

.dark .flatpickr-months {
  border-bottom-color: #1e293b !important;
}

.flatpickr-months .flatpickr-month {
  height: 38px !important;
  color: #0f172a !important;
}

.dark .flatpickr-months .flatpickr-month {
  color: #f8fafc !important;
}

.flatpickr-current-month {
  font-size: 0.9375rem !important;
  font-weight: 800 !important;
  padding: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 4px !important;
}

.flatpickr-current-month .flatpickr-monthDropdown-months {
  font-weight: 800 !important;
  color: #0f172a !important;
  appearance: none !important;
  cursor: pointer !important;
  padding: 2px 6px !important;
  border-radius: 6px !important;
}

.flatpickr-current-month .flatpickr-monthDropdown-months:hover {
  background: #f1f5f9 !important;
}

.dark .flatpickr-current-month .flatpickr-monthDropdown-months {
  color: #f1f5f9 !important;
  background: #0f172a !important;
}

.dark .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
  background: #1e293b !important;
}

.flatpickr-current-month input.cur-year {
  font-weight: 800 !important;
  color: #0f172a !important;
  padding: 2px 4px !important;
}

.dark .flatpickr-current-month input.cur-year {
  color: #f1f5f9 !important;
}

.flatpickr-prev-month,
.flatpickr-next-month {
  padding: 8px !important;
  border-radius: 0.75rem !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  transition: all 0.15s ease !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

.flatpickr-prev-month:hover,
.flatpickr-next-month:hover {
  background: #e2e8f0 !important;
  transform: scale(1.05) !important;
}

.dark .flatpickr-prev-month,
.dark .flatpickr-next-month {
  background: #1e293b !important;
  border-color: #334155 !important;
}

.dark .flatpickr-prev-month:hover,
.dark .flatpickr-next-month:hover {
  background: #334155 !important;
}

/* Weekday header */
.flatpickr-weekdays {
  margin: 0.625rem 0 0.375rem 0 !important;
}

span.flatpickr-weekday {
  color: #64748b !important;
  font-size: 0.7rem !important;
  font-weight: 800 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.06em !important;
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

/* Day items base */
.flatpickr-day {
  border-radius: 0.625rem !important;
  font-size: 0.8125rem !important;
  font-weight: 700 !important;
  height: 38px !important;
  line-height: 38px !important;
  max-width: 38px !important;
  border: 1px solid transparent !important;
  margin: 2px 0 !important;
  transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

/* AVAILABLE DATES - Clear, attractive emerald-green highlight */
.flatpickr-day:not(.flatpickr-disabled):not(.prevMonthDay):not(.nextMonthDay):not(.is-booked-date):not(.selected) {
  color: #065f46 !important;
  background: #ecfdf5 !important;
  border-color: #a7f3d0 !important;
  font-weight: 800 !important;
}

.flatpickr-day:not(.flatpickr-disabled):not(.prevMonthDay):not(.nextMonthDay):not(.is-booked-date):not(.selected):hover {
  background: #d1fae5 !important;
  color: #047857 !important;
  border-color: #6ee7b7 !important;
  transform: scale(1.06) !important;
  box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.25) !important;
}

.dark .flatpickr-day:not(.flatpickr-disabled):not(.prevMonthDay):not(.nextMonthDay):not(.is-booked-date):not(.selected) {
  color: #a7f3d0 !important;
  background: rgba(6, 78, 59, 0.35) !important;
  border-color: #065f46 !important;
}

.dark .flatpickr-day:not(.flatpickr-disabled):not(.prevMonthDay):not(.nextMonthDay):not(.is-booked-date):not(.selected):hover {
  background: rgba(6, 95, 70, 0.6) !important;
  color: #6ee7b7 !important;
  border-color: #059669 !important;
}

/* Today styling */
.flatpickr-day.today {
  border: 2px solid #2563eb !important;
  border-radius: 0.625rem !important;
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  font-weight: 800 !important;
}

.dark .flatpickr-day.today {
  border-color: #60a5fa !important;
  background: rgba(37, 99, 235, 0.2) !important;
  color: #93c5fd !important;
}

/* Selected Day styling */
.flatpickr-day.selected,
.flatpickr-day.selected:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
  border-color: #1d4ed8 !important;
  color: #ffffff !important;
  font-weight: 800 !important;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35) !important;
}

.dark .flatpickr-day.selected {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
  border-color: #2563eb !important;
}

/* In Range Styling */
.flatpickr-day.inRange {
  background: #dbeafe !important;
  border-color: transparent !important;
  color: #1e40af !important;
}

.dark .flatpickr-day.inRange {
  background: #1e3a8a !important;
  color: #bfdbfe !important;
}

/* Month boundary days */
.flatpickr-day.prevMonthDay,
.flatpickr-day.nextMonthDay {
  color: #cbd5e1 !important;
  opacity: 0.5 !important;
  background: transparent !important;
  border-color: transparent !important;
}

.dark .flatpickr-day.prevMonthDay,
.dark .flatpickr-day.nextMonthDay {
  color: #475569 !important;
}

/* Disabled Past Dates (Before Today) */
.flatpickr-day.flatpickr-disabled:not(.is-booked-date) {
  color: #cbd5e1 !important;
  background: transparent !important;
  border-color: transparent !important;
  opacity: 0.35 !important;
  cursor: not-allowed !important;
  text-decoration: none !important;
}

.dark .flatpickr-day.flatpickr-disabled:not(.is-booked-date) {
  color: #475569 !important;
  opacity: 0.3 !important;
}

/* BOOKED / RESERVED DATES - High visibility distinct badge */
.flatpickr-day.is-booked-date,
.flatpickr-day.flatpickr-disabled.is-booked-date {
  background: #ffe4e6 !important;
  color: #e11d48 !important;
  border-color: #fecdd3 !important;
  text-decoration: line-through !important;
  cursor: not-allowed !important;
  font-weight: 800 !important;
  position: relative !important;
  opacity: 0.95 !important;
}

.flatpickr-day.is-booked-date::after {
  content: '';
  position: absolute;
  bottom: 3px;
  left: 50%;
  transform: translateX(-50%);
  width: 4px;
  height: 4px;
  border-radius: 9999px;
  background: #e11d48;
}

.dark .flatpickr-day.is-booked-date,
.dark .flatpickr-day.flatpickr-disabled.is-booked-date {
  background: #4c0519 !important;
  color: #fda4af !important;
  border-color: #881337 !important;
  opacity: 0.95 !important;
}

.dark .flatpickr-day.is-booked-date::after {
  background: #fb7185;
}

/* Calendar Legend at bottom of Dropdown */
.app-calendar-legend {
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding-top: 0.75rem;
  margin-top: 0.625rem;
  border-top: 1px solid #f1f5f9;
  font-size: 0.6875rem;
  font-weight: 700;
  color: #64748b;
}

.dark .app-calendar-legend {
  border-top-color: #1e293b;
  color: #94a3b8;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 5px;
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 9999px;
}

.dot-booked {
  background: #e11d48;
  box-shadow: 0 0 0 2px #ffe4e6;
}

.dot-today {
  background: #2563eb;
  box-shadow: 0 0 0 2px #dbeafe;
}

.dot-available {
  background: #10b981;
  box-shadow: 0 0 0 2px #d1fae5;
}
</style>
