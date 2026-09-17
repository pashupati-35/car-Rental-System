<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import { useAdminTheme, type AdminThemeStyle } from '@/composable/useAdminTheme'
import { useSiteSettings } from '@/composable/useSiteSettings'

const page = usePage()
const { theme, setTheme, initTheme } = useAdminTheme()
const { companyName } = useSiteSettings()

const isMobileDrawerOpen = ref(false)
const showProfileMenu = ref(false)
const showThemeMenu = ref(false)

const auth = computed(() => page.props.auth as any)
const admin = computed(() => auth.value?.admin || auth.value?.user)

const displayName = computed(() => {
  return admin.value?.name || admin.value?.full_name || 'CRM Manager'
})

const adminEmail = computed(() => {
  return admin.value?.email || 'crm@crmcarrental.com'
})

const adminAvatarUrl = computed(() => {
  const a = admin.value
  if (!a) return null
  if (a.image_url) return a.image_url
  if (a.image_path?.original) return a.image_path.original
  if (a.image_path?.thumb) return a.image_path.thumb
  if (typeof a.image_path === 'string' && a.image_path) return a.image_path
  if (typeof a.image === 'string' && a.image) {
    return a.image.startsWith('http') ? a.image : `/${a.image.replace(/^\/+/, '')}`
  }
  if (typeof a.avatar === 'string' && a.avatar) {
    return a.avatar.startsWith('http') ? a.avatar : `/${a.avatar.replace(/^\/+/, '')}`
  }
  
  return null
})

const flashSuccess = ref('')
const flashError = ref('')

watch(
  () => page.props.flash as any,
  (newFlash: any) => {
    if (newFlash?.success) flashSuccess.value = newFlash.success
    if (newFlash?.error) flashError.value = newFlash.error
  },
  { immediate: true, deep: true },
)

const currentUrl = computed(() => page.url)

const crmNav = computed(() => [
  { title: 'CRM Command Hub', icon: 'ri-dashboard-3-line', href: '/crm/dashboard', badge: 'Live' },
  { title: 'Leads & Inquiries', icon: 'ri-user-search-line', href: '/crm/leads', badge: '' },
  { title: 'Deals & Sales Pipeline', icon: 'ri-kanban-view', href: '/crm/deals', badge: '' },
  { title: 'Rental Quotations (CPQ)', icon: 'ri-file-list-3-line', href: '/crm/quotations', badge: '' },
  { title: 'Customer 360 Directory', icon: 'ri-user-smile-line', href: '/crm/customers', badge: '' },
  { title: 'Fleet Owner 360', icon: 'ri-building-line', href: '/crm/owners', badge: '' },
  { title: 'Support & Incident Desk', icon: 'ri-customer-service-2-line', href: '/crm/tickets', badge: '' },
  { title: 'Corporate B2B Fleet', icon: 'ri-building-4-line', href: '/crm/corporate-accounts', badge: '' },
  { title: 'Account Security & MFA', icon: 'ri-shield-keyhole-line', href: '/crm/security', badge: '' },
])

const isActive = (href: string) => {
  const current = currentUrl.value.split('?')[0] ?? ''
  if (href === '/crm/dashboard') {
    return current === '/crm/dashboard' || current === '/crm' || current === '/dashboard'
  }
  
  return current.startsWith(href)
}

const logout = () => {
  router.post('/logout')
}

const themeStyles: { id: AdminThemeStyle; label: string; dotClass: string }[] = [
  { id: 'light', label: 'Light Mode', dotClass: 'bg-amber-400' },
  { id: 'dark', label: 'Dark Mode', dotClass: 'bg-slate-400' },
  { id: 'midnight', label: 'Midnight Blue', dotClass: 'bg-blue-500' },
  { id: 'system', label: 'System Default', dotClass: 'bg-emerald-500' },
]

const handleOutsideClick = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!target.closest('#crm-profile-menu-container')) {
    showProfileMenu.value = false
  }
  if (!target.closest('#crm-theme-menu-container')) {
    showThemeMenu.value = false
  }
}

onMounted(() => {
  initTheme()
  document.addEventListener('click', handleOutsideClick)
})

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick)
})
</script>

