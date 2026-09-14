import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { NewsAndUpdatesDetailsResponse, NewsAndUpdatesFilters, NewsAndUpdatesItem, NewsAndUpdatesListResponse } from '@/types/cms/NewsAndUpdatesType'

export default class NewsAndUpdatesService extends BaseAPIService {
  constructor() {
    super('admin/news-and-update')
  }

  async list(filters: NewsAndUpdatesFilters = {}) {
    return this.query<NewsAndUpdatesListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<NewsAndUpdatesDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<NewsAndUpdatesItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<NewsAndUpdatesItem>) {
    return this.post<ApiResponse>(`${id}/update`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }
}
