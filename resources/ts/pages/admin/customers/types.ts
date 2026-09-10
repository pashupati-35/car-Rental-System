export interface CustomerItem {
  id: number
  name: string
  full_name?: string
  email: string
  phone_number?: string
  phone?: string
  address?: string
  gender?: string
  created_at?: string
  updated_at?: string
  bookings_count?: number
  [key: string]: any
}

export interface CustomerStats {
  total_bookings: number
  confirmed_bookings: number
  completed_bookings: number
  cancelled_bookings: number
  pending_bookings: number
  total_spent: number
  total_payments: number
}

export interface CustomerBookingItem {
  id: number
  customer_id: number
  car_id: number
  pick_up_date: string
  last_date: string
  pickup_location: string
  drop_location: string
  total_price: number | string
  status: string
  purpose?: string
  other_purpose?: string
  created_at?: string
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
    driver?: {
      id: number
      name: string
      phone: string
    }
    owner?: {
      id: number
      full_name?: string
      name?: string
    }
  }
  payment?: {
    id: number
    amount: number | string
    card_number?: string
    created_at?: string
  }
}

export interface CustomerPaymentItem {
  id: number
  customer_id: number
  booking_id: number
  car_id: number
  amount: number | string
  card_number?: string
  expiry_date?: string
  created_at?: string
  car?: {
    id: number
    car_name?: string
    brand?: string
    car_model?: string
  }
  booking?: {
    id: number
    pickup_location?: string
    drop_location?: string
    status?: string
  }
}
