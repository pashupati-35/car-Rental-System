export interface CarItem {
  id: number
  car_name: string
  car_model?: string
  brand?: string
  model?: string
  car_number?: string
  plate_number?: string
  owner_id?: number
  driver_id?: number | null
  price_per_day?: number | string
  rental_price?: number | string
  seating_capacity?: number | string
  fuel_type?: string
  transmission?: string
  description?: string
  image?: string
  status?: 'pending' | 'verified' | 'available' | 'rejected' | string
  available?: 'yes' | 'no' | string
  created_at?: string
  updated_at?: string
  owner?: {
    id: number
    full_name?: string
    name?: string
    email?: string
    contact_number?: string
  }
  driver?: {
    id: number
    name?: string
    phone?: string
  }
  [key: string]: any
}
