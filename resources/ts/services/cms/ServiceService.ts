import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { ServiceDetailsResponse, ServiceFilters, ServiceItem, ServiceListResponse } from '@/types/cms/ServiceType'

export default class ServiceService extends BaseAPIService {
  constructor() {
    super('admin/service')
  }

  async list(filters: ServiceFilters = {}) {
    return this.query<ServiceListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<ServiceDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<ServiceItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<ServiceItem>) {
    return this.post<ApiResponse>(`${id}/update`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
