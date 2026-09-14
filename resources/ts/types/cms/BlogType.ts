import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface BlogItem {
  id: number
  title: string
  slug?: string
  content?: string
  image?: string
  image_path?: { original?: string; thumb?: string }
  author_name?: string
  author_image?: string
  category_id?: number
  publish_date?: string
  type?: 'blog' | 'news' | 'event' | string
  is_active?: number | boolean
  event_date?: string
  event_end?: string
  meta_title?: string
  meta_description?: string
  social_share_image?: string
  created_at?: string
  updated_at?: string
}

export interface BlogFilters extends BaseFilters {
  title?: string
  type?: string
  category_id?: number
  publish_date_from?: string
  publish_date_to?: string
  filter_by?: string
}

export type BlogListResponse = PaginatedResponse<BlogItem>

export interface BlogDetailsResponse {
  status: string
  blog: BlogItem
}
