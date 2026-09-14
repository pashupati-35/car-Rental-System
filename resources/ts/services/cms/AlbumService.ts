import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { AlbumDetailsResponse, AlbumFilters, AlbumItem, AlbumListResponse } from '@/types/cms/AlbumType'

export default class AlbumService extends BaseAPIService {
  constructor() {
    super('admin/album')
  }

  async list(filters: AlbumFilters = {}) {
    return this.query<AlbumListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<AlbumDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<AlbumItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<AlbumItem>) {
    return this.post<ApiResponse>(`${id}/update`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('sort', { ids })
  }
}
