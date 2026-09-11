import { computed, watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

export interface SiteSettings {
  logo?: string | null
  favicon?: string | null
  app_logo?: string | null
  footer_logo?: string | null
  login_bg_image?: string | null
  company_name?: string | null
  slogan?: string | null
  tagline?: string | null
  primary_color?: string | null
  secondary_color?: string | null
}

export function useSiteSettings() {
  const page = usePage()

  const siteSettings = computed<SiteSettings>(() => {
    return (page.props.siteSettings as SiteSettings) || {}
  })

  const logoUrl = computed(() => {
    return siteSettings.value?.logo || siteSettings.value?.app_logo || null
  })

  const footerLogoUrl = computed(() => {
    return siteSettings.value?.footer_logo || siteSettings.value?.logo || null
  })

  const faviconUrl = computed(() => {
    return siteSettings.value?.favicon || '/favicon.ico'
  })

  const companyName = computed(() => {
    return siteSettings.value?.company_name || 'Car Rental'
  })

  const updateFavicon = (url: string) => {
    if (typeof document === 'undefined' || !url) return
    let link: HTMLLinkElement | null = document.querySelector("link[rel*='icon']")
    if (!link) {
      link = document.createElement('link')
      link.rel = 'icon'
      document.head.appendChild(link)
    }
    link.href = url
  }

  const initSiteBranding = () => {
    if (typeof window === 'undefined') return
    if (faviconUrl.value) {
      updateFavicon(faviconUrl.value)
    }
  }

  watch(
    () => faviconUrl.value,
    (newFavicon: string | null) => {
      if (newFavicon) {
        updateFavicon(newFavicon)
      }
    },
    { immediate: true },
  )

  onMounted(() => {
    initSiteBranding()
  })

  return {
    siteSettings,
    logoUrl,
    footerLogoUrl,
    faviconUrl,
    companyName,
    updateFavicon,
    initSiteBranding,
  }
}
