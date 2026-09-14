import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { ContactDetailsResponse, ContactFilters, ContactItem, ContactListResponse } from '@/types/cms/ContactType'

export default class ContactService extends BaseAPIService {
  constructor() {
    super('admin/contact')
  }

  async list(filters: ContactFilters = {}) {
    return this.query<ContactListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<ContactDetailsResponse>(`${id}`)
  }

  async store(data: Partial<ContactItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: Partial<ContactItem>) {
    return this.put<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }
}
