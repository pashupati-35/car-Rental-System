import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { BlogDetailsResponse, BlogFilters, BlogItem, BlogListResponse } from '@/types/cms/BlogType'

export default class BlogService extends BaseAPIService {
  constructor() {
    super('admin/blog')
  }

  async list(filters: BlogFilters = {}) {
    return this.query<BlogListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<BlogDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<BlogItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<BlogItem>) {
    return this.post<ApiResponse>(`${id}/update`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }
}
