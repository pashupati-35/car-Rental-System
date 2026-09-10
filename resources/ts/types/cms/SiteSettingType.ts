export interface SiteSettingItem {
  id?: number
  description?: string
  mobile?: string
  phone?: string
  email?: string
  support_email?: string
  website?: string
  address?: string
  whatsapp?: string
  viber?: string
  pininterest?: string
  tiktok?: string
  linkedin?: string
  instagram?: string
  youtube?: string
  twitter?: string
  facebook?: string
  copy_right_text?: string
  cookie_content_text?: string
  terms_condition?: string
  zoom_link?: string
  map_url?: string
  tagline?: string
  logo?: string
  logo_path?: { original?: string; thumb?: string }
  email_logo_image?: string
  email_logo_path?: { original?: string; thumb?: string }
  app_logo?: string
  app_logo_path?: { original?: string; thumb?: string }
  enable_cookies?: number
  fav_icon?: string
  fav_icon_path?: { original?: string; thumb?: string }
  slogan?: string
  login_bg_image?: string
  login_bg_path?: { original?: string; thumb?: string }
  login_bg_color?: string
  primary_color?: string
  secondary_color?: string
  seo_title?: string
  seo_keyword?: string
  seo_description?: string
}

export interface SiteSettingResponse {
  status: string
  data: SiteSettingItem
}
