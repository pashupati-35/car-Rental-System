import type { BaseFilters, PaginatedResponse } from './CommonPagination'

export interface MenuItemData {
  id: number
  title: string
  menu_type?: string
  header?: string
  position?: number
  is_active?: number | boolean
  items?: any[]
  created_at?: string
  updated_at?: string
}

export interface MenuFilters extends BaseFilters {
  menu_type?: string
}

export type MenuListResponse = PaginatedResponse<MenuItemData>

export interface MenuDetailsResponse {
  status: string
  menu: MenuItemData
}
