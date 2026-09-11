import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { SliderDetailsResponse, SliderFilters, SliderItem, SliderListResponse } from '@/types/cms/SliderType'

export default class SliderService extends BaseAPIService {
  constructor() {
    super('admin/slider')
  }

  async list(filters: SliderFilters = {}) {
    return this.query<SliderListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<SliderDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<SliderItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<SliderItem>) {
    return this.post<ApiResponse>(`${id}/update`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
