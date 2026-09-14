import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { PageDetailsResponse, PageFilters, PageItem, PageListResponse } from '@/types/cms/PageType'

export default class PageService extends BaseAPIService {
  constructor() {
    super('admin/page')
  }

  async list(filters: PageFilters = {}) {
    return this.query<PageListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<PageDetailsResponse>(`${id}`)
  }

  async store(data: Partial<PageItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: Partial<PageItem>) {
    return this.put<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }
}
