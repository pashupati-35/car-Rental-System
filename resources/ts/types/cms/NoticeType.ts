import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface NoticeItem {
  id: number
  title: string
  slug?: string
  description?: string
  file?: string
  position?: number
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export type NoticeFilters = BaseFilters

export type NoticeListResponse = PaginatedResponse<NoticeItem>

export interface NoticeDetailsResponse {
  status: string
  notice: NoticeItem
}
