export interface DriverItem {
  id: number
  name: string
  phone: string
  email?: string
  license_number?: string
  experience_years?: number | string
  status?: string
  owner_id?: number | string | null
  address?: string
  photo?: string
  license_photo?: string
  image?: string
  image_path?: { original?: string; thumb?: string }
  license_photo_url?: string
  created_at?: string
  updated_at?: string
  owner?: {
    id: number
    full_name?: string
    name?: string
    email?: string
  }
  cars_count?: number
  [key: string]: any
}