<template>
  <div class="min-h-screen font-sans antialiased text-slate-900 dark:text-slate-100 flex flex-col bg-slate-50 dark:bg-slate-950">
    <!-- Top Header Bar -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 px-4 sm:px-6 py-2.5 flex items-center justify-between shadow-xs">
      <!-- Left: Mobile toggle & CRM Branding -->
      <div class="flex items-center gap-3">
        <button
          type="button"
          class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
          @click="isMobileDrawerOpen = true"
        >
          <i class="ri-menu-line text-xl" />
        </button>

        <Link
          href="/crm/dashboard"
          class="flex items-center gap-2.5"
        >
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
            <i class="ri-shield-user-line" />
          </div>
          <div class="flex flex-col">
            <span class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white uppercase leading-none">
              {{ companyName || 'Car Rental' }}
            </span>
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mt-0.5">
              CRM Portal
            </span>
          </div>
        </Link>
      </div>

      <!-- Center: Quick Actions (Desktop) -->
      <div class="hidden md:flex items-center gap-2">
        <Link
          href="/crm/leads"
          class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition"
        >
          <i class="ri-user-add-line text-indigo-500" />
          <span>+ Lead</span>
        </Link>
        <Link
          href="/crm/deals"
          class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition"
        >
          <i class="ri-kanban-view text-emerald-500" />
          <span>+ Deal</span>
        </Link>
        <Link
          href="/crm/quotations/create"
          class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition"
        >
          <i class="ri-file-add-line text-purple-500" />
          <span>+ Quote</span>
        </Link>
        <Link
          href="/crm/tickets"
          class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition"
        >
          <i class="ri-customer-service-2-line text-rose-500" />
          <span>Support Desk</span>
        </Link>
      </div>

      <!-- Right: Theme Switcher, Admin Portal Link & User Profile -->
      <div class="flex items-center gap-2.5">
        <!-- Switch to Admin Portal link -->
        <a
          href="/admin/dashboard"
          class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
          title="Switch to Master Operations Admin Portal"
        >
          <i class="ri-dashboard-line" />
          <span>Admin Portal</span>
        </a>

        <!-- Theme Selector -->
        <div
          id="crm-theme-menu-container"
          class="relative"
        >
          <button
            type="button"
            class="p-2 rounded-xl text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            title="Switch Theme"
            @click="showThemeMenu = !showThemeMenu"
          >
            <i class="ri-palette-line text-lg" />
          </button>

          <div
            v-if="showThemeMenu"
            class="absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl p-2 z-50 space-y-1"
          >
            <button
              v-for="th in themeStyles"
              :key="th.id"
              type="button"
              class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
              @click="setTheme(th.id); showThemeMenu = false"
            >
              <div class="flex items-center gap-2">
                <span
                  class="w-2.5 h-2.5 rounded-full"
                  :class="th.dotClass"
                />
                <span>{{ th.label }}</span>
              </div>
              <i
                v-if="theme === th.id"
                class="ri-check-line text-indigo-600 text-sm"
              />
            </button>
          </div>
        </div>

        <!-- User Profile Dropdown -->
        <div
          id="crm-profile-menu-container"
          class="relative"
        >
          <button
            type="button"
            class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
            @click="showProfileMenu = !showProfileMenu"
          >
            <div class="w-8 h-8 rounded-xl overflow-hidden bg-gradient-to-tr from-emerald-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs border border-emerald-500/20">
              <img
                v-if="adminAvatarUrl"
                :src="adminAvatarUrl"
                class="w-full h-full object-cover"
                :alt="displayName"
                @error="(e: any) => (e.target.style.display = 'none')"
              >
              <span v-else>{{ displayName.charAt(0).toUpperCase() }}</span>
            </div>
            <div class="hidden sm:flex flex-col text-left">
              <span class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                {{ displayName }}
              </span>
              <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">
                CRM Staff
              </span>
            </div>
            <i class="ri-arrow-down-s-line text-xs text-slate-400" />
          </button>

          <div
            v-if="showProfileMenu"
            class="absolute right-0 mt-2 w-60 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl p-2 z-50 space-y-1 font-sans"
          >
            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl overflow-hidden bg-gradient-to-tr from-emerald-600 to-indigo-600 text-white font-bold text-sm flex items-center justify-center shrink-0 shadow-xs border border-emerald-500/20">
                <img
                  v-if="adminAvatarUrl"
                  :src="adminAvatarUrl"
                  class="w-full h-full object-cover"
                  :alt="displayName"
                  @error="(e: any) => (e.target.style.display = 'none')"
                >
                <span v-else>{{ displayName.charAt(0).toUpperCase() }}</span>
              </div>
              <div class="min-w-0 flex-1">
                <div class="font-bold text-xs text-slate-900 dark:text-white truncate">
                  {{ displayName }}
                </div>
                <div class="text-[11px] text-slate-400 truncate">
                  {{ adminEmail }}
                </div>
              </div>
            </div>

            <Link
              href="/crm/security"
              class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
              @click="showProfileMenu = false"
            >
              <i class="ri-shield-keyhole-line text-sm text-indigo-500" />
              <span>Security & MFA</span>
            </Link>

            <button
              type="button"
              class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition text-left cursor-pointer"
              @click="logout"
            >
              <i class="ri-logout-box-r-line text-sm" />
              <span>Sign Out</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Body Layout -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Desktop Sidebar -->
      <aside class="hidden lg:flex w-64 flex-col bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800/80 p-4 shrink-0 overflow-y-auto">
        <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2 px-3">
          CRM Navigation
        </div>

        <nav class="space-y-1.5 flex-1">
          <Link
            v-for="item in crmNav"
            :key="item.title"
            :href="item.href"
            :class="isActive(item.href)
              ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/30'
              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-semibold'"
            class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-[13.5px] transition-all group cursor-pointer"
          >
            <div class="flex items-center gap-3 min-w-0">
              <i
                :class="[
                  item.icon,
                  isActive(item.href) ? 'text-white' : 'text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200'
                ]"
                class="text-lg shrink-0"
              />
              <span class="truncate">{{ item.title }}</span>
            </div>
            <span
              v-if="item.badge"
              class="px-2 py-0.5 rounded-full text-xs font-mono font-bold shrink-0"
              :class="isActive(item.href) ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'"
            >
              {{ item.badge }}
            </span>
          </Link>
        </nav>

        <!-- Sidebar footer info -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
          <span class="font-semibold text-emerald-600">Enterprise CRM v2.0</span>
          <span>Online</span>
        </div>
      </aside>

      <!-- Mobile Sidebar Drawer -->
      <div
        v-if="isMobileDrawerOpen"
        class="fixed inset-0 z-50 lg:hidden flex"
      >
        <div
          class="fixed inset-0 bg-black/60 backdrop-blur-xs"
          @click="isMobileDrawerOpen = false"
        />
        <div class="relative w-72 bg-white dark:bg-slate-900 p-5 flex flex-col z-10 space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl overflow-hidden bg-gradient-to-tr from-emerald-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs border border-emerald-500/20">
                <img
                  v-if="adminAvatarUrl"
                  :src="adminAvatarUrl"
                  class="w-full h-full object-cover"
                  :alt="displayName"
                  @error="(e: any) => (e.target.style.display = 'none')"
                >
                <span v-else>{{ displayName.charAt(0).toUpperCase() }}</span>
              </div>
              <div class="flex flex-col">
                <span class="font-bold text-xs text-slate-900 dark:text-white leading-tight truncate max-w-[150px]">{{ displayName }}</span>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">CRM Portal</span>
              </div>
            </div>
            <button
              class="text-slate-400 hover:text-slate-600"
              @click="isMobileDrawerOpen = false"
            >
              <i class="ri-close-line text-xl" />
            </button>
          </div>

          <nav class="space-y-1.5 flex-1 overflow-y-auto">
            <Link
              v-for="item in crmNav"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) ? 'bg-emerald-600 text-white font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
              class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm"
              @click="isMobileDrawerOpen = false"
            >
              <div class="flex items-center gap-3">
                <i
                  :class="item.icon"
                  class="text-lg"
                />
                <span>{{ item.title }}</span>
              </div>
            </Link>
          </nav>
        </div>
      </div>

      <!-- Main Content Area -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <!-- Flash Alert Messages -->
        <div
          v-if="flashSuccess"
          class="mb-5 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between"
        >
          <div class="flex items-center gap-2">
            <i class="ri-checkbox-circle-fill text-lg text-emerald-600" />
            <span>{{ flashSuccess }}</span>
          </div>
          <button
            class="text-emerald-500 hover:text-emerald-700"
            @click="flashSuccess = ''"
          >
            <i class="ri-close-line text-lg" />
          </button>
        </div>

        <div
          v-if="flashError"
          class="mb-5 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between"
        >
          <div class="flex items-center gap-2">
            <i class="ri-error-warning-fill text-lg text-rose-600" />
            <span>{{ flashError }}</span>
          </div>
          <button
            class="text-rose-500 hover:text-rose-700"
            @click="flashError = ''"
          >
            <i class="ri-close-line text-lg" />
          </button>
        </div>

        <!-- Page Slots -->
        <slot />
      </main>
    </div>
  </div>
</template>
