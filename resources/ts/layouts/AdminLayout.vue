<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import MessageBox from '@/components/MessageBox.vue'
import { useAdminTheme, type AdminThemeStyle } from '@/composable/useAdminTheme'

const page = usePage()
const { theme, setTheme, initTheme } = useAdminTheme()

const isMobileDrawerOpen = ref(false)
const showProfileMenu = ref(false)
const showCmsMenu = ref(false)
const showThemeMenu = ref(false)
const isMobileCmsExpanded = ref(false)

const auth = computed(() => page.props.auth as any)
const admin = computed(() => auth.value?.admin || auth.value?.user)

const displayName = computed(() => {
  return admin.value?.name || admin.value?.full_name || 'System Admin'
})

const adminEmail = computed(() => {
  return admin.value?.email || 'admin@autorent.com'
})

const flashSuccess = ref('')
const flashError = ref('')

watch(
  () => page.props.flash as any,
  (newFlash: any) => {
    if (newFlash?.success) {
      flashSuccess.value = newFlash.success
    }
    if (newFlash?.error) {
      flashError.value = newFlash.error
    }
  },
  { immediate: true, deep: true },
)

const currentUrl = computed(() => page.url)

const liveSiteUrl = computed(() => {
  if (typeof window !== 'undefined') {
    const host = window.location.host
    const protocol = window.location.protocol
    const publicHost = host.replace(/^portal\./, '')
    
    return `${protocol}//${publicHost}/`
  }
  
  return '/'
})

const adminCounts = computed(() => (page.props.adminCounts as any) || {})

const adminNav = computed(() => [
  { title: 'Master Dashboard', icon: 'ri-dashboard-3-line', href: '/admin/dashboard', badge: '' },
  { 
    title: 'Fleet Cars Verification', 
    icon: 'ri-car-line', 
    href: '/admin/cars', 
    badge: adminCounts.value.pendingCars ? `${adminCounts.value.pendingCars} Pend` : (adminCounts.value.totalCars ? String(adminCounts.value.totalCars) : ''), 
  },
  { 
    title: 'Driver Directory', 
    icon: 'ri-user-star-line', 
    href: '/admin/drivers', 
    badge: adminCounts.value.totalDrivers ? String(adminCounts.value.totalDrivers) : '', 
  },
  { 
    title: 'Rental Bookings', 
    icon: 'ri-calendar-check-line', 
    href: '/admin/booked-cars', 
    badge: adminCounts.value.pendingBookings ? `${adminCounts.value.pendingBookings} Pend` : (adminCounts.value.totalBookings ? String(adminCounts.value.totalBookings) : ''), 
  },
  { 
    title: 'Fleet Owners', 
    icon: 'ri-building-line', 
    href: '/admin/owners', 
    badge: adminCounts.value.totalOwners ? String(adminCounts.value.totalOwners) : '', 
  },
  { 
    title: 'Customers', 
    icon: 'ri-user-smile-line', 
    href: '/admin/customers', 
    badge: adminCounts.value.totalCustomers ? String(adminCounts.value.totalCustomers) : '', 
  },
  { title: 'Master CMS Suite', icon: 'ri-layout-masonry-line', href: '/admin/cms', badge: '16' },
  { 
    title: 'Email Templates', 
    icon: 'ri-mail-settings-line', 
    href: '/admin/email-templates', 
    badge: adminCounts.value.totalEmailTemplates ? String(adminCounts.value.totalEmailTemplates) : '', 
  },
  { 
    title: 'Activity Logs', 
    icon: 'ri-history-line', 
    href: '/admin/activity-logs', 
    badge: '', 
  },
  { 
    title: 'Email Logs', 
    icon: 'ri-mail-check-line', 
    href: '/admin/email-logs', 
    badge: '', 
  },
  { title: 'Admin Profile', icon: 'ri-user-settings-line', href: '/admin/profile', badge: '' },
  { title: 'Account Security & MFA', icon: 'ri-shield-keyhole-line', href: '/admin/security', badge: '' },
])

