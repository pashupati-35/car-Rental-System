import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface FaqItem {
  id: number
  title: string
  short_description?: string
  tags?: string
  description?: string
  seo_title?: string
  seo_description?: string
  seo_keyword?: string
  position?: number
  is_active?: number | boolean
  faq_category_id?: number
  category?: { id: number; title: string }
  created_at?: string
  updated_at?: string
}

export interface FaqFilters extends BaseFilters {
  faq_category_id?: number
}

export type FaqListResponse = PaginatedResponse<FaqItem>

export interface FaqDetailsResponse {
  status: string
  faq: FaqItem
}
