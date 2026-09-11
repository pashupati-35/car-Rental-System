import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface PageItem {
  id: number
  title: string
  slug?: string
  description?: string
  seo_title?: string
  seo_description?: string
  seo_keyword?: string
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export type PageFilters = BaseFilters

export type PageListResponse = PaginatedResponse<PageItem>

export interface PageDetailsResponse {
  status: string
  page: PageItem
}