const cmsQuickLinks = [
  { label: 'FAQ', href: '/admin/cms?module=faqs', icon: 'ri-question-line' },
  { label: 'Blog', href: '/admin/cms?module=blogs', icon: 'ri-article-line' },
  { label: 'Career', href: '/admin/cms?module=careers', icon: 'ri-briefcase-line' },
  { label: 'Teams', href: '/admin/cms?module=teams', icon: 'ri-team-line' },
  { label: 'Our service', href: '/admin/cms?module=services', icon: 'ri-heart-line' },
  { label: 'Popup', href: '/admin/cms?module=popups', icon: 'ri-window-line' },
  { label: 'Notices', href: '/admin/cms?module=notices', icon: 'ri-notification-3-line' },
  { label: 'News and updates', href: '/admin/cms?module=news', icon: 'ri-newspaper-line' },
  { label: 'Sliders', href: '/admin/cms?module=sliders', icon: 'ri-slideshow-3-line' },
  { label: 'Pages', href: '/admin/cms?module=pages', icon: 'ri-file-list-3-line' },
  { label: 'Testimonials', href: '/admin/cms?module=testimonials', icon: 'ri-star-line' },
  { label: 'Album', href: '/admin/cms?module=albums', icon: 'ri-image-line' },
  { label: 'Menu', href: '/admin/cms?module=menus', icon: 'ri-menu-line' },
  { label: 'Partners', href: '/admin/cms?module=partners', icon: 'ri-hand-heart-line' },
  { label: 'Enquiries', href: '/admin/cms?module=enquiries', icon: 'ri-mail-unread-line' },
  { label: 'Contacts', href: '/admin/cms?module=contacts', icon: 'ri-contacts-book-line' },
  { label: 'Site Settings & SEO', href: '/admin/cms?module=site-settings', icon: 'ri-settings-4-line' },
]

const isActive = (href: string) => {
  const current = currentUrl.value.split('?')[0]
  if (href === '/admin/dashboard') {
    return current === '/admin/dashboard' || current === '/admin'
  }
  if (href === '/admin/profile') {
    return current === '/admin/profile'
  }
  if (href === '/admin/security') {
    return current === '/admin/security'
  }
  if (href === '/cars') {
    return current === '/cars' || current.startsWith('/cars/')
  }
  if (href === '/car-calendar') {
    return current.startsWith('/car-calendar')
  }
  
  return current.startsWith(href)
}

const logout = () => {
  router.post('/admin/logout')
}

const themeOptions: { value: AdminThemeStyle; label: string; icon: string }[] = [
  { value: 'dark', label: 'Dark Mode', icon: 'ri-moon-clear-line' },
  { value: 'midnight', label: 'Midnight Obsidian', icon: 'ri-sparkling-2-line' },
  { value: 'light', label: 'Light Mode', icon: 'ri-sun-line' },
  { value: 'system', label: 'System Default', icon: 'ri-computer-line' },
]

const currentThemeIcon = computed(() => {
  if (theme.value === 'light') return 'ri-sun-line text-amber-500'
  if (theme.value === 'midnight') return 'ri-sparkling-2-line text-purple-400'
  if (theme.value === 'system') return 'ri-computer-line text-blue-400'
  
  return 'ri-moon-clear-line text-indigo-400'
})

const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('#admin-profile-dropdown-container')) {
    showProfileMenu.value = false
  }
  if (!target.closest('#admin-cms-dropdown-container')) {
    showCmsMenu.value = false
  }
  if (!target.closest('#admin-theme-dropdown-container')) {
    showThemeMenu.value = false
  }
}

