import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface EnquiryItem {
  id: number
  name: string
  slug?: string
  email?: string
  subject?: string
  message?: string
  phone?: string
  token?: string
  mark_as_read?: boolean
  created_at?: string
  updated_at?: string
}

export interface EnquiryFilters extends BaseFilters {
  mark_as_read?: boolean
}

export type EnquiryListResponse = PaginatedResponse<EnquiryItem>

export interface EnquiryDetailsResponse {
  status: string
  enquiry: EnquiryItem
}
