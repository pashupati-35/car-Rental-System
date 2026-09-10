export interface BookingItem {
  id: number
  customer_id?: number
  car_id?: number
  start_date?: string
  end_date?: string
  total_price?: number | string
  status?: 'pending' | 'confirm' | 'confirmed' | 'completed' | 'cancel' | 'cancelled' | string
  created_at?: string
  customer?: {
    id: number
    name?: string
    full_name?: string
    email?: string
    phone_number?: string
    phone?: string
  }
  car?: {
    id: number
    car_name?: string
    brand?: string
    car_model?: string
    model?: string
    car_number?: string
    plate_number?: string
    image?: string
    price_per_day?: number | string
  }
  [key: string]: any
}
