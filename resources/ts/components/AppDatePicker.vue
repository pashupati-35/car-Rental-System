<script setup lang="ts">
import { useTheme } from 'vuetify'
import FlatPickr from 'vue-flatpickr-component'

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  type: 'date',
  label: '',
  disabled: false,
  readonly: false,
  clearable: false,
  errorMessages: () => [],
  config: () => ({}),
})

const emit = defineEmits<{
    'update:modelValue': [value: string]
    'blur': []
}>()

defineOptions({
  inheritAttrs: false,
})

interface Props {
    modelValue?: string

    type?: 'date' | 'datetime-local'
    label?: string
    placeholder?: string
    disabled?: boolean
    readonly?: boolean
    clearable?: boolean
    errorMessages?: string[]
    config?: Record<string, any>
}

const vuetifyTheme = useTheme()

const refFlatPicker = ref()
const refTextField = ref()
const isCalendarOpen = ref(false)

const hasTime = computed(() => props.type === 'datetime-local')

// flatpickr's own input is hidden, so the visible Vuetify field is what the
// calendar has to line up with
const getAnchorElement = (): HTMLElement | null => {
  const root = refTextField.value?.$el as HTMLElement | undefined

  return (root?.querySelector('.v-field') as HTMLElement | null) ?? root ?? null
}

// The calendar lives on <body> and is positioned in document coordinates.
// Vuetify pins <html> with `position: fixed` while a dialog is open, which both
// zeroes `pageYOffset` and turns <html> into the containing block — flatpickr's
// own math then drops the calendar near the top of the screen. Measuring
// against the document element covers the pinned and the normal case alike.
const positionCalendar = (fp: any) => {
  const anchor = getAnchorElement()
  const calendar = fp?.calendarContainer as HTMLElement | undefined

  if (!anchor || !calendar) return

  const anchorRect = anchor.getBoundingClientRect()
  const origin = document.documentElement.getBoundingClientRect()
  const calendarHeight = calendar.offsetHeight
  const calendarWidth = calendar.offsetWidth
  const gap = 2

  // Flip above the field when there is not enough room underneath it
  const showOnTop = window.innerHeight - anchorRect.bottom < calendarHeight
    && anchorRect.top > calendarHeight

  const top = showOnTop
    ? anchorRect.top - calendarHeight - gap
    : anchorRect.bottom + gap

  // Keep the calendar on screen when the field sits close to the right edge
  const left = Math.max(8, Math.min(anchorRect.left, window.innerWidth - calendarWidth - 8))

  calendar.style.top = `${top - origin.top}px`
  calendar.style.left = `${left - origin.left}px`
  calendar.style.right = 'auto'
}

const repositionCalendar = () => {
  if (refFlatPicker.value?.fp)
    positionCalendar(refFlatPicker.value.fp)
}

const flatpickrConfig = computed(() => ({
  enableTime: hasTime.value,

  // Escaped "T" keeps the emitted value in the same shape as a native
  // datetime-local input, e.g. "2025-06-26T09:30"
  dateFormat: hasTime.value ? 'Y-m-d\\TH:i' : 'Y-m-d',
  altInput: true,
  altFormat: hasTime.value ? 'F j, Y h:i K' : 'F j, Y',
  position: positionCalendar,
  ...props.config,
}))

// vue-flatpickr applies config changes through fp.set(), which cannot add or
// remove the time inputs on an existing instance, so rebuild it on type change
const pickerKey = computed(() => (hasTime.value ? 'datetime' : 'date'))

// The text field mirrors the raw model value; hide the "T" separator from it
const displayValue = computed(() => (props.modelValue ?? '').replace('T', ' '))

// The calendar is appended to <body>, outside Vuetify's app root, so it carries
// its own theme class for the --v-theme-* variables to resolve
const updateThemeClassInCalendar = () => {
  if (!refFlatPicker.value?.fp?.calendarContainer) return

  const themeName = vuetifyTheme.global.name.value

  refFlatPicker.value.fp.calendarContainer.classList.add(`v-theme--${themeName}`)
}

onMounted(() => {
  updateThemeClassInCalendar()
})

const onCalendarOpen = () => {
  isCalendarOpen.value = true

  // The container is recreated whenever the picker is rebuilt (type change)
  updateThemeClassInCalendar()

  // Capture phase so scrolling inside a dialog moves the calendar along too
  window.addEventListener('scroll', repositionCalendar, true)
}

const onCalendarClose = () => {
  isCalendarOpen.value = false
  window.removeEventListener('scroll', repositionCalendar, true)
}

onBeforeUnmount(() => {
  window.removeEventListener('scroll', repositionCalendar, true)
})

const handleInput = (val: string) => {
  // vue-flatpickr v12 calls setDate(value, true) on programmatic prop changes,
  // which makes flatpickr echo update:modelValue back even when the user didn't
  // interact. Ignore echoes that match the current value so they don't mark the
  // field dirty / trigger validation.
  if ((val ?? '') === (props.modelValue ?? ''))
    return

  emit('update:modelValue', val)
}

const handleBlur = () => {
  emit('blur')
}

const handleClear = () => {
  emit('update:modelValue', '')
}

const openCalendar = () => {
  if (!props.disabled && !props.readonly && refFlatPicker.value) {
    refFlatPicker.value.fp.open()
  }
}
</script>

