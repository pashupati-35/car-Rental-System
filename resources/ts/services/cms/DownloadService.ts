import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { DownloadDetailsResponse, DownloadFilters, DownloadItem, DownloadListResponse } from '@/types/cms/DownloadType'

export default class DownloadService extends BaseAPIService {
  constructor() {
    super('admin/download')
  }

  async list(filters: DownloadFilters = {}) {
    return this.query<DownloadListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<DownloadDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<DownloadItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<DownloadItem>) {
    return this.post<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
