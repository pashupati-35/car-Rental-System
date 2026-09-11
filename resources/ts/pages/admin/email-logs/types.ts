export interface EmailLogItem {
  id: number
  sender_type?: string | null
  sender_id?: number | null
  sender_name?: string
  sender_role?: string
  from?: string | null
  to: string
  cc?: string | null
  bcc?: string | null
  reply_to?: string | null
  subject: string
  body?: string | null
  content?: string | null
  status: 'sent' | 'failed' | 'queued' | 'pending' | string
  mailable_class?: string | null
  transport?: string | null
  error_message?: string | null
  ip_address?: string | null
  user_agent?: string | null
  attachments?: any[] | null
  headers?: any[] | Record<string, any> | null
  sent_at?: string | null
  created_at: string
  updated_at?: string
  sender?: {
    id: number
    name?: string
    full_name?: string
    email?: string
    [key: string]: any
  } | null
}
