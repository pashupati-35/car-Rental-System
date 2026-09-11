import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { SiteSettingItem, SiteSettingResponse } from '@/types/cms/SiteSettingType'

export default class SiteSettingService extends BaseAPIService {
  constructor() {
    super('admin/site-setting')
  }

  async getSettings() {
    return this.query<SiteSettingResponse>('get/all')
  }

  async updateSettings(id: number | string, data: FormData | Partial<SiteSettingItem>) {
    return this.post<ApiResponse>(`${id}/update`, data)
  }

  async sendTestEmail(email: string) {
    return this.post<ApiResponse>('test-email', { email })
  }

  async testS3() {
    return this.query<ApiResponse>('test/s3')
  }
}