<template>
  <div class="app-date-picker-wrapper">
    <VTextField
      ref="refTextField"
      :model-value="displayValue"
      :label="label"
      :placeholder="placeholder"
      :disabled="disabled"
      readonly
      :clearable="clearable"
      :error-messages="errorMessages"
      prepend-inner-icon="ri-calendar-line"
      @click="openCalendar"
      @click:clear="handleClear"
      @blur="handleBlur"
    >
      <template #label>
        <slot name="label">
          {{ label }}
        </slot>
      </template>
    </VTextField>

    <FlatPickr
      :key="pickerKey"
      ref="refFlatPicker"
      :model-value="modelValue"
      :config="flatpickrConfig"
      :placeholder="placeholder"
      :disabled="disabled"
      class="flat-picker-hidden"
      @update:model-value="handleInput"
      @on-open="onCalendarOpen"
      @on-close="onCalendarClose"
    />
  </div>
</template>

<style lang="scss">
@use "flatpickr/dist/flatpickr.css";

.app-date-picker-wrapper {
    position: relative;
}

.flat-picker-hidden {
    position: absolute;
    opacity: 0;
    pointer-events: none;
    width: 0;
    height: 0;
}

.flatpickr-calendar {
    border-radius: 8px;
    background-color: rgb(var(--v-theme-surface));
    box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(var(--v-border-color), 0.12);
    padding: 8px;
    width: 320px;

    .flatpickr-months {
        padding: 8px 0 12px;

        .flatpickr-month {
            height: 36px;
        }

        .flatpickr-current-month {
            font-size: 1rem;
            font-weight: 600;
            color: rgba(var(--v-theme-on-surface), 0.87);

            .flatpickr-monthDropdown-months {
                font-weight: 600;
                appearance: none;
                background: transparent;
                border: none;
                color: rgba(var(--v-theme-on-surface), 0.87);
            }

            .numInputWrapper {
                input.cur-year {
                    font-weight: 600;
                    color: rgba(var(--v-theme-on-surface), 0.87);
                }
            }
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            fill: rgba(var(--v-theme-on-surface), 0.6);
            padding: 8px;
            border-radius: 4px;
            transition: all 0.2s;

            &:hover {
                background: rgba(var(--v-theme-on-surface), 0.08);

                svg {
                    fill: rgba(var(--v-theme-on-surface), 0.87);
                }
            }

            svg {
                width: 14px;
                height: 14px;
                fill: rgba(var(--v-theme-on-surface), 0.6);
                stroke: none;
            }
        }
    }

    .flatpickr-weekdays {
        margin: 8px 0;

        .flatpickr-weekday {
            color: rgba(var(--v-theme-on-surface), 0.6);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    }

    .flatpickr-days {
        width: 100%;

        .dayContainer {
            width: 100%;
            min-width: 100%;
            max-width: 100%;
        }
    }

    .flatpickr-day {
        color: rgba(var(--v-theme-on-background), 0.87);
        border-radius: 6px;
        font-weight: 500;
        height: 40px;
        line-height: 40px;
        max-width: 40px;
        transition: all 0.2s ease;
        border: none;

        &.today {
            border: 2px solid rgb(var(--v-theme-primary));
            background-color: transparent;
            color: rgb(var(--v-theme-primary));
            font-weight: 600;

            &:hover {
                background-color: rgba(var(--v-theme-primary), 0.08);
            }
        }

        &.selected,
        &.selected:hover {
            background: rgb(var(--v-theme-primary));
            color: rgb(var(--v-theme-on-primary));
            font-weight: 600;
            box-shadow: 0px 2px 4px rgba(var(--v-theme-primary), 0.3);
            border: none;
        }

        &.inRange {
            background: rgba(var(--v-theme-primary), 0.12) !important;
            color: rgb(var(--v-theme-primary));
            box-shadow: none !important;
            border: none;
        }

        &.startRange,
        &.endRange {
            background: rgb(var(--v-theme-primary)) !important;
            color: rgb(var(--v-theme-on-primary));
            font-weight: 600;
            box-shadow: 0px 2px 4px rgba(var(--v-theme-primary), 0.3);
        }

        &.prevMonthDay,
        &.nextMonthDay {
            color: rgba(var(--v-theme-on-background), 0.38);
        }

        &.flatpickr-disabled {
            color: rgba(var(--v-theme-on-background), 0.26);
            cursor: not-allowed;
        }

        &:hover:not(.selected):not(.startRange):not(.endRange):not(.flatpickr-disabled) {
            background: rgba(var(--v-theme-on-surface), 0.08);
            color: rgba(var(--v-theme-on-surface), 0.87);
            border: none;
        }
    }

    &.open {
        z-index: 2401;
    }

    &::before,
    &::after {
        display: none;
    }

    // Time picker styles (if enabled)
    &.hasTime {
        .flatpickr-time {
            border-top: 1px solid rgba(var(--v-border-color), 0.12);
            margin-top: 8px;
            padding-top: 12px;

            input,
            .flatpickr-am-pm {
                color: rgba(var(--v-theme-on-surface), 0.87);
                font-weight: 500;

                &:hover,
                &:focus {
                    background: rgba(var(--v-theme-on-surface), 0.04);
                }
            }

            .flatpickr-time-separator {
                color: rgba(var(--v-theme-on-surface), 0.6);
            }
        }
    }
}
</style>
