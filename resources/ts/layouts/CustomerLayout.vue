<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import MessageBox from '@/components/MessageBox.vue'
import { useAdminTheme, type AdminThemeStyle } from '@/composable/useAdminTheme'

const page = usePage()
const { theme, setTheme, initTheme } = useAdminTheme()

const isMobileDrawerOpen = ref(false)
const showProfileMenu = ref(false)
const showThemeMenu = ref(false)

const auth = computed(() => (page.props.auth as any) || {})
const customer = computed(() => auth.value?.customer || auth.value?.user)

const displayName = computed(() => {
  return customer.value?.name || customer.value?.full_name || 'Customer'
})

const customerEmail = computed(() => {
  return customer.value?.email || 'customer@carrental.com'
})

const isImpersonating = computed(() => {
  return Boolean(auth.value?.isImpersonating || (auth.value?.admin && auth.value?.customer))
})

const adminReturnUrl = computed(() => {
  const base = auth.value?.adminPortalUrl || ''

  return base ? `${base}/admin/customers` : '/admin/customers'
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

const customerNav = computed(() => [
  { title: 'Customer Dashboard', icon: 'ri-dashboard-3-line', href: '/customer/dashboard' },
  { title: 'Browse Fleet', icon: 'ri-car-line', href: '/customer/cars' },
  { title: 'My Bookings', icon: 'ri-calendar-check-line', href: '/customer/bookings' },
  { title: 'Availability Calendar', icon: 'ri-calendar-line', href: '/customer/calendar' },
  { title: 'AI Assistant', icon: 'ri-sparkling-line', href: '/ai-chat' },
  { title: 'Customer Profile', icon: 'ri-user-smile-line', href: '/customer/profile' },
  { title: 'Account Security & MFA', icon: 'ri-shield-keyhole-line', href: '/customer/security' },
])

const isActive = (href: string) => {
  const current = currentUrl.value.split('?')[0]
  if (href === '/customer/dashboard') {
    return current === '/customer/dashboard' || current === '/customer'
  }
  if (href === '/customer/profile') {
    return current === '/customer/profile'
  }
  if (href === '/customer/security') {
    return current === '/customer/security'
  }
  if (href === '/customer/cars') {
    return current === '/customer/cars' || current.startsWith('/customer/cars/')
  }
  if (href === '/customer/calendar') {
    return current === '/customer/calendar' || current.startsWith('/customer/calendar/')
  }
  if (href === '/customer/bookings') {
    return current.startsWith('/customer/bookings')
  }
  
  return current.startsWith(href)
}

const logout = () => {
  router.post('/customer/logout')
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
  
  return 'ri-moon-clear-line text-blue-400'
})

const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('#customer-profile-dropdown-container')) {
    showProfileMenu.value = false
  }
  if (!target.closest('#customer-theme-dropdown-container')) {
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
  <div class="min-h-screen bg-slate-100/70 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col font-sans selection:bg-blue-600 selection:text-white antialiased transition-colors duration-200">
    <!-- Admin Impersonation Notice Top Banner -->
    <div
      v-if="isImpersonating"
      class="bg-gradient-to-r from-amber-600 via-indigo-700 to-blue-700 text-white px-4 py-2.5 shadow-md flex items-center justify-between text-xs sm:text-sm font-medium z-50"
    >
      <div class="flex items-center gap-2 max-w-4xl truncate">
        <span class="px-2 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-black uppercase tracking-wider shrink-0">
          Admin Impersonation
        </span>
        <span class="truncate">
          Logged in as Customer: <strong class="text-white underline">{{ displayName }}</strong> ({{ customerEmail }})
        </span>
      </div>
      <a
        :href="adminReturnUrl"
        class="shrink-0 px-3 py-1 rounded-xl bg-white text-indigo-900 font-bold text-xs shadow-sm hover:bg-slate-100 transition-colors flex items-center gap-1.5 cursor-pointer ms-3"
      >
        <i class="ri-arrow-left-line" />
        <span>Return to Admin Portal</span>
      </a>
    </div>

    <!-- Top Modern Customer Header Bar -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="w-full px-3 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 h-16 flex items-center justify-between gap-3">
        <!-- Left: Mobile Drawer Hamburger Button + Logo & Portal Badge -->
        <div class="flex items-center gap-2.5 sm:gap-3.5">
          <button
            type="button"
            class="lg:hidden p-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer"
            aria-label="Open Navigation Menu"
            @click="isMobileDrawerOpen = true"
          >
            <i class="ri-menu-2-line text-2xl" />
          </button>

          <Link
            href="/customer/dashboard"
            class="flex items-center gap-2.5 group"
          >
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 via-indigo-600 to-sky-600 flex items-center justify-center text-white font-black text-lg shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
              CR
            </div>
            <span class="font-black text-lg tracking-tight text-slate-900 dark:text-white hidden sm:inline font-sans">
              Car Rental
            </span>
          </Link>

          <span class="bg-blue-50 text-blue-700 border border-blue-200/80 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800 text-[11px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider shrink-0 font-sans">
            Customer Portal
          </span>
        </div>

        <!-- Center: Quick Nav -->
        <div class="hidden md:flex items-center gap-1.5 lg:gap-2">
          <Link
            href="/customer/dashboard"
            :class="isActive('/customer/dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Dashboard
          </Link>
          <Link
            href="/customer/cars"
            :class="isActive('/customer/cars') ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Browse Fleet
          </Link>
          <Link
            href="/customer/bookings"
            :class="isActive('/customer/bookings') ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            My Bookings
          </Link>
          <Link
            href="/customer/calendar"
            :class="isActive('/customer/calendar') ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Calendar
          </Link>
          <Link
            href="/ai-chat"
            :class="isActive('/ai-chat') ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors flex items-center gap-1.5 font-sans"
          >
            <i class="ri-sparkling-line text-blue-600 dark:text-blue-400" />
            <span>AI Assistant</span>
          </Link>
        </div>

        <!-- Right Side: Theme Switcher, Live Site & Profile Dropdown -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Theme Switcher Dropdown -->
          <div
            id="customer-theme-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="p-2 sm:px-3 sm:py-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-colors inline-flex items-center gap-1.5 cursor-pointer focus:outline-none"
              title="Change Theme Style"
              @click="showThemeMenu = !showThemeMenu; showProfileMenu = false"
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
                :class="theme === opt.value ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/80 dark:text-blue-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium'"
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
                  class="ri-check-line text-blue-600 dark:text-blue-400 text-xs font-bold"
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

          <!-- Profile Dropdown Container -->
          <div
            id="customer-profile-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="flex items-center gap-2.5 p-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer"
              @click="showProfileMenu = !showProfileMenu; showThemeMenu = false"
            >
              <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                {{ (displayName || 'C')[0].toUpperCase() }}
              </div>
              <div
                class="text-left hidden lg:block"
                style="padding-right: 0.25rem"
              >
                <span class="text-xs sm:text-[13px] font-bold text-slate-800 dark:text-slate-100 block leading-tight truncate max-w-[130px] font-sans">
                  {{ displayName }}
                </span>
                <span class="text-[10px] font-medium text-slate-400 block leading-none font-sans">
                  Customer
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
                <span class="text-[11px] text-slate-500 block truncate">{{ customerEmail }}</span>
              </div>

              <!-- Menu Items -->
              <div class="space-y-1">
                <Link
                  href="/customer/profile"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-user-smile-line text-base text-slate-500" />
                  <span>Customer Profile</span>
                </Link>

                <Link
                  href="/customer/security"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-shield-keyhole-line text-base text-blue-600" />
                  <span>Account Security & MFA</span>
                </Link>

                <Link
                  href="/customer/dashboard"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-dashboard-line text-base text-indigo-600" />
                  <span>Traveler Dashboard</span>
                </Link>
              </div>

              <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                <button
                  type="button"
                  class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 font-semibold text-xs sm:text-[13px] transition-colors cursor-pointer"
                  @click="logout"
                >
                  <span>Sign Out</span>
                  <i class="ri-logout-box-r-line" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Mobile Navigation Drawer Slide-Over -->
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
              <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 via-indigo-600 to-sky-600 text-white font-black flex items-center justify-center text-base shadow-md">
                CR
              </div>
              <div>
                <span class="font-black text-base text-slate-900 dark:text-white block">Car Rental</span>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400">Customer Portal</span>
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
              class="text-center py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs sm:text-[13px] font-bold text-slate-700 dark:text-slate-200 hover:bg-blue-50 hover:text-blue-600 transition-colors flex items-center justify-center gap-1.5"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-external-link-line text-sm" /> Live Site
            </a>
            <Link
              href="/customer/calendar"
              class="text-center py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs sm:text-[13px] font-bold text-slate-700 dark:text-slate-200 hover:bg-blue-50 hover:text-blue-600 transition-colors flex items-center justify-center gap-1.5"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-calendar-line text-sm" /> Calendar
            </Link>
          </div>

          <!-- Main Nav Links List -->
          <div class="space-y-1 pt-1">
            <div class="px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
              Traveler Menu
            </div>
            <Link
              v-for="item in customerNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300 font-bold border border-blue-200/80 dark:border-blue-800/80 shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
              class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm transition-all"
              @click="isMobileDrawerOpen = false"
            >
              <div class="flex items-center gap-3">
                <i
                  :class="item.icon"
                  class="text-lg text-blue-600 dark:text-blue-400"
                />
                <span>{{ item.title }}</span>
              </div>
            </Link>
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
          <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60">
            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">
              {{ (displayName || 'C')[0].toUpperCase() }}
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-xs font-bold text-slate-900 dark:text-white block truncate">{{ displayName }}</span>
              <span class="text-[10px] text-slate-400 block truncate">{{ customerEmail }}</span>
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

    <!-- Main Body Container with Flush Desktop Sidebar -->
    <div class="flex-1 flex w-full font-sans min-h-[calc(100vh-4rem)]">
      <!-- Desktop Sidebar Navigation -->
      <aside class="w-64 xl:w-72 shrink-0 hidden lg:flex flex-col justify-between bg-slate-100/95 dark:bg-slate-900 border-r border-slate-200/90 dark:border-slate-800 p-4 xl:p-5 sticky top-16 h-[calc(100vh-4rem)] select-none z-30">
        <!-- Top Section: Brand & Nav Links -->
        <div class="space-y-4 flex-1 flex flex-col min-h-0">
          <div class="flex items-center gap-3 px-1 py-1 pb-3 border-b border-slate-200/80 dark:border-slate-800/80">
            <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-600 flex items-center justify-center text-white font-black text-lg shadow-md shadow-blue-500/20 shrink-0">
              <i class="ri-compass-3-line text-xl" />
            </div>
            <div class="flex flex-col min-w-0">
              <span class="font-black text-base tracking-wider text-slate-900 dark:text-white uppercase truncate font-sans">
                Car Rental
              </span>
              <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest leading-none">
                Traveler Hub
              </span>
            </div>
          </div>

          <!-- Main Nav List -->
          <nav class="space-y-1.5 flex-1 overflow-y-auto no-scrollbar pe-0.5">
            <Link
              v-for="item in customerNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) 
                ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' 
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
            </Link>
          </nav>

          <!-- AI Support Promo Card -->
          <div class="p-3.5 rounded-2xl bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-800 text-white shadow-md space-y-1.5 text-xs shrink-0">
            <div class="flex items-center gap-1.5 font-bold">
              <i class="ri-sparkling-fill text-amber-300" />
              <span>AI Trip Assistant</span>
            </div>
            <p class="text-[11px] text-blue-100/90 leading-tight">
              Plan your routes, check car availability, and calculate travel budgets instantly.
            </p>
            <Link
              href="/ai-chat"
              class="inline-block mt-1 px-3 py-1.5 rounded-xl bg-white text-blue-700 font-bold text-[11px] shadow-xs hover:bg-blue-50 transition-colors"
            >
              Start AI Chat &rarr;
            </Link>
          </div>
        </div>

        <!-- Bottom Section: Customer Profile Card -->
        <div class="pt-3 border-t border-slate-200 dark:border-slate-800/80 shrink-0">
          <Link
            href="/customer/profile"
            class="flex items-center justify-between p-2 rounded-2xl hover:bg-slate-200/70 dark:hover:bg-slate-800/70 transition-colors group cursor-pointer"
          >
            <div class="flex items-center gap-3 min-w-0">
              <div class="relative shrink-0">
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-600 text-white font-bold text-xs flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-800">
                  {{ (displayName || 'C')[0].toUpperCase() }}
                </div>
                <span class="absolute bottom-0 end-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-100 dark:ring-slate-900" />
              </div>
              <div class="min-w-0 flex-1">
                <span class="text-sm font-bold text-slate-900 dark:text-white block truncate leading-tight">
                  {{ displayName }}
                </span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 block truncate leading-tight mt-0.5">
                  Customer
                </span>
              </div>
            </div>
            <i class="ri-arrow-right-s-line text-sm text-slate-400 group-hover:text-slate-700 dark:group-hover:text-white transition-transform" />
          </Link>
        </div>
      </aside>

      <!-- Main Content Area -->
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

    <!-- Customer Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-xs text-slate-500 font-sans">
      <div class="w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="font-bold text-slate-700 dark:text-slate-300">Car Rental Traveler Portal</span>
          <span class="text-slate-300 dark:text-slate-700">|</span>
          <span class="flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            Verified Vehicle Directory Active
          </span>
        </div>

        <div class="flex items-center gap-4 text-xs text-slate-400">
          <span>&copy; 2026 Car Rental Global Systems</span>
          <Link
            href="/customer/cars"
            class="hover:text-blue-600 transition-colors font-semibold"
          >
            Showroom
          </Link>
          <Link
            href="/customer/security"
            class="hover:text-blue-600 transition-colors font-semibold"
          >
            Security & 2FA
          </Link>
        </div>
      </div>
    </footer>
  </div>
</template>
