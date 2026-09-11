import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { CareerDetailsResponse, CareerFilters, CareerItem, CareerListResponse } from '@/types/cms/CareerType'

export default class CareerService extends BaseAPIService {
  constructor() {
    super('admin/career')
  }

  async list(filters: CareerFilters = {}) {
    return this.query<CareerListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<CareerDetailsResponse>(`${id}`)
  }

  async store(data: Partial<CareerItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: Partial<CareerItem>) {
    return this.put<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
