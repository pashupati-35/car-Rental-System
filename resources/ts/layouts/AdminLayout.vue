<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'

const page = usePage()
const isMobileDrawerOpen = ref(false)
const showProfileMenu = ref(false)
const showCmsMenu = ref(false)
const isMobileCmsExpanded = ref(false)

const auth = computed(() => page.props.auth as any)
const admin = computed(() => auth.value?.admin || auth.value?.user)

const displayName = computed(() => {
  return admin.value?.name || admin.value?.full_name || 'System Admin'
})

const adminEmail = computed(() => {
  return admin.value?.email || 'admin@autorent.com'
})

const flash = computed(() => page.props.flash as any)
const currentUrl = computed(() => page.url)

const adminNav = [
  { title: 'Master Dashboard', icon: 'ri-dashboard-line', href: '/admin/dashboard', badge: '' },
  { title: 'Fleet Cars Verification', icon: 'ri-car-line', href: '/admin/cars', badge: '' },
  { title: 'Driver Directory', icon: 'ri-user-star-line', href: '/admin/drivers', badge: '' },
  { title: 'Rental Bookings', icon: 'ri-calendar-check-line', href: '/admin/booked-cars', badge: '' },
  { title: 'Fleet Owners', icon: 'ri-building-line', href: '/admin/owners', badge: '' },
  { title: 'Customers', icon: 'ri-user-smile-line', href: '/admin/customers', badge: '' },
  { title: 'Master CMS Suite', icon: 'ri-layout-masonry-line', href: '/admin/cms', badge: '16' },
  { title: 'Email Templates', icon: 'ri-mail-settings-line', href: '/admin/email-templates', badge: '' },
  { title: 'Admin Profile & Security', icon: 'ri-shield-user-line', href: '/admin/profile', badge: '' },
]

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
  { label: 'Site Settings & SEO', href: '/admin/cms?module=site-settings', icon: 'ri-settings-4-line' },
]

const isActive = (href: string) => {
  if (href === '/admin/dashboard') {
    return currentUrl.value === '/admin/dashboard' || currentUrl.value === '/admin'
  }
  
  return currentUrl.value.startsWith(href)
}

const logout = () => {
  router.post('/admin/logout')
}

