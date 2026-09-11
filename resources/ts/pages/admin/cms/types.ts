export interface CmsModuleMeta {
  id: string
  label: string
  icon: string
  color: string
}

export interface BaseCmsItem {
  id: number
  is_active?: number | boolean
  created_at?: string
  updated_at?: string
  [key: string]: any
}

export interface FaqItem extends BaseCmsItem {
  title: string
  description: string
  position?: number
}

export interface BlogItem extends BaseCmsItem {
  title: string
  slug?: string
  author_name?: string
  image?: string
  short_description?: string
  content?: string
}

export interface CareerItem extends BaseCmsItem {
  job_title: string
  department?: string
  location?: string
  employment_type?: string
  salary_range?: string
  description?: string
}

export interface TeamItem extends BaseCmsItem {
  name: string
  designation?: string
  email?: string
  phone?: string
  image?: string
  bio?: string
  facebook_url?: string
  twitter_url?: string
  linkedin_url?: string
}

export interface ServiceItem extends BaseCmsItem {
  title: string
  slug?: string
  price?: number | string
  image?: string
  short_description?: string
  description?: string
  icon?: string
}

export interface PopupItem extends BaseCmsItem {
  title: string
  popup_type?: string
  image?: string
  link_url?: string
  description?: string
}

export interface NoticeItem extends BaseCmsItem {
  title: string
  description?: string
  file_url?: string
  notice_date?: string
}

export interface NewsItem extends BaseCmsItem {
  title: string
  slug?: string
  published_date?: string
  image?: string
  description?: string
}

export interface SliderItem extends BaseCmsItem {
  title: string
  subtitle?: string
  image: string
  button_text?: string
  button_url?: string
  position?: number
}

export interface PageItem extends BaseCmsItem {
  title: string
  slug?: string
  content?: string
  banner_image?: string
}

export interface TestimonialItem extends BaseCmsItem {
  name: string
  designation?: string
  company_name?: string
  rating?: number | string
  description: string
  image?: string
}

export interface AlbumItem extends BaseCmsItem {
  title: string
  cover_image?: string
  description?: string
}

export interface MenuItem extends BaseCmsItem {
  title: string
  url?: string
  target?: string
  parent_id?: number | null
  position?: number
}

export interface PartnerItem extends BaseCmsItem {
  name: string
  logo?: string
  website_url?: string
  description?: string
}

export interface EnquiryItem extends BaseCmsItem {
  name: string
  email: string
  phone?: string
  subject?: string
  message?: string
  mark_as_read?: number | boolean
  is_read?: number | boolean
}

export interface ContactItem extends BaseCmsItem {
  name: string
  email: string
  phone?: string
  subject?: string
  message?: string
  mark_as_read?: number | boolean
  is_read?: number | boolean
}

export interface SiteSettingsItem extends BaseCmsItem {
  company_name?: string
  email?: string
  mobile?: string
  website?: string
  address?: string
  seo_title?: string
  meta_keywords?: string
  seo_description?: string
  facebook_url?: string
  instagram_url?: string
  twitter_url?: string
  linkedin_url?: string
  youtube_url?: string
}

export type CmsItem =
  | FaqItem
  | BlogItem
  | CareerItem
  | TeamItem
  | ServiceItem
  | PopupItem
  | NoticeItem
  | NewsItem
  | SliderItem
  | PageItem
  | TestimonialItem
  | AlbumItem
  | MenuItem
  | PartnerItem
  | EnquiryItem
  | ContactItem
  | SiteSettingsItem
  | BaseCmsItem

export interface PaginationLink {
  url: string | null
  label: string
  active: boolean
  page?: number
}
