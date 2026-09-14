import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { PopupDetailsResponse, PopupFilters, PopupItem, PopupListResponse } from '@/types/cms/PopupType'

export default class PopupService extends BaseAPIService {
  constructor() {
    super('admin/popup')
  }

  async list(filters: PopupFilters = {}) {
    return this.query<PopupListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<PopupDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<PopupItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<PopupItem>) {
    return this.post<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }
}