onMounted(() => {
  initTheme()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="min-h-screen bg-slate-100/70 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col font-sans selection:bg-indigo-500 selection:text-white antialiased transition-colors duration-200">
    <!-- Top Modern Full-Width Admin Header Bar -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="w-full px-3 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 h-16 flex items-center justify-between gap-3">
        <!-- Left: Mobile Drawer Hamburger Button + Logo & Admin Portal Badge -->
        <div class="flex items-center gap-2.5 sm:gap-3.5">
          <!-- Mobile Drawer Hamburger Button -->
          <button
            type="button"
            class="lg:hidden p-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer"
            aria-label="Open Navigation Menu"
            @click="isMobileDrawerOpen = true"
          >
            <i class="ri-menu-2-line text-2xl" />
          </button>

          <Link
            href="/admin/dashboard"
            class="flex items-center gap-2.5 group"
          >
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-800 flex items-center justify-center text-white font-black text-lg shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
              CR
            </div>
            <span class="font-black text-lg tracking-tight text-slate-900 dark:text-white hidden sm:inline font-sans">
              AutoRent
            </span>
          </Link>

          <span class="bg-indigo-50 text-indigo-700 border border-indigo-200/80 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800 text-[11px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider shrink-0 font-sans">
            Admin Portal
          </span>
        </div>

        <!-- Center: Quick Nav & CMS Dropdown Menu -->
        <div class="hidden md:flex items-center gap-1.5 lg:gap-2">
          <!-- CMS Dropdown -->
          <div
            id="admin-cms-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              :class="(currentUrl.startsWith('/admin/cms') || currentUrl.startsWith('/admin/cars')) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
              class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] flex items-center gap-1.5 transition-colors cursor-pointer font-sans"
              @click="showCmsMenu = !showCmsMenu; showProfileMenu = false; showThemeMenu = false"
            >
              <i class="ri-layout-masonry-line text-base text-indigo-600 dark:text-indigo-400" />
              <span>CMS & Fleet</span>
              <i
                class="ri-arrow-down-s-line text-xs transition-transform"
                :class="showCmsMenu ? 'rotate-180' : ''"
              />
            </button>

            <!-- CMS Dropdown Menu -->
            <div
              v-if="showCmsMenu"
              class="absolute left-0 mt-2 w-72 max-h-[80vh] overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-2 z-50 space-y-1 animate-in fade-in slide-in-from-top-2 duration-150"
            >
              <div class="px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                Master CMS Suite (16 Modules)
              </div>
              <div class="grid grid-cols-1 gap-0.5">
                <Link
                  v-for="item in cmsQuickLinks"
                  :key="item.label"
                  :href="item.href"
                  class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800 transition-colors"
                  @click="showCmsMenu = false"
                >
                  <i
                    :class="item.icon"
                    class="text-base text-indigo-600 dark:text-indigo-400"
                  />
                  <span>{{ item.label }}</span>
                </Link>
              </div>
            </div>
          </div>

          <Link
            href="/admin/dashboard"
            :class="isActive('/admin/dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Dashboard
          </Link>
          <Link
            href="/admin/cars"
            :class="isActive('/admin/cars') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors flex items-center gap-1.5 font-sans"
          >
            <span>Browse Fleet</span>
            <span
              v-if="adminCounts.totalCars"
              class="px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 font-bold"
            >
              {{ adminCounts.totalCars }}
            </span>
          </Link>
          <Link
            href="/car-calendar"
            :class="isActive('/car-calendar') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Calendar
          </Link>
        </div>

        <!-- Right Side: Theme Switcher, Live Site & Profile Dropdown (Right side of header) -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Theme Switcher Dropdown in Header -->
          <div
            id="admin-theme-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="p-2 sm:px-3 sm:py-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-colors inline-flex items-center gap-1.5 cursor-pointer focus:outline-none"
              title="Change Theme Style"
              @click="showThemeMenu = !showThemeMenu; showProfileMenu = false; showCmsMenu = false"
            >
              <i
                :class="currentThemeIcon"
                class="text-base"
              />
              <span class="hidden sm:inline capitalize">{{ theme }}</span>
              <i
                class="ri-arrow-down-s-line text-xs transition-transform"
                :class="showThemeMenu ? 'rotate-180' : ''"
              />
            </button>

            <div
              v-if="showThemeMenu"
              class="absolute right-0 mt-2 w-44 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-1.5 z-50 space-y-1 animate-in fade-in slide-in-from-top-2 duration-150"
            >
              <div class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-400">
                Theme Appearance
              </div>
              <button
                v-for="opt in themeOptions"
                :key="opt.value"
                type="button"
                :class="theme === opt.value ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/80 dark:text-indigo-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium'"
                class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-xs transition-colors cursor-pointer text-left"
                @click="setTheme(opt.value); showThemeMenu = false"
              >
                <div class="flex items-center gap-2">
                  <i
                    :class="opt.icon"
                    class="text-sm"
                  />
                  <span>{{ opt.label }}</span>
                </div>
                <i
                  v-if="theme === opt.value"
                  class="ri-check-line text-indigo-600 dark:text-indigo-400 text-xs font-bold"
                />
              </button>
            </div>
          </div>

          <a
            :href="liveSiteUrl"
            target="_blank"
            rel="noopener noreferrer"
            title="Open Public Website in New Tab"
            class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 text-sm transition-colors hidden sm:inline-flex items-center gap-1.5 font-sans"
          >
            <i class="ri-external-link-line text-xs" />
            <span class="text-xs sm:text-[13px] font-semibold">Live Site</span>
          </a>

          <!-- Profile Dropdown Container on Right Side of Header -->
          <div
            id="admin-profile-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="flex items-center gap-2.5 p-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer"
              @click="showProfileMenu = !showProfileMenu; showCmsMenu = false; showThemeMenu = false"
            >
              <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                {{ (displayName || 'A')[0].toUpperCase() }}
              </div>
              <div
                class="text-left hidden lg:block"
                style="padding-right: 0.25rem"
              >
                <span class="text-xs sm:text-[13px] font-bold text-slate-800 dark:text-slate-100 block leading-tight truncate max-w-[130px] font-sans">
                  {{ displayName }}
                </span>
                <span class="text-[10px] font-medium text-slate-400 block leading-none font-sans">
                  Super Admin
                </span>
              </div>
              <i
                class="ri-arrow-down-s-line text-xs text-slate-400 transition-transform"
                :class="showProfileMenu ? 'rotate-180' : ''"
              />
            </button>

            <!-- Profile Popup Menu -->
            <div
              v-if="showProfileMenu"
              class="absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-2 z-50 space-y-2 animate-in fade-in slide-in-from-top-2 duration-150 font-sans"
            >
              <!-- User Info Header -->
              <div class="px-3 py-2 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-center space-y-0.5">
                <div class="flex items-center justify-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-500" />
                  <span class="text-xs font-bold text-slate-900 dark:text-white">{{ displayName }}</span>
                </div>
                <span class="text-[11px] text-slate-500 block truncate">{{ adminEmail }}</span>
              </div>

              <!-- Menu Items -->
              <div class="space-y-1">
                <Link
                  href="/admin/profile"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-user-3-line text-base text-slate-500" />
                  <span>Admin Profile</span>
                </Link>

                <Link
                  href="/admin/security"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-shield-keyhole-line text-base text-indigo-600" />
                  <span>Account Security & MFA</span>
                </Link>

                <Link
                  href="/admin/dashboard"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-dashboard-line text-base text-blue-600" />
                  <span>Master Dashboard</span>
                </Link>
              </div>

              <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                <button
                  type="button"
                  class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 font-semibold text-xs sm:text-[13px] transition-colors cursor-pointer"
                  @click="logout"
                >
                  <span>Logout</span>
                  <i class="ri-logout-box-r-line" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Mobile Navigation Drawer Slide-Over (Gray-White in light theme) -->
    <div
      v-if="isMobileDrawerOpen"
      class="fixed inset-0 z-50 lg:hidden flex font-sans"
    >
      <div
        class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity"
        @click="isMobileDrawerOpen = false"
      />

      <div class="relative w-80 max-w-[85vw] bg-white dark:bg-slate-900 h-full p-5 flex flex-col justify-between shadow-2xl border-r border-slate-200 dark:border-slate-800 z-10 overflow-y-auto">
        <div class="space-y-4">
          <!-- Drawer Header -->
          <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-800 text-white font-black flex items-center justify-center text-base shadow-md">
                CR
              </div>
              <div>
                <span class="font-black text-base text-slate-900 dark:text-white block">AutoRent</span>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Admin Control Hub</span>
              </div>
            </div>
            <button
              class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
              aria-label="Close Navigation"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-close-line text-2xl" />
            </button>
          </div>

          <!-- Quick Action Buttons on Mobile -->
          <div class="grid grid-cols-2 gap-2">
            <a
              :href="liveSiteUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="text-center py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs sm:text-[13px] font-bold text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition-colors flex items-center justify-center gap-1.5"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-external-link-line text-sm" /> Live Site
            </a>
            <Link
              href="/car-calendar"
              class="text-center py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs sm:text-[13px] font-bold text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition-colors flex items-center justify-center gap-1.5"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-calendar-line text-sm" /> Calendar
            </Link>
          </div>

          <!-- Main Nav Links List -->
          <div class="space-y-1 pt-1">
            <div class="px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
              System Operations
            </div>
            <Link
              v-for="item in adminNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300 font-bold border border-indigo-200/80 dark:border-indigo-800/80 shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
              class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm transition-all"
              @click="isMobileDrawerOpen = false"
            >
              <div class="flex items-center gap-3">
                <i
                  :class="item.icon"
                  class="text-lg text-indigo-600 dark:text-indigo-400"
                />
                <span>{{ item.title }}</span>
              </div>
              <span
                v-if="item.badge"
                class="px-2 py-0.5 rounded-full text-xs font-mono font-bold"
                :class="isActive(item.href) ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300'"
              >
                {{ item.badge }}
              </span>
            </Link>

            <!-- Mobile CMS Dropdown Accordion -->
            <div class="pt-2">
              <button
                type="button"
                class="w-full flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                @click="isMobileCmsExpanded = !isMobileCmsExpanded"
              >
                <div class="flex items-center gap-3">
                  <i class="ri-layout-masonry-line text-lg text-indigo-600 dark:text-indigo-400" />
                  <span>CMS Modules (16)</span>
                </div>
                <i
                  class="ri-arrow-down-s-line text-sm transition-transform duration-200"
                  :class="isMobileCmsExpanded ? 'rotate-180' : ''"
                />
              </button>

              <div
                v-if="isMobileCmsExpanded"
                class="py-1 ps-4 pe-1 space-y-0.5 max-h-56 overflow-y-auto"
              >
                <Link
                  v-for="c in cmsQuickLinks"
                  :key="c.label"
                  :href="c.href"
                  class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl text-xs sm:text-[13px] font-medium text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800 transition-colors"
                  @click="isMobileDrawerOpen = false"
                >
                  <i
                    :class="c.icon"
                    class="text-sm text-indigo-500"
                  />
                  <span>{{ c.label }}</span>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
          <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">
              {{ (displayName || 'A')[0].toUpperCase() }}
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-xs font-bold text-slate-900 dark:text-white block truncate">{{ displayName }}</span>
              <span class="text-[10px] text-slate-400 block truncate">{{ adminEmail }}</span>
            </div>
          </div>

          <button
            type="button"
            class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 font-bold text-sm transition-colors cursor-pointer"
            @click="logout"
          >
            <span>Sign Out</span>
            <i class="ri-logout-box-r-line text-base" />
          </button>
        </div>
      </div>
    </div>

    <!-- Main Body Container with Flush Gray-White Desktop Sidebar (No Gaps) -->
    <div class="flex-1 flex w-full font-sans min-h-[calc(100vh-4rem)]">
      <!-- Desktop Sidebar Navigation (Flush to Left, Header, Footer & Main Content with Gray-White Background) -->
      <aside class="w-64 xl:w-72 shrink-0 hidden lg:flex flex-col justify-between bg-slate-100/95 dark:bg-slate-900 border-r border-slate-200/90 dark:border-slate-800 p-4 xl:p-5 sticky top-16 h-[calc(100vh-4rem)] select-none z-30">
        <!-- Top Section: Brand & Nav Links -->
        <div class="space-y-4 flex-1 flex flex-col min-h-0">
          <!-- Sidebar Brand Header (Image reference: Icon + Bold Title) -->
          <div class="flex items-center gap-3 px-1 py-1 pb-3 border-b border-slate-200/80 dark:border-slate-800/80">
            <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-indigo-600 via-blue-600 to-indigo-700 flex items-center justify-center text-white font-black text-lg shadow-md shadow-indigo-500/20 shrink-0">
              <i class="ri-steering-2-line text-xl" />
            </div>
            <div class="flex flex-col min-w-0">
              <span class="font-black text-base tracking-wider text-slate-900 dark:text-white uppercase truncate font-sans">
                AutoRent
              </span>
              <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest leading-none">
                Admin Panel
              </span>
            </div>
          </div>

          <!-- Main Nav List (Matching screenshot pill items) -->
          <nav class="space-y-1.5 flex-1 overflow-y-auto no-scrollbar pe-0.5">
            <Link
              v-for="item in adminNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) 
                ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' 
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white font-semibold'"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-[13.5px] transition-all group font-sans cursor-pointer"
            >
              <div class="flex items-center gap-3.5 min-w-0">
                <i
                  :class="[
                    item.icon,
                    isActive(item.href) ? 'text-white' : 'text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200'
                  ]"
                  class="text-lg transition-transform shrink-0"
                />
                <span class="truncate">{{ item.title }}</span>
              </div>
              <span
                v-if="item.badge"
                class="px-2 py-0.5 rounded-full text-xs font-mono font-bold shrink-0"
                :class="isActive(item.href) ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
              >
                {{ item.badge }}
              </span>
            </Link>

            <!-- CMS Modules Accordion in Sidebar -->
            <div class="pt-1">
              <button
                type="button"
                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-[13.5px] font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200/70 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white transition-all cursor-pointer"
                @click="isMobileCmsExpanded = !isMobileCmsExpanded"
              >
                <div class="flex items-center gap-3.5">
                  <i class="ri-layout-masonry-line text-lg text-slate-400" />
                  <span>CMS Suite (16)</span>
                </div>
                <i
                  class="ri-arrow-down-s-line text-xs transition-transform duration-200"
                  :class="isMobileCmsExpanded ? 'rotate-180 text-slate-900 dark:text-white' : ''"
                />
              </button>

              <div
                v-if="isMobileCmsExpanded"
                class="mt-1 ms-4 ps-3 border-l border-slate-300 dark:border-slate-800 space-y-0.5 max-h-48 overflow-y-auto py-1"
              >
                <Link
                  v-for="c in cmsQuickLinks"
                  :key="c.label"
                  :href="c.href"
                  :class="currentUrl.includes(c.href.split('?')[1] || '') ? 'text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50/80 dark:bg-slate-800/60' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/40'"
                  class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl text-xs font-medium transition-colors"
                >
                  <i
                    :class="c.icon"
                    class="text-sm text-indigo-500"
                  />
                  <span class="truncate">{{ c.label }}</span>
                </Link>
              </div>
            </div>
          </nav>
        </div>

        <!-- Bottom Section of Sidebar: User Profile Card (Matching Image Reference) -->
        <div class="pt-3 border-t border-slate-200 dark:border-slate-800/80 shrink-0">
          <Link
            href="/admin/profile"
            class="flex items-center justify-between p-2 rounded-2xl hover:bg-slate-200/70 dark:hover:bg-slate-800/70 transition-colors group cursor-pointer"
          >
            <div class="flex items-center gap-3 min-w-0">
              <div class="relative shrink-0">
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-600 via-blue-600 to-indigo-700 text-white font-bold text-xs flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-800">
                  {{ (displayName || 'A')[0].toUpperCase() }}
                </div>
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-100 dark:ring-slate-900" />
              </div>
              <div class="min-w-0 flex-1">
                <span class="text-sm font-bold text-slate-900 dark:text-white block truncate leading-tight">
                  {{ displayName }}
                </span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 block truncate leading-tight mt-0.5">
                  Admin
                </span>
              </div>
            </div>
            <i class="ri-arrow-right-s-line text-sm text-slate-400 group-hover:text-slate-700 dark:group-hover:text-white transition-transform" />
          </Link>
        </div>
      </aside>

      <!-- Main Content Area (Flush to Sidebar Border) -->
      <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 xl:p-10 bg-slate-100/40 dark:bg-slate-950 overflow-y-auto">
        <!-- Flash Alerts -->
        <MessageBox
          v-model="flashSuccess"
          type="success"
          class="mb-5"
        />
        <MessageBox
          v-model="flashError"
          type="error"
          class="mb-5"
        />

        <slot />
      </main>
    </div>

    <!-- Dedicated Admin System Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-xs text-slate-500 font-sans">
      <div class="w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="font-bold text-slate-700 dark:text-slate-300">AutoRent Super Admin Hub</span>
          <span class="text-slate-300 dark:text-slate-700">|</span>
          <span class="flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            All Systems Operational
          </span>
        </div>

        <div class="flex items-center gap-4 text-xs text-slate-400">
          <span>Enterprise v2.5</span>
          <span>&copy; 2026 AutoRent Global Systems</span>
          <Link
            href="/admin/cms"
            class="hover:text-indigo-600 transition-colors font-semibold"
          >
            CMS Control
          </Link>
          <Link
            href="/admin/security"
            class="hover:text-indigo-600 transition-colors font-semibold"
          >
            Security
          </Link>
        </div>
      </div>
    </footer>
  </div>
</template>
