import BaseAPIService from '@/services/BaseAPIService'
import type { ApiResponse } from '@/types/cms/CommonPagination'
import type { TeamDetailsResponse, TeamFilters, TeamItem, TeamListResponse } from '@/types/cms/TeamType'

export default class TeamService extends BaseAPIService {
  constructor() {
    super('admin/team')
  }

  async list(filters: TeamFilters = {}) {
    return this.query<TeamListResponse>('', { params: filters })
  }

  async getById(id: number | string) {
    return this.query<TeamDetailsResponse>(`${id}`)
  }

  async store(data: FormData | Partial<TeamItem>) {
    return this.post<ApiResponse>('', data)
  }

  async update(id: number | string, data: FormData | Partial<TeamItem>) {
    return this.post<ApiResponse>(`${id}`, data)
  }

  async deleteItem(id: number | string) {
    return this.delete<ApiResponse>(`${id}`)
  }

  async sort(ids: number[]) {
    return this.post<ApiResponse>('get/sort', { ids })
  }
}
