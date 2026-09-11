<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import MessageBox from '@/components/MessageBox.vue'
import { useOwnerTheme, type OwnerThemeStyle } from '@/composable/useOwnerTheme'
import { useSiteSettings } from '@/composable/useSiteSettings'

const page = usePage()
const { theme, setTheme, initTheme } = useOwnerTheme()
const { logoUrl, companyName } = useSiteSettings()

const isMobileDrawerOpen = ref(false)
const showProfileMenu = ref(false)
const showThemeMenu = ref(false)

const auth = computed(() => (page.props.auth as any) || {})
const owner = computed(() => auth.value?.owner || auth.value?.user)

const displayName = computed(() => {
  return owner.value?.full_name || owner.value?.name || 'Fleet Partner'
})

const ownerEmail = computed(() => {
  return owner.value?.email || 'owner@carrental.com'
})

const isImpersonating = computed(() => {
  return Boolean(auth.value?.isImpersonating || (auth.value?.admin && auth.value?.owner))
})

const adminReturnUrl = computed(() => {
  const base = auth.value?.adminPortalUrl || ''

  return base ? `${base}/admin/owners` : '/admin/owners'
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

const ownerNav = computed(() => [
  { title: 'Fleet Dashboard', icon: 'ri-dashboard-3-line', href: '/owner/dashboard' },
  { title: 'My Fleet Cars', icon: 'ri-car-line', href: '/owner/cars' },
  { title: 'Driver Roster', icon: 'ri-user-star-line', href: '/owner/drivers' },
  { title: 'Add New Car', icon: 'ri-add-circle-line', href: '/owner/cars/create' },
  { title: 'Rental Bookings', icon: 'ri-calendar-check-line', href: '/owner/bookings' },
  { title: 'Availability Calendar', icon: 'ri-calendar-line', href: '/owner/calendar' },
  { title: 'Owner Profile', icon: 'ri-user-settings-line', href: '/owner/profile' },
  { title: 'Account Security & MFA', icon: 'ri-shield-keyhole-line', href: '/owner/security' },
])

const isActive = (href: string) => {
  const current = currentUrl.value.split('?')[0]
  if (href === '/owner/dashboard') {
    return current === '/owner/dashboard' || current === '/owner'
  }
  if (href === '/owner/profile') {
    return current === '/owner/profile'
  }
  if (href === '/owner/security') {
    return current === '/owner/security'
  }
  if (href === '/owner/cars/create') {
    return current === '/owner/cars/create'
  }
  if (href === '/owner/cars') {
    return current === '/owner/cars' || (current.startsWith('/owner/cars/') && !current.includes('/create'))
  }
  if (href === '/owner/drivers') {
    return current.startsWith('/owner/drivers')
  }
  if (href === '/owner/bookings') {
    return current.startsWith('/owner/bookings')
  }
  if (href === '/owner/calendar') {
    return current.startsWith('/owner/calendar') || current.startsWith('/owner/car-calendar')
  }
  
  return current.startsWith(href)
}

const logout = () => {
  router.post('/owner/logout')
}

const themeOptions: { value: OwnerThemeStyle; label: string; icon: string }[] = [
  { value: 'dark', label: 'Dark Mode', icon: 'ri-moon-clear-line' },
  { value: 'midnight', label: 'Midnight Obsidian', icon: 'ri-sparkling-2-line' },
  { value: 'light', label: 'Light Mode', icon: 'ri-sun-line' },
  { value: 'system', label: 'System Default', icon: 'ri-computer-line' },
]

const currentThemeIcon = computed(() => {
  if (theme.value === 'light') return 'ri-sun-line text-amber-500'
  if (theme.value === 'midnight') return 'ri-sparkling-2-line text-purple-400'
  if (theme.value === 'system') return 'ri-computer-line text-emerald-400'
  
  return 'ri-moon-clear-line text-emerald-400'
})

const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('#owner-profile-dropdown-container')) {
    showProfileMenu.value = false
  }
  if (!target.closest('#owner-theme-dropdown-container')) {
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
  <div class="min-h-screen bg-slate-100/70 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col font-sans selection:bg-emerald-600 selection:text-white antialiased transition-colors duration-200">
    <!-- Admin Impersonation Notice Top Banner -->
    <div
      v-if="isImpersonating"
      class="bg-gradient-to-r from-amber-600 via-teal-700 to-emerald-700 text-white px-4 py-2.5 shadow-md flex items-center justify-between text-xs sm:text-sm font-medium z-50"
    >
      <div class="flex items-center gap-2 max-w-4xl truncate">
        <span class="px-2 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-black uppercase tracking-wider shrink-0">
          Admin Impersonation
        </span>
        <span class="truncate">
          Logged in as Fleet Owner: <strong class="text-white underline">{{ displayName }}</strong> ({{ ownerEmail }})
        </span>
      </div>
      <a
        :href="adminReturnUrl"
        class="shrink-0 px-3 py-1 rounded-xl bg-white text-emerald-950 font-bold text-xs shadow-sm hover:bg-slate-100 transition-colors flex items-center gap-1.5 cursor-pointer ms-3"
      >
        <i class="ri-arrow-left-line" />
        <span>Return to Admin Portal</span>
      </a>
    </div>

    <!-- Top Modern Owner Header Bar -->
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
            href="/owner/dashboard"
            class="flex items-center gap-2.5 group"
          >
            <img
              v-if="logoUrl"
              :src="logoUrl"
              :alt="companyName"
              class="h-9 max-w-[140px] sm:max-w-[180px] object-contain group-hover:scale-105 transition-transform"
            />
            <template v-else>
              <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 flex items-center justify-center text-white font-black text-lg shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                CR
              </div>
              <span class="font-black text-lg tracking-tight text-slate-900 dark:text-white hidden sm:inline font-sans">
                {{ companyName }}
              </span>
            </template>
          </Link>

          <span class="bg-emerald-50 text-emerald-700 border border-emerald-200/80 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800 text-[11px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider shrink-0 font-sans">
            Fleet Owner Portal
          </span>
        </div>

        <!-- Center: Quick Nav -->
        <div class="hidden md:flex items-center gap-1.5 lg:gap-2">
          <Link
            href="/owner/dashboard"
            :class="isActive('/owner/dashboard') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Dashboard
          </Link>
          <Link
            href="/owner/cars"
            :class="isActive('/owner/cars') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            My Fleet
          </Link>
          <Link
            href="/owner/drivers"
            :class="isActive('/owner/drivers') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Drivers
          </Link>
          <Link
            href="/owner/bookings"
            :class="isActive('/owner/bookings') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Bookings
          </Link>
          <Link
            href="/owner/calendar"
            :class="isActive('/owner/calendar') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
            class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] transition-colors font-sans"
          >
            Calendar
          </Link>
        </div>

        <!-- Right Side: Theme Switcher, Live Site & Profile Dropdown -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Theme Switcher Dropdown -->
          <div
            id="owner-theme-dropdown-container"
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
                :class="theme === opt.value ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-300 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium'"
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
                  class="ri-check-line text-emerald-600 dark:text-emerald-400 text-xs font-bold"
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
            id="owner-profile-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="flex items-center gap-2.5 p-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer"
              @click="showProfileMenu = !showProfileMenu; showThemeMenu = false"
            >
              <div class="w-8 h-8 rounded-xl overflow-hidden bg-gradient-to-tr from-emerald-600 to-teal-700 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                <img
                  v-if="owner?.image_url || owner?.image_path?.original || (typeof owner?.image_path === 'string' && owner?.image_path)"
                  :src="owner?.image_url || owner?.image_path?.original || owner?.image_path"
                  class="w-full h-full object-cover"
                  alt="Owner"
                  @error="(e: any) => (e.target.style.display = 'none')"
                >
                <span>{{ (displayName || 'O')[0].toUpperCase() }}</span>
              </div>
              <div
                class="text-left hidden lg:block"
                style="padding-right: 0.25rem"
              >
                <span class="text-xs sm:text-[13px] font-bold text-slate-800 dark:text-slate-100 block leading-tight truncate max-w-[130px] font-sans">
                  {{ displayName }}
                </span>
                <span class="text-[10px] font-medium text-slate-400 block leading-none font-sans">
                  Fleet Owner
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
                <span class="text-[11px] text-slate-500 block truncate">{{ ownerEmail }}</span>
              </div>

              <!-- Menu Items -->
              <div class="space-y-1">
                <Link
                  href="/owner/profile"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-user-settings-line text-base text-slate-500" />
                  <span>Fleet Owner Profile</span>
                </Link>

                <Link
                  href="/owner/security"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-shield-keyhole-line text-base text-emerald-600" />
                  <span>Account Security & MFA</span>
                </Link>

                <Link
                  href="/owner/dashboard"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs sm:text-[13px] font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-dashboard-line text-base text-teal-600" />
                  <span>Fleet Dashboard</span>
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
              <img
                v-if="logoUrl"
                :src="logoUrl"
                :alt="companyName"
                class="h-9 max-w-[140px] object-contain"
              />
              <template v-else>
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 text-white font-black flex items-center justify-center text-base shadow-md">
                  CR
                </div>
                <div>
                  <span class="font-black text-base text-slate-900 dark:text-white block">{{ companyName }}</span>
                  <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Owner Hub</span>
                </div>
              </template>
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
            <Link
              href="/owner/cars/create"
              class="text-center py-2.5 px-3 rounded-xl bg-emerald-600 text-white text-xs sm:text-[13px] font-bold shadow-xs hover:bg-emerald-700 transition-colors flex items-center justify-center gap-1.5"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-add-circle-line text-sm" /> Add Car
            </Link>
            <Link
              href="/owner/calendar"
              class="text-center py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs sm:text-[13px] font-bold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 hover:text-emerald-600 transition-colors flex items-center justify-center gap-1.5"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-calendar-line text-sm" /> Calendar
            </Link>
          </div>

          <!-- Main Nav Links List -->
          <div class="space-y-1 pt-1">
            <div class="px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
              Fleet Management
            </div>
            <Link
              v-for="item in ownerNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 font-bold border border-emerald-200/80 dark:border-emerald-800/80 shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
              class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm transition-all"
              @click="isMobileDrawerOpen = false"
            >
              <div class="flex items-center gap-3">
                <i
                  :class="item.icon"
                  class="text-lg text-emerald-600 dark:text-emerald-400"
                />
                <span>{{ item.title }}</span>
              </div>
            </Link>
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
          <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">
              {{ (displayName || 'O')[0].toUpperCase() }}
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-xs font-bold text-slate-900 dark:text-white block truncate">{{ displayName }}</span>
              <span class="text-[10px] text-slate-400 block truncate">{{ ownerEmail }}</span>
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
            <img
              v-if="logoUrl"
              :src="logoUrl"
              :alt="companyName"
              class="h-9 max-w-[140px] object-contain"
            />
            <template v-else>
              <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-emerald-700 flex items-center justify-center text-white font-black text-lg shadow-md shadow-emerald-500/20 shrink-0">
                <i class="ri-car-line text-xl" />
              </div>
              <div class="flex flex-col min-w-0">
                <span class="font-black text-base tracking-wider text-slate-900 dark:text-white uppercase truncate font-sans">
                  {{ companyName }}
                </span>
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest leading-none">
                  Owner Hub
                </span>
              </div>
            </template>
          </div>

          <!-- Main Nav List -->
          <nav class="space-y-1.5 flex-1 overflow-y-auto no-scrollbar pe-0.5">
            <Link
              v-for="item in ownerNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) 
                ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/30' 
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

          <!-- Fleet Quick Add CTA Card -->
          <div class="p-3.5 rounded-2xl bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 text-white shadow-md space-y-1.5 text-xs shrink-0">
            <div class="flex items-center gap-1.5 font-bold">
              <i class="ri-add-circle-fill text-white" />
              <span>Expand Your Fleet</span>
            </div>
            <p class="text-[11px] text-emerald-100/90 leading-tight">
              List new cars and assign professional chauffeurs to maximize rental payouts.
            </p>
            <Link
              href="/owner/cars/create"
              class="inline-block mt-1 px-3 py-1.5 rounded-xl bg-white text-emerald-800 font-bold text-[11px] shadow-xs hover:bg-emerald-50 transition-colors"
            >
              + List New Vehicle
            </Link>
          </div>
        </div>

        <!-- Bottom Section: Owner Profile Card -->
        <div class="pt-3 border-t border-slate-200 dark:border-slate-800/80 shrink-0">
          <Link
            href="/owner/profile"
            class="flex items-center justify-between p-2 rounded-2xl hover:bg-slate-200/70 dark:hover:bg-slate-800/70 transition-colors group cursor-pointer"
          >
            <div class="flex items-center gap-3 min-w-0">
              <div class="relative shrink-0">
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-emerald-600 via-teal-600 to-emerald-700 text-white font-bold text-xs flex items-center justify-center shadow-xs ring-2 ring-white dark:ring-slate-800">
                  {{ (displayName || 'O')[0].toUpperCase() }}
                </div>
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-100 dark:ring-slate-900" />
              </div>
              <div class="min-w-0 flex-1">
                <span class="text-sm font-bold text-slate-900 dark:text-white block truncate leading-tight">
                  {{ displayName }}
                </span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 block truncate leading-tight mt-0.5">
                  Fleet Owner
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

    <!-- Owner Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-xs text-slate-500 font-sans">
      <div class="w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="font-bold text-slate-700 dark:text-slate-300">Car Rental Fleet Partner Portal</span>
          <span class="text-slate-300 dark:text-slate-700">|</span>
          <span class="flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            Partner Network Active
          </span>
        </div>

        <div class="flex items-center gap-4 text-xs text-slate-400">
          <span>&copy; 2026 Car Rental Global Systems</span>
          <Link
            href="/owner/cars"
            class="hover:text-emerald-600 transition-colors font-semibold"
          >
            My Fleet
          </Link>
          <Link
            href="/owner/security"
            class="hover:text-emerald-600 transition-colors font-semibold"
          >
            Security & 2FA
          </Link>
        </div>
      </div>
    </footer>
  </div>
</template>
