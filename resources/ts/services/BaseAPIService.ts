import axios, { type AxiosInstance, type AxiosRequestConfig, type AxiosResponse } from 'axios'

export default class BaseAPIService {
  protected api: AxiosInstance
  protected prefix: string

  constructor(prefix = '') {
    this.prefix = prefix.replace(/^\/+|\/+$/g, '')
    this.api = axios.create({
      baseURL: '/',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    })
  }

  protected buildUrl(endpoint = ''): string {
    const cleanEndpoint = endpoint.replace(/^\/+/, '')
    if (!this.prefix) {
      return `/${cleanEndpoint}`
    }
    if (!cleanEndpoint) {
      return `/${this.prefix}`
    }
    return `/${this.prefix}/${cleanEndpoint}`
  }

  async query<T>(endpoint = '', config: AxiosRequestConfig = {}): Promise<T> {
    const url = this.buildUrl(endpoint)
    const response: AxiosResponse<T> = await this.api.get(url, config)
    return response.data
  }

  async get<T>(endpoint = '', config: AxiosRequestConfig = {}): Promise<T> {
    return this.query<T>(endpoint, config)
  }

  async post<T>(endpoint = '', data: any = {}, config: AxiosRequestConfig = {}): Promise<T> {
    const url = this.buildUrl(endpoint)
    const response: AxiosResponse<T> = await this.api.post(url, data, config)
    return response.data
  }

  async put<T>(endpoint = '', data: any = {}, config: AxiosRequestConfig = {}): Promise<T> {
    const url = this.buildUrl(endpoint)
    const response: AxiosResponse<T> = await this.api.put(url, data, config)
    return response.data
  }

  async patch<T>(endpoint = '', data: any = {}, config: AxiosRequestConfig = {}): Promise<T> {
    const url = this.buildUrl(endpoint)
    const response: AxiosResponse<T> = await this.api.patch(url, data, config)
    return response.data
  }

  async delete<T>(endpoint = '', config: AxiosRequestConfig = {}): Promise<T> {
    const url = this.buildUrl(endpoint)
    const response: AxiosResponse<T> = await this.api.delete(url, config)
    return response.data
  }
}
