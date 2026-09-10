import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface AlbumItem {
  id: number
  title: string
  slug?: string
  cover_image?: string
  cover_image_path?: { original?: string; thumb?: string }
  description?: string
  event_date?: string
  tags?: string
  position?: number
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export interface AlbumFilters extends BaseFilters {}

export type AlbumListResponse = PaginatedResponse<AlbumItem>

export interface AlbumDetailsResponse {
  status: string
  album: AlbumItem
}
