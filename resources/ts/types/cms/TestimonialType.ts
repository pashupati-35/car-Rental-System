import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface TestimonialItem {
  id: number
  title: string
  name: string
  description?: string
  type?: string
  job_title?: string
  image?: string
  image_path?: { original?: string; thumb?: string }
  rating?: number
  status?: string
  position?: number
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export interface TestimonialFilters extends BaseFilters {}

export type TestimonialListResponse = PaginatedResponse<TestimonialItem>

export interface TestimonialDetailsResponse {
  status: string
  testimonial: TestimonialItem
}
