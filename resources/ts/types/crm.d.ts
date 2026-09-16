export interface CrmLead {
  id: number
  first_name: string
  last_name?: string
  full_name: string
  company_name?: string
  email?: string
  phone?: string
  source: 'website' | 'phone' | 'walk_in' | 'referral' | 'corporate' | 'ai_chat' | 'other'
  status: 'new' | 'contacted' | 'qualified' | 'proposal_sent' | 'converted' | 'lost'
  priority: 'low' | 'medium' | 'high' | 'urgent'
  estimated_value: number | string
  interested_car_id?: number
  interested_car?: any
  pickup_date?: string
  return_date?: string
  notes?: string
  converted_customer_id?: number
  converted_customer?: any
  converted_at?: string
  assigned_admin_id?: number
  assigned_admin?: any
  deals?: CrmDeal[]
  quotations?: CrmQuotation[]
  created_at: string
  updated_at: string
}

export interface CrmDeal {
  id: number
  deal_number: string
  title: string
  lead_id?: number
  lead?: CrmLead
  customer_id?: number
  customer?: any
  corporate_account_id?: number
  corporate_account?: CrmCorporateAccount
  car_id?: number
  car?: any
  stage: 'lead_in' | 'needs_analysis' | 'vehicle_proposed' | 'negotiation' | 'won' | 'lost'
  value: number | string
  win_probability: number
  expected_close_date?: string
  loss_reason?: string
  notes?: string
  assigned_admin_id?: number
  assigned_admin?: any
  created_at: string
  updated_at: string
}

export interface CrmCustomerInteraction {
  id: number
  customer_id: number
  admin_id?: number
  admin?: any
  type: 'call' | 'email' | 'meeting' | 'note' | 'whatsapp' | 'sms'
  subject: string
  details: string
  interaction_date: string
  created_at: string
  updated_at: string
}

export interface CrmCustomerPreference {
  id: number
  customer_id: number
  preferred_car_type?: string
  preferred_transmission?: 'Automatic' | 'Manual'
  preferred_fuel_type?: 'Petrol' | 'Diesel' | 'Electric' | 'Hybrid'
  needs_child_seat: boolean
  needs_chauffeur: boolean
  vip_status: boolean
  loyalty_tier: 'Standard' | 'Silver' | 'Gold' | 'Platinum'
  special_requests?: string
}

export interface CrmTask {
  id: number
  title: string
  description?: string
  related_type?: string
  related_id?: number
  due_date?: string
  priority: 'low' | 'medium' | 'high' | 'urgent'
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled'
  assigned_admin_id?: number
  completed_at?: string
  created_at: string
}

export interface CrmQuotationItem {
  id?: number
  quotation_id?: number
  description: string
  quantity: number
  unit_price: number | string
  total_price: number | string
}

export interface CrmQuotation {
  id: number
  quotation_number: string
  customer_id?: number
  customer?: any
  lead_id?: number
  lead?: CrmLead
  car_id?: number
  car?: any
  start_date: string
  end_date: string
  days_count: number
  daily_rate: number | string
  subtotal: number | string
  tax_rate: number | string
  tax_amount: number | string
  discount_amount: number | string
  total_amount: number | string
  status: 'draft' | 'sent' | 'accepted' | 'rejected' | 'expired'
  valid_until?: string
  terms_conditions?: string
  notes?: string
  created_by?: number
  creator?: any
  items?: CrmQuotationItem[]
  created_at: string
  updated_at: string
}

export interface CrmSupportTicketMessage {
  id: number
  ticket_id: number
  sender_type: 'admin' | 'customer'
  sender_id?: number
  sender_name?: string
  message: string
  attachments?: any
  created_at: string
}

export interface CrmSupportTicket {
  id: number
  ticket_number: string
  customer_id?: number
  customer?: any
  car_id?: number
  car?: any
  booking_id?: number
  booking?: any
  subject: string
  category: 'roadside_assistance' | 'billing' | 'extension' | 'vehicle_complaint' | 'general'
  priority: 'low' | 'medium' | 'high' | 'urgent'
  status: 'open' | 'in_progress' | 'waiting_customer' | 'resolved' | 'closed'
  assigned_admin_id?: number
  assigned_admin?: any
  resolved_at?: string
  messages?: CrmSupportTicketMessage[]
  created_at: string
  updated_at: string
}

export interface CrmCorporateAccount {
  id: number
  company_name: string
  business_reg_number?: string
  tax_id?: string
  contact_person: string
  email: string
  phone?: string
  address?: string
  credit_limit: number | string
  contract_discount_percent: number | string
  payment_terms: string
  status: 'active' | 'pending' | 'suspended'
  notes?: string
  assigned_admin_id?: number
  assigned_admin?: any
  created_at: string
  updated_at: string
}
