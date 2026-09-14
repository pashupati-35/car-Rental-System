export interface OwnerItem {
  id: number
  full_name: string
  name?: string
  email: string
  contact_number?: string
  phone?: string
  address?: string
  gender?: string
  status?: string
  cars_count?: number
  drivers_count?: number
  created_at?: string
  updated_at?: string
  [key: string]: any
}

export interface OwnerStats {
  total_cars: number
  verified_cars: number
  pending_cars: number
  rejected_cars: number
  total_drivers: number
  total_bookings: number
  confirmed_bookings: number
  total_revenue: number
}
