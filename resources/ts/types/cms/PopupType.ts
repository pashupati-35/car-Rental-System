import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface PopupItem {
  id: number
  title: string
  slug?: string
  description?: string
  link?: string
  type?: string
  video_url?: string
  location?: string
  show_location?: number
  image?: string
  image_path?: { original?: string; thumb?: string }
  start_date?: string
  end_date?: string
  position?: number
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export interface PopupFilters extends BaseFilters {
  type?: string
}

export type PopupListResponse = PaginatedResponse<PopupItem>

export interface PopupDetailsResponse {
  status: string
  popup: PopupItem
}
