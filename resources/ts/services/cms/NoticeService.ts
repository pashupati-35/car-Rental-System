import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { NoticeDetailsResponse, NoticeFilters, NoticeItem, NoticeListResponse } from '@/types/cms/NoticeType'

export default class NoticeService extends BaseAPIService {
  constructor() {
    super('admin/notice')
  }

  async list(filters: NoticeFilters = {}) {
    return this.query<NoticeListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<NoticeDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<NoticeItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<NoticeItem>) {
    return this.put<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
