import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface CareerItem {
  id: number
  title: string
  slug?: string
  position?: number
  description?: string
  opened_at?: string
  expiry_date?: string
  employment_type?: string
  min_qualification?: string
  salary_offer?: string
  no_of_vacancies?: number
  seo_title?: string
  seo_description?: string
  seo_keywords?: string
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
}

export interface CareerFilters extends BaseFilters {
  employment_type?: string
}

export type CareerListResponse = PaginatedResponse<CareerItem>

export interface CareerDetailsResponse {
  status: string
  career: CareerItem
}
