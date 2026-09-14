<script setup lang="ts">
import type { CalendarEvent, EventRange } from '@/utils/calendarEvents'
import {
  MONTHS,
  WEEKDAYS,
  dayKey,
  eventColor,
  eventStamp,
  eventsOn,
  formatLongDay,
  toEventRanges,
} from '@/utils/calendarEvents'

interface Props {
  events: CalendarEvent[]

  /** The highlighted day. Use with v-model:selected. */
  selected: Date
  loading?: boolean

  /** Hex colour for today's pill and the selected-day ring. */
  accent?: string

  /** How many event names fit in a cell before they collapse to "+N more". */
  maxPerDay?: number
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  accent: '#0d9488',
  maxPerDay: 2,
})

const emit = defineEmits<{
  'update:selected': [value: Date]
}>()

const today = new Date()

today.setHours(0, 0, 0, 0)

const todayKey = dayKey(today)

const viewDate = ref(new Date(props.selected.getFullYear(), props.selected.getMonth(), 1))

const ranges = computed<EventRange[]>(() => toEventRanges(props.events))

const monthLabel = computed(
  () => `${MONTHS[viewDate.value.getMonth()]} ${viewDate.value.getFullYear()}`,
)

const monthEventCount = computed(() => {
  const year = viewDate.value.getFullYear()
  const month = viewDate.value.getMonth()
  const from = dayKey(new Date(year, month, 1))
  const to = dayKey(new Date(year, month + 1, 0))

  return ranges.value.filter((range: EventRange) => range.endKey >= from && range.startKey <= to).length
})

/**
 * Only the weeks this month actually touches - a month ending on a Sunday keeps
 * that last row, but nothing pads the grid out to a fixed six.
 * Days either side of the month render as blanks that hold the column open.
 */
const calendarCells = computed(() => {
  const year = viewDate.value.getFullYear()
  const month = viewDate.value.getMonth()
  const leading = new Date(year, month, 1).getDay()
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const cellCount = Math.ceil((leading + daysInMonth) / 7) * 7
  const selectedKey = dayKey(props.selected)

  return Array.from({ length: cellCount }, (_, index) => {
    const date = new Date(year, month, index - leading + 1)
    const key = dayKey(date)
    const filler = date.getMonth() !== month

    return {
      key,
      date,
      filler,
      day: date.getDate(),
      col: index % 7,
      isToday: !filler && key === todayKey,
      isSelected: !filler && key === selectedKey,
      isWeekend: date.getDay() === 0 || date.getDay() === 6,
      isSaturday: date.getDay() === 6,
      events: filler ? [] : eventsOn(ranges.value, date),
    }
  })
})

const selectedStyle = computed(() => ({
  backgroundColor: `${props.accent}1a`,
  boxShadow: `inset 0 0 0 1px ${props.accent}80`,
}))

const todayStyle = computed(() => ({
  backgroundColor: props.accent,
  color: '#fff',
}))

/** Hover cards on the edge columns would spill outside the card — pin them inward. */
function hoverCardAlign(col: number): string {
  if (col <= 1) return 'left-0'

  if (col >= 5) return 'right-0'

  return 'left-1/2 -translate-x-1/2'
}

function selectDay(date: Date): void {
  emit('update:selected', new Date(date))
}

function shiftMonth(delta: number): void {
  viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() + delta, 1)
}

function goToToday(): void {
  viewDate.value = new Date(today.getFullYear(), today.getMonth(), 1)
  emit('update:selected', new Date(today))
}
</script>

