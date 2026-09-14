import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface SliderItem {
  id: number
  title: string
  slug?: string
  slider_type_id?: number
  description?: string
  image?: string
  image_path?: { original?: string; thumb?: string }
  link?: string
  position?: number
  new_tab?: number
  heading_text?: string
  sub_heading_text?: string
  button_text?: string
  show_button?: number
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export interface SliderFilters extends BaseFilters {
  slider_type_id?: number
}

export type SliderListResponse = PaginatedResponse<SliderItem>

export interface SliderDetailsResponse {
  status: string
  slider: SliderItem
}
