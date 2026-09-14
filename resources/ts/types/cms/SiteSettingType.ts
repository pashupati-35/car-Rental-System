export interface SiteSettingItem {
  id?: number
  company_name?: string
  slogan?: string
  tagline?: string
  description?: string
  terms_condition?: string
  mobile?: string
  phone?: string
  email?: string
  support_email?: string
  website?: string
  address?: string
  address_type?: string
  map_url?: string
  zoom_link?: string
  
  // Tax & Finance & Formats
  pan_no?: string
  vat_no?: string
  tax_percentage?: number | string
  date_format?: string

  // Social Links
  whatsapp?: string
  viber?: string
  pininterest?: string
  tiktok?: string
  linkedin?: string
  instagram?: string
  youtube?: string
  twitter?: string
  facebook?: string
  facebook_chat_widgets?: string
  
  // SEO & Analytics
  seo_title?: string
  seo_keyword?: string
  seo_description?: string
  google_analytics?: string
  pixels?: string
  recaptcha_site_key?: string
  recaptcha_secret_key?: string
  has_recaptcha_secret_key?: boolean

  // Notices & Legal
  copy_right_text?: string
  cookie_content_text?: string
  enable_cookies?: number | boolean
  is_admission_form_active?: number | boolean

  // Logos & Media
  logo?: string | File | null
  logo_path?: { original?: string; thumb?: string }
  app_logo?: string | File | null
  app_logo_path?: { original?: string; thumb?: string }
  footer_logo?: string | File | null
  footer_logo_path?: { original?: string; thumb?: string }
  email_logo_image?: string | File | null
  email_logo_path?: { original?: string; thumb?: string }
  fav_icon?: string | File | null
  fav_icon_path?: { original?: string; thumb?: string }
  login_bg_image?: string | File | null
  login_bg_path?: { original?: string; thumb?: string }
  login_bg_color?: string
  primary_color?: string
  secondary_color?: string
  colors_variables?: string

  // SMTP Mail Server
  display_smtp?: number | boolean
  mail_driver?: string
  mail_host?: string
  mail_port?: string | number
  mail_user_name?: string
  mail_password?: string
  has_mail_password?: boolean
  mail_encryption?: string
  mail_sender_name?: string
  mail_sender_address?: string

  // Storage
  display_storage?: number | boolean
  storage_type?: string
  storage_endpoint?: string
  storage_access_key?: string
  has_storage_access_key?: boolean
  storage_secret_key?: string
  has_storage_secret_key?: boolean
  storage_region?: string
  storage_bucket_name?: string
  storage_url?: string

  [key: string]: any
}

export interface SiteSettingResponse {
  status: string
  data: SiteSettingItem
}
