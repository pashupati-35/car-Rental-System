import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { TestimonialDetailsResponse, TestimonialFilters, TestimonialItem, TestimonialListResponse } from '@/types/cms/TestimonialType'

export default class TestimonialService extends BaseAPIService {
  constructor() {
    super('admin/testimonial')
  }

  async list(filters: TestimonialFilters = {}) {
    return this.query<TestimonialListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<TestimonialDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<TestimonialItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<TestimonialItem>) {
    return this.post<ApiResponse>(`${id}/update`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
