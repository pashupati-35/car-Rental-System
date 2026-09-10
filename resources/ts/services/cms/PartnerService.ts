import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { PartnerDetailsResponse, PartnerFilters, PartnerItem, PartnerListResponse } from '@/types/cms/PartnerType'

export default class PartnerService extends BaseAPIService {
  constructor() {
    super('admin/partner')
  }

  async list(filters: PartnerFilters = {}) {
    return this.query<PartnerListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<PartnerDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<PartnerItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<PartnerItem>) {
    return this.post<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }
}
