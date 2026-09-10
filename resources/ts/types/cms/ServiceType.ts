import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface ServiceItem {
  id: number
  title: string
  slug?: string
  description?: string
  image?: string
  image_path?: { original?: string; thumb?: string }
  type?: string
  is_active?: number | boolean
  position?: number
  price?: number
  seo_title?: string
  seo_keyword?: string
  seo_description?: string
  social_share_image?: string
  created_at?: string
  updated_at?: string
}

export interface ServiceFilters extends BaseFilters {
  type?: string
}

export type ServiceListResponse = PaginatedResponse<ServiceItem>

export interface ServiceDetailsResponse {
  status: string
  service: ServiceItem
}
