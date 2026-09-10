import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface NewsAndUpdatesItem {
  id: number
  title: string
  slug?: string
  description?: string
  image?: string
  published_date?: string
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export interface NewsAndUpdatesFilters extends BaseFilters {}

export type NewsAndUpdatesListResponse = PaginatedResponse<NewsAndUpdatesItem>

export interface NewsAndUpdatesDetailsResponse {
  status: string
  news: NewsAndUpdatesItem
}
