import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface MediaItem {
  id: number
  title: string
  original_name?: string
  path?: string
  type?: string
  size?: number
  created_at?: string
  updated_at?: string
}

export interface MediaFilters extends BaseFilters {}

export type MediaListResponse = PaginatedResponse<MediaItem>

export interface MediaDetailsResponse {
  status: string
  data: MediaItem
}
