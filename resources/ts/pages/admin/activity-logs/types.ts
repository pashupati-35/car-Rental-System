export interface ActivityLogItem {
  id: number
  log_type: string
  description?: string
  causer_type?: string | null
  causer_id?: number | null
  causer_name?: string
  causer_role?: string
  subject_type?: string | null
  subject_id?: number | null
  table_name?: string | null
  ip_address?: string | null
  user_agent?: string | null
  properties?: Record<string, any> | null
  created_at: string
  updated_at?: string
  causer?: {
    id: number
    name?: string
    full_name?: string
    email?: string
    [key: string]: any
  } | null
  subject?: any
}
