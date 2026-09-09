/**
 * Shared chat UI utility functions.
 *
 * Centralised here to prevent the same logic being duplicated across
 * PortalConversationList, PortalChatWindow, PortalMessageBubble, and NewPortalChatModal.
 */

import type { MessageType } from '@/types/chat'

/** Return up to 2 uppercase initials from a display name. */
export function getInitials(name: string | null | undefined): string {
  if (!name) return '?'

  return name
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map(n => n[0]?.toUpperCase() ?? '')
    .join('')
}

/**
 * Format an ISO timestamp for message bubbles.
 * Output: "3:45 PM"
 */
export function formatMessageTime(iso: string | null | undefined): string {
  if (!iso) return ''
  try {
    return new Date(iso).toLocaleTimeString(undefined, {
      hour: '2-digit',
      minute: '2-digit',
    })
  }
  catch {
    return ''
  }
}

/**
 * Format an ISO timestamp for conversation-list previews.
 * Output: "now" / "5m" / "3h" / "2d" / "Jan 5"
 */
export function formatConversationTime(iso: string | null | undefined): string {
  if (!iso) return ''
  try {
    const date   = new Date(iso)
    const now    = new Date()
    const diffMs = now.getTime() - date.getTime()
    const diffM  = Math.floor(diffMs / 60_000)
    const diffH  = Math.floor(diffM / 60)
    const diffD  = Math.floor(diffH / 24)

    if (diffM < 1)  return 'now'
    if (diffM < 60) return `${diffM}m`
    if (diffH < 24) return `${diffH}h`
    if (diffD < 7)  return `${diffD}d`

    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
  }
  catch {
    return ''
  }
}

/**
 * Short preview text for the last message shown in a conversation-list row.
 */
export function messagePreview(
  msg: { body: string | null; type: MessageType | string } | null,
): string {
  if (!msg) return 'No messages yet'
  if (msg.type === 'image') return '📷 Photo'
  if (msg.type === 'file')  return '📎 Attachment'

  return msg.body ?? 'No messages yet'
}
