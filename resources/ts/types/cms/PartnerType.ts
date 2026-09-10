import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface PartnerItem {
  id: number
  title: string
  slug?: string
  description?: string
  url?: string
  featured_photo?: string
  featured_photo_path?: { original?: string; thumb?: string }
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export interface PartnerFilters extends BaseFilters {}

export type PartnerListResponse = PaginatedResponse<PartnerItem>

export interface PartnerDetailsResponse {
  status: string
  partner: PartnerItem
}
