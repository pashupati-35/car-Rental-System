/**
 * Single source of truth for notification → icon + tone mapping.
 * Used by both the bell dropdown row and the full-page notification center
 * so both surfaces stay visually consistent when new types are added.
 *
 * Tones map to a small Tailwind class set in `TONE_BG`; `bgClass()` returns
 * a ready-to-bind class string.
 */

export interface NotificationIcon {
  icon: string
  tone: NotificationTone
}

type NotificationTone = 'sky' | 'emerald' | 'amber' | 'rose' | 'violet' | 'slate'

const TYPE_ICONS: Record<string, NotificationIcon> = {
  application_history_updated: { icon: 'ri-file-list-3-line',     tone: 'sky' },
  application_status_changed: { icon: 'ri-flag-2-line',          tone: 'sky' },
  application_status_updated: { icon: 'ri-flag-2-line',          tone: 'sky' },
  application_offer_updated: { icon: 'ri-award-line',           tone: 'amber' },
  application_visa_granted: { icon: 'ri-passport-line',        tone: 'emerald' },
  application_visa_rejected: { icon: 'ri-passport-line',        tone: 'rose' },
  application_submitted: { icon: 'ri-send-plane-line',      tone: 'sky' },
  document_uploaded: { icon: 'ri-attachment-line',      tone: 'slate' },
  document_requested: { icon: 'ri-folder-warning-line',  tone: 'amber' },
  payment_received: { icon: 'ri-bank-card-line',       tone: 'emerald' },
  payment_submitted: { icon: 'ri-bank-card-line',       tone: 'sky' },
  payment_approved: { icon: 'ri-check-double-line',    tone: 'emerald' },
  payment_rejected: { icon: 'ri-close-circle-line',    tone: 'rose' },
  payment_due: { icon: 'ri-time-line',            tone: 'amber' },
  payment_updated: { icon: 'ri-refresh-line',         tone: 'sky' },
  payment_deleted: { icon: 'ri-delete-bin-line',      tone: 'slate' },
  message_received: { icon: 'ri-chat-3-line',          tone: 'violet' },
  chat_conversation_started: { icon: 'ri-chat-new-line',        tone: 'violet' },
  discussion_topic_created: { icon: 'ri-discuss-line',         tone: 'violet' },
  discussion_topic_closed: { icon: 'ri-lock-line',            tone: 'slate' },
  student_registered: { icon: 'ri-user-add-line',        tone: 'emerald' },
  agent_registered: { icon: 'ri-user-add-line',        tone: 'emerald' },
  consultant_registered: { icon: 'ri-user-add-line',        tone: 'emerald' },
  pr_registered: { icon: 'ri-user-add-line',        tone: 'emerald' },
  profile_updated: { icon: 'ri-user-settings-line',   tone: 'slate' },
  profile_reviewed: { icon: 'ri-shield-check-line',    tone: 'sky' },
  account_approved: { icon: 'ri-shield-check-line',    tone: 'emerald' },
  account_rejected: { icon: 'ri-shield-cross-line',    tone: 'rose' },
  pr_status_updated: { icon: 'ri-shield-user-line',     tone: 'sky' },
  pr_status_changed: { icon: 'ri-shield-user-line',     tone: 'sky' },
  pr_assignment_created: { icon: 'ri-user-shared-line',     tone: 'sky' },
  pr_assignment_approved: { icon: 'ri-user-follow-line',     tone: 'emerald' },
  pr_assignment_rejected: { icon: 'ri-user-unfollow-line',   tone: 'rose' },
  system_alert: { icon: 'ri-alert-line',           tone: 'rose' },
  system_announcement: { icon: 'ri-megaphone-line',       tone: 'amber' },
  appointment_reminder: { icon: 'ri-calendar-event-line',  tone: 'sky' },
  task_assigned: { icon: 'ri-checkbox-circle-line', tone: 'sky' },
  visa_decision: { icon: 'ri-passport-line',        tone: 'sky' },
  commission_paid: { icon: 'ri-money-dollar-circle-line', tone: 'emerald' },
  subscription_activated: { icon: 'ri-vip-crown-line',       tone: 'emerald' },
  subscription_expiring: { icon: 'ri-time-line',            tone: 'amber' },
  subscription_expired: { icon: 'ri-time-line',            tone: 'rose' },
}

const TONE_BG: Record<NotificationTone, string> = {
  sky: 'bg-sky-50 text-sky-700',
  emerald: 'bg-emerald-50 text-emerald-700',
  amber: 'bg-amber-50 text-amber-700',
  rose: 'bg-rose-50 text-rose-700',
  violet: 'bg-violet-50 text-violet-700',
  slate: 'bg-slate-100 text-slate-600',
}

/** Resolve a notification type to its icon + tone (falls back to a neutral bell). */
export function iconFor(type: string | null | undefined): NotificationIcon {
  return TYPE_ICONS[type ?? ''] ?? { icon: 'ri-notification-2-line', tone: 'sky' }
}

/** Ready-to-bind Tailwind classes for the icon's circular background. */
export function toneClass(tone: NotificationTone): string {
  return TONE_BG[tone] ?? TONE_BG.sky
}
