/**
 * Shared calendar helpers.
 *
 * Centralised here so the employee Dashboard month view and the Holiday
 * Calendar page parse and format the same API dates the same way.
 */

import type { CalendarEvent } from '@/types/employee/calendar/CalendarType'

export const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
export const WEEKDAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

/** Used when an event has no type, or its type was soft-deleted. */
export const FALLBACK_COLOR = '#94a3b8'

/** The date half of "24 Jul 2026, 00:00 AM". */
export function datePart(raw?: string | null): string {
  if (!raw) return ''

  return (String(raw).split(',')[0] ?? '').trim()
}

/** The time half of "24 Jul 2026, 00:00 AM". */
export function timePart(raw?: string | null): string {
  if (!raw) return ''

  return (String(raw).split(',')[1] ?? '').trim()
}

/** Parse an API date ("24 Jul 2026, 00:00 AM" or ISO) to local midnight. */
export function parseApiDate(raw?: string | null): Date | null {
  const part = datePart(raw)

  if (!part) return null

  const iso = /^(\d{4})-(\d{1,2})-(\d{1,2})/.exec(part)

  if (iso) return new Date(Number(iso[1]), Number(iso[2]) - 1, Number(iso[3]))

  const parsed = new Date(part)

  if (Number.isNaN(parsed.getTime())) return null

  return new Date(parsed.getFullYear(), parsed.getMonth(), parsed.getDate())
}

/** Numeric YYYYMMDD key for cheap same-day comparisons. */
export function dayKey(date: Date): number {
  return date.getFullYear() * 10000 + date.getMonth() * 100 + date.getDate()
}

/** "4 Sep" */
export function formatShortDay(date: Date): string {
  return `${date.getDate()} ${MONTHS[date.getMonth()]}`
}

/** "Fri, 4 Sep 2026" */
export function formatLongDay(date: Date): string {
  return `${WEEKDAYS[date.getDay()]}, ${date.getDate()} ${MONTHS[date.getMonth()]} ${date.getFullYear()}`
}

/** Event type may be null in real data — never dereference it directly. */
export function eventColor(event: CalendarEvent): string {
  return event.event_type?.color || FALLBACK_COLOR
}

/** "09:00 AM - 05:00 PM", or "All day". */
export function eventTime(event: CalendarEvent): string {
  if (event.is_all_day) return 'All day'

  const start = timePart(event.start_date)
  const end = timePart(event.end_date)

  if (!start) return 'All day'

  return end ? `${start} - ${end}` : start
}

/** "Sep 4, 2026, 12:00 AM" — all-day events drop the clock time. */
export function eventStamp(event: CalendarEvent, raw: string | null): string {
  const date = parseApiDate(raw)

  if (!date) return '—'

  const day = `${MONTHS[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`

  if (event.is_all_day) return day

  const time = timePart(raw)

  return time ? `${day}, ${time}` : day
}

export interface EventRange {
  event: CalendarEvent
  start: Date
  startKey: number
  endKey: number
}

/** Active events mapped to a start/end day-key range, earliest first. */
export function toEventRanges(events: CalendarEvent[]): EventRange[] {
  return events
    .filter(event => event.is_active)
    .map(event => {
      const parsedStart = parseApiDate(event.start_date)
      const start = parsedStart ?? new Date()
      const end = parseApiDate(event.end_date) ?? start
      const [from, to] = start <= end ? [start, end] : [end, start]

      return { event, start: from, startKey: dayKey(from), endKey: dayKey(to) }
    })
    .sort((a, b) => a.startKey - b.startKey)
}

/** Every event whose range covers the given day. */
export function eventsOn(ranges: EventRange[], date: Date): CalendarEvent[] {
  const key = dayKey(date)

  return ranges
    .filter(range => key >= range.startKey && key <= range.endKey)
    .map(range => range.event)
}
