import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { FaqDetailsResponse, FaqFilters, FaqItem, FaqListResponse } from '@/types/cms/FaqType'

export default class FaqService extends BaseAPIService {
  constructor() {
    super('admin/faq')
  }

  async list(filters: FaqFilters = {}) {
    return this.query<FaqListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<FaqDetailsResponse>(`${id}`)
  }

  async store(data: Partial<FaqItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: Partial<FaqItem>) {
    return this.put<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
