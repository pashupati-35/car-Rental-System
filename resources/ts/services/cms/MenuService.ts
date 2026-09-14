import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { MenuDetailsResponse, MenuFilters, MenuItemData, MenuListResponse } from '@/types/cms/MenuType'

export default class MenuService extends BaseAPIService {
  constructor() {
    super('admin/menu')
  }

  async list(filters: MenuFilters = {}) {
    return this.query<MenuListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<MenuDetailsResponse>(`${id}`)
  }

  async store(data: Partial<MenuItemData>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: Partial<MenuItemData>) {
    return this.put<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
