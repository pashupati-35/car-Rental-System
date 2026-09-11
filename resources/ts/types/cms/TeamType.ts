import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface TeamItem {
  id: number
  title: string
  slug?: string
  position?: number
  image?: string
  image_path?: { original?: string; thumb?: string }
  description?: string
  job_title?: string
  fb_url?: string
  linked_url?: string
  whatsapp?: string
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export type TeamFilters = BaseFilters

export type TeamListResponse = PaginatedResponse<TeamItem>

export interface TeamDetailsResponse {
  status: string
  team: TeamItem
}