<template>
  <div :style="{ '--cal-accent': accent }">
    <!-- Month nav -->
    <div class="flex flex-col pa-5 gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div class="min-w-0">
        <h2 class="text-base font-semibold text-slate-900 dark:text-slate-100">
          {{ monthLabel }}
        </h2>
        <p class="mt-1.5 truncate text-sm text-slate-500 dark:text-slate-400">
          {{ monthEventCount }} {{ monthEventCount === 1 ? 'event' : 'events' }} this month
        </p>
      </div>

      <div class="flex shrink-0 items-center gap-2">
        <button
          type="button"
          class="h-9 rounded-full bg-slate-100 px-4 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
          @click="goToToday"
        >
          Today
        </button>

        <div class="flex items-center gap-0.5 rounded-full bg-slate-100 p-0.5 dark:bg-slate-800">
          <button
            type="button"
            class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-white hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-100"
            aria-label="Previous month"
            @click="shiftMonth(-1)"
          >
            <svg
              class="h-4 w-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
          <button
            type="button"
            class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-white hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-100"
            aria-label="Next month"
            @click="shiftMonth(1)"
          >
            <svg
              class="h-4 w-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      class="mt-8 flex animate-pulse flex-col gap-3"
    >
      <div class="h-6 w-full rounded-lg bg-slate-100 dark:bg-slate-800" />
      <div
        v-for="row in 6"
        :key="row"
        class="h-14 w-full rounded-xl bg-slate-100 dark:bg-slate-800"
      />
    </div>

    <template v-else>
      <!-- Weekday row -->
      <div class="mt-7 grid grid-cols-7">
        <div
          v-for="weekday in WEEKDAYS"
          :key="weekday"
          class="py-1 text-center text-[10px] font-semibold uppercase tracking-widest sm:text-[11px]"
          :class="weekday === 'Sat' ? 'text-rose-400' : 'text-slate-400 dark:text-slate-500'"
        >
          <span class="hidden sm:inline">{{ weekday }}</span>
          <span class="sm:hidden">{{ weekday[0] }}</span>
        </div>
      </div>

      <!-- Days grid -->
      <div class="mt-2 grid grid-cols-7 gap-1 sm:gap-1.5">
        <template
          v-for="cell in calendarCells"
          :key="cell.key"
        >
          <!-- Days either side of the month: an empty box, just holding the column -->
          <div v-if="cell.filler" />

          <button
            v-else
            type="button"
            class="calendar-cell relative flex min-h-20 flex-col items-stretch rounded-xl px-1 pb-1 pt-1.5 text-left transition-colors sm:min-h-24 sm:px-1.5 sm:pt-2"
            :class="cell.isSelected ? '' : 'hover:bg-slate-100 dark:hover:bg-slate-800'"
            :style="cell.isSelected ? selectedStyle : undefined"
            @click="selectDay(cell.date)"
          >
            <span
              class="mx-auto flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-semibold transition-colors sm:mx-0 sm:text-xs"
              :class="cell.isToday
                ? 'shadow-sm'
                : cell.isSaturday ? 'text-rose-400' : cell.isWeekend ? 'text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-300'"
              :style="cell.isToday ? todayStyle : undefined"
            >
              {{ cell.day }}
            </span>

            <!-- Event names -->
            <span
              v-if="cell.events.length"
              class="mt-1 flex min-w-0 flex-col gap-0.5"
            >
              <span
                v-for="event in cell.events.slice(0, maxPerDay)"
                :key="event.id"
                class="group/event relative block min-w-0 rounded-md border-l-2 px-1 py-0.5 text-[9px] font-semibold leading-tight sm:text-[10px]"
                :style="{
                  borderColor: eventColor(event),
                  backgroundColor: `${eventColor(event)}1a`,
                  color: eventColor(event),
                }"
              >
                <span class="block truncate">{{ event.title }}</span>

                <!-- Hover detail -->
                <span
                  class="pointer-events-none absolute bottom-full z-20 mb-2 block w-60 origin-bottom scale-95 rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-left opacity-0 shadow-xl transition-all duration-150 group-hover/event:scale-100 group-hover/event:opacity-100 dark:border-slate-700 dark:bg-slate-800"
                  :class="hoverCardAlign(cell.col)"
                >
                  <span class="block text-sm font-semibold leading-snug text-slate-900 dark:text-slate-100">
                    {{ event.title }}
                  </span>

                  <span
                    v-if="event.event_type"
                    class="mt-2 inline-block rounded-full px-2.5 py-1 text-[10px] font-semibold text-white"
                    :style="{ backgroundColor: eventColor(event) }"
                  >
                    {{ event.event_type.title }}
                  </span>

                  <span class="mt-2.5 flex items-center gap-2 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                    <svg
                      class="h-3.5 w-3.5 shrink-0"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                    {{ eventStamp(event, event.start_date) }}
                  </span>

                  <span class="mt-1.5 flex items-center gap-2 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                    <svg
                      class="h-3.5 w-3.5 shrink-0"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 12h16m0 0l-6-6m6 6l-6 6"
                      />
                    </svg>
                    {{ eventStamp(event, event.end_date) }}
                  </span>

                  <span
                    v-if="event.location"
                    class="mt-1.5 flex items-center gap-2 text-[11px] text-slate-400 dark:text-slate-500"
                  >
                    <svg
                      class="h-3.5 w-3.5 shrink-0"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                      />
                    </svg>
                    <span class="truncate">{{ event.location }}</span>
                  </span>

                  <span
                    v-if="event.description"
                    class="calendar-clamp mt-2 text-[11px] leading-relaxed text-slate-400 dark:text-slate-500"
                  >
                    {{ event.description }}
                  </span>
                </span>
              </span>

              <span
                v-if="cell.events.length > maxPerDay"
                class="px-1 text-[9px] font-semibold leading-tight text-slate-400 dark:text-slate-500"
              >
                +{{ cell.events.length - maxPerDay }} more
              </span>
            </span>
          </button>
        </template>
      </div>

      <!-- Screen-reader label for the highlighted day -->
      <span class="sr-only">Selected day: {{ formatLongDay(props.selected) }}</span>
    </template>
  </div>
</template>

<style scoped>
.calendar-cell:focus {
  outline: none;
}

.calendar-cell:focus-visible {
  outline: 2px solid var(--cal-accent);
  outline-offset: 2px;
}

.calendar-clamp {
  display: -webkit-box;
  overflow: hidden;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}
</style>
