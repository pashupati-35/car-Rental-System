import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface ContactItem {
  id: number
  type?: string
  first_name: string
  last_name?: string
  phone?: string
  email?: string
  subject?: string
  message?: string
  is_read?: boolean
  replied?: boolean
  is_active?: boolean
  created_at?: string
  updated_at?: string
}

export interface ContactFilters extends BaseFilters {
  is_read?: boolean
}

export type ContactListResponse = PaginatedResponse<ContactItem>

export interface ContactDetailsResponse {
  status: string
  contact: ContactItem
}
