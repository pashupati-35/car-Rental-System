import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { EnquiryDetailsResponse, EnquiryFilters, EnquiryItem, EnquiryListResponse } from '@/types/cms/EnquiryType'

export default class EnquiryService extends BaseAPIService {
  constructor() {
    super('admin/enquiry')
  }

  async list(filters: EnquiryFilters = {}) {
    return this.query<EnquiryListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<EnquiryDetailsResponse>(`${id}`)
  }

  async store(data: Partial<EnquiryItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: Partial<EnquiryItem>) {
    return this.put<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }
}