const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('#admin-profile-dropdown-container')) {
    showProfileMenu.value = false
  }
  if (!target.closest('#admin-cms-dropdown-container')) {
    showCmsMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col font-sans selection:bg-indigo-500 selection:text-white antialiased">
    <!-- Top Modern Admin Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-xs">
      <div class="w-full px-3 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 h-16 flex items-center justify-between gap-3">
        <!-- Left: Hamburger on Mobile + Logo & Admin Portal Badge -->
        <div class="flex items-center gap-2.5 sm:gap-3">
          <!-- Mobile Drawer Hamburger Button -->
          <button
            type="button"
            class="lg:hidden p-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none"
            aria-label="Open Mobile Menu"
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
            <span class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white hidden sm:inline">
              AutoRent
            </span>
          </Link>

          <span class="bg-indigo-50 text-indigo-700 border border-indigo-200/80 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800 text-[10px] px-2.5 py-0.5 rounded-full font-extrabold uppercase tracking-wider shrink-0">
            Admin Portal
          </span>
        </div>

        <!-- Center: Quick Nav & CMS Dropdown Menu -->
        <div class="hidden md:flex items-center gap-1 lg:gap-2">
          <!-- CMS Dropdown -->
          <div
            id="admin-cms-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center gap-1.5 transition-colors cursor-pointer"
              @click="showCmsMenu = !showCmsMenu; showProfileMenu = false"
            >
              <i class="ri-layout-masonry-line text-sm text-indigo-600 dark:text-indigo-400" />
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
              <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Master CMS & Fleet Suite (16 Modules)
              </div>
              <div class="grid grid-cols-1 gap-0.5">
                <Link
                  v-for="item in cmsQuickLinks"
                  :key="item.label"
                  :href="item.href"
                  class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800 transition-colors"
                  @click="showCmsMenu = false"
                >
                  <i
                    :class="item.icon"
                    class="text-sm text-indigo-600 dark:text-indigo-400"
                  />
                  <span>{{ item.label }}</span>
                </Link>
              </div>
            </div>
          </div>

          <Link
            href="/admin/dashboard"
            :class="isActive('/admin/dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
            class="px-3 py-2 rounded-xl text-xs transition-colors"
          >
            Dashboard
          </Link>
          <Link
            href="/cars"
            class="px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
          >
            Browse Fleet
          </Link>
          <Link
            href="/car-calendar"
            class="px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
          >
            Calendar
          </Link>
        </div>

        <!-- Right Side: Live Site & Admin Profile Dropdown -->
        <div class="flex items-center gap-2 sm:gap-3">
          <Link
            href="/"
            title="View Public Website"
            class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 text-sm transition-colors hidden sm:inline-flex items-center gap-1.5"
          >
            <i class="ri-external-link-line text-xs" />
            <span class="text-xs font-medium">Live Site</span>
          </Link>

          <!-- Profile Dropdown Container -->
          <div
            id="admin-profile-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="flex items-center gap-2 sm:gap-2.5 p-1 sm:p-1.5 sm:pr-3 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:shadow-md transition-all cursor-pointer"
              @click="showProfileMenu = !showProfileMenu; showCmsMenu = false"
            >
              <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-sm shrink-0">
                {{ displayName.charAt(0).toUpperCase() }}
              </div>
              <div class="hidden sm:block text-left">
                <p class="text-xs font-bold text-slate-900 dark:text-white leading-tight flex items-center gap-1.5">
                  {{ displayName }}
                  <span
                    class="w-2 h-2 rounded-full bg-emerald-500 inline-block"
                    title="Active"
                  />
                </p>
                <p class="text-[10px] text-slate-500 font-medium">
                  Super Admin
                </p>
              </div>
              <i
                class="ri-arrow-down-s-line text-slate-400 text-xs transition-transform hidden sm:inline"
                :class="showProfileMenu ? 'rotate-180' : ''"
              />
            </button>

            <!-- Profile Popup Menu -->
            <div
              v-if="showProfileMenu"
              class="absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-3 z-50 space-y-2 animate-in fade-in slide-in-from-top-2 duration-150"
            >
              <!-- User Info Header -->
              <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-center">
                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-indigo-600 to-blue-600 text-white font-bold text-base flex items-center justify-center mx-auto mb-1.5 shadow-sm">
                  {{ displayName.charAt(0).toUpperCase() }}
                </div>
                <div class="flex items-center justify-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-500" />
                  <span class="text-xs font-bold text-slate-900 dark:text-white">{{ displayName }}</span>
                </div>
                <span class="text-[11px] text-slate-500 block truncate">{{ adminEmail }}</span>
                <span class="mt-1 inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                  Super Administrator
                </span>
              </div>

              <!-- Menu Items -->
              <div class="space-y-1">
                <Link
                  href="/admin/profile"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-user-3-line text-sm text-slate-500" />
                  <span>Admin Profile</span>
                </Link>

                <Link
                  href="/admin/profile"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-shield-keyhole-line text-sm text-indigo-600" />
                  <span>Account Security (MFA)</span>
                </Link>

                <Link
                  href="/admin/dashboard"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-dashboard-line text-sm text-blue-600" />
                  <span>Master Dashboard</span>
                </Link>
              </div>

              <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                <button
                  type="button"
                  class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 font-semibold text-xs transition-colors cursor-pointer"
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

    <!-- Mobile Navigation Drawer Overlay -->
    <div
      v-if="isMobileDrawerOpen"
      class="fixed inset-0 z-50 lg:hidden flex"
    >
      <div
        class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"
        @click="isMobileDrawerOpen = false"
      />

      <div class="relative w-80 max-w-[85vw] bg-white dark:bg-slate-900 h-full p-5 flex flex-col justify-between shadow-2xl border-r border-slate-200 dark:border-slate-800 z-10 overflow-y-auto">
        <div class="space-y-5">
          <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black flex items-center justify-center text-sm shadow-md">
                CR
              </div>
              <div>
                <span class="font-extrabold text-sm text-slate-900 dark:text-white block">AutoRent</span>
                <span class="text-[9px] font-bold uppercase tracking-wider text-indigo-600">Admin Control</span>
              </div>
            </div>
            <button
              class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg cursor-pointer"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <!-- Quick Actions & Links -->
          <div class="flex gap-2">
            <Link
              href="/"
              class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200"
              @click="isMobileDrawerOpen = false"
            >
              <i
                class="ri-external-link-line"
                style="margin-right: 0.25rem"
              /> Live Site
            </Link>
            <Link
              href="/car-calendar"
              class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200"
              @click="isMobileDrawerOpen = false"
            >
              <i
                class="ri-calendar-line"
                style="margin-right: 0.25rem"
              /> Calendar
            </Link>
          </div>

          <!-- Main Nav Links -->
          <div class="space-y-1">
            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
              Admin Navigation
            </div>
            <Link
              v-for="item in adminNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium'"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-colors"
              @click="isMobileDrawerOpen = false"
            >
              <div class="flex items-center gap-3">
                <i
                  :class="item.icon"
                  class="text-sm text-indigo-600 dark:text-indigo-400"
                />
                <span>{{ item.title }}</span>
              </div>
              <span
                v-if="item.badge"
                class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 font-bold"
              >
                {{ item.badge }}
              </span>
            </Link>

            <!-- Mobile CMS Dropdown Accordion -->
            <div class="pt-2">
              <button
                type="button"
                class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                @click="isMobileCmsExpanded = !isMobileCmsExpanded"
              >
                <div class="flex items-center gap-2">
                  <i class="ri-folder-settings-line text-indigo-600" />
                  <span>CMS Modules (16)</span>
                </div>
                <i
                  class="ri-arrow-down-s-line text-xs transition-transform"
                  :class="isMobileCmsExpanded ? 'rotate-180' : ''"
                />
              </button>

              <div
                v-if="isMobileCmsExpanded"
                class="py-1 space-y-0.5 max-h-48 overflow-y-auto"
                style="padding-left: 1rem; padding-right: 0.25rem"
              >
                <Link
                  v-for="c in cmsQuickLinks"
                  :key="c.label"
                  :href="c.href"
                  class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] text-slate-600 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-slate-800"
                  @click="isMobileDrawerOpen = false"
                >
                  <i
                    :class="c.icon"
                    class="text-xs text-indigo-500"
                  />
                  <span>{{ c.label }}</span>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
          <button
            type="button"
            class="w-full flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 font-semibold text-xs transition-colors cursor-pointer"
            @click="logout"
          >
            <span>Sign Out</span>
            <i class="ri-logout-box-r-line" />
          </button>
        </div>
      </div>
    </div>

    <!-- Main Body Layout with Sidebar - Full Fluid Width & Auto Adjust -->
    <div class="flex-1 flex w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 py-5 sm:py-6 gap-6">
      <!-- Desktop Sidebar Navigation (Matching Image 2 Reference) -->
      <aside class="w-60 xl:w-64 shrink-0 hidden lg:block">
        <div class="sticky top-24 space-y-4">
          <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-1">
            <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
              Navigation
            </div>
            <Link
              v-for="item in adminNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold'"
              class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group"
            >
              <div class="flex items-center gap-3">
                <i
                  :class="item.icon"
                  class="text-sm group-hover:text-indigo-600 transition-colors"
                />
                <span>{{ item.title }}</span>
              </div>
              <span
                v-if="item.badge"
                class="px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 font-bold"
              >
                {{ item.badge }}
              </span>
            </Link>
          </div>

          <!-- AI Support Card (Matching Image 2 Reference) -->
          <div class="p-4 rounded-2xl bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-800 text-white shadow-lg space-y-2 text-xs relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-xl pointer-events-none" />
            <h4 class="font-bold text-sm">
              Need Help?
            </h4>
            <p class="text-[11px] text-indigo-100/90 leading-relaxed">
              Ask our AI assistant for instant fleet, CMS, and booking dispute guidance.
            </p>
            <Link
              href="/ai-chat"
              class="inline-block mt-1 px-3.5 py-2 rounded-xl bg-white text-indigo-700 font-bold text-[11px] shadow-sm hover:bg-indigo-50 transition-colors"
            >
              Chat with AI &rarr;
            </Link>
          </div>
        </div>
      </aside>

      <!-- Main Content Area - Expands to use full screen -->
      <main class="flex-1 min-w-0 w-full">
        <!-- Flash Alerts -->
        <div
          v-if="flash?.success"
          class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-xs"
        >
          <i class="ri-checkbox-circle-fill text-emerald-600 text-base shrink-0" />
          <span>{{ flash.success }}</span>
        </div>
        <div
          v-if="flash?.error"
          class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5 shadow-xs"
        >
          <i class="ri-error-warning-fill text-rose-600 text-base shrink-0" />
          <span>{{ flash.error }}</span>
        </div>

        <slot />
      </main>
    </div>

    <!-- Dedicated Admin System Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-xs text-slate-500">
      <div class="w-full mx-auto px-3 sm:px-6 lg:px-8 xl:px-10 2xl:px-12 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="font-bold text-slate-700 dark:text-slate-300">AutoRent Super Admin Hub</span>
          <span class="text-slate-300 dark:text-slate-700">|</span>
          <span class="flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
            All Systems Operational
          </span>
        </div>

        <div class="flex items-center gap-4 text-[11px] text-slate-400">
          <span>Enterprise v2.5</span>
          <span>&copy; 2026 AutoRent Global Systems</span>
          <Link
            href="/admin/cms"
            class="hover:text-indigo-600 transition-colors"
          >
            CMS Control
          </Link>
          <Link
            href="/admin/profile"
            class="hover:text-indigo-600 transition-colors"
          >
            Security
          </Link>
        </div>
      </div>
    </footer>
  </div>
</template>

