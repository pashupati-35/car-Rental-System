import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { MediaDetailsResponse, MediaFilters, MediaItem, MediaListResponse } from '@/types/cms/MediaType'

export default class MediaService extends BaseAPIService {
  constructor() {
    super('admin/media')
  }

  async list(filters: MediaFilters = {}) {
    return this.query<MediaListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<MediaDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<MediaItem>) {
    return this.post<ApiResponse>('', data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }
}
