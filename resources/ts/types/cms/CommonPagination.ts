export interface PaginationMeta {
  current_page: number
  from: number | null
  last_page: number
  per_page: number
  to: number | null
  total: number
}

export interface PaginationLinks {
  first: string | null
  last: string | null
  prev: string | null
  next: string | null
}

export interface PaginatedResponse<T> {
  data: T[]
  meta?: PaginationMeta
  links?: PaginationLinks
  current_page?: number
  last_page?: number
  per_page?: number
  total?: number
}

export interface BaseFilters {
  search?: string
  page?: number
  per_page?: number
  sort_by?: string
  sort_dir?: 'asc' | 'desc' | 'ASC' | 'DESC'
  is_active?: number | boolean
}

export interface ApiResponse<T = any> {
  status: string
  message?: string
  data?: T
}
