import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface DownloadItem {
  id: number
  title: string
  slug?: string
  preview_image?: string
  preview_image_path?: { original?: string; thumb?: string }
  description?: string
  download_type_id?: number
  position?: number
  file?: string
  file_path?: string
  type?: string
  is_private?: number
  public_hidden?: number
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export interface DownloadFilters extends BaseFilters {
  download_type_id?: number
}

export type DownloadListResponse = PaginatedResponse<DownloadItem>

export interface DownloadDetailsResponse {
  status: string
  download: DownloadItem
}
