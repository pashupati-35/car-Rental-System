<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import MessageBox from '@/components/MessageBox.vue'

const page = usePage()

const showProfileMenu = ref(false)
const showCmsMenu = ref(false)

const auth = computed(() => page.props.auth as any)
const user = computed(() => auth.value?.admin || auth.value?.owner || auth.value?.customer || auth.value?.user)

const role = computed(() => {
  if (auth.value?.admin) return 'Admin'
  if (auth.value?.owner) return 'Owner'
  if (auth.value?.customer) return 'Customer'
  
  return 'User'
})

const rolePrefix = computed(() => {
  if (role.value === 'Admin') return 'admin'
  if (role.value === 'Owner') return 'owner'
  
  return 'customer'
})

const displayName = computed(() => {
  return user.value?.name || user.value?.full_name || 'Account'
})

const userEmail = computed(() => {
  return user.value?.email || ''
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

const adminNav = [
  { title: 'Master Dashboard', icon: 'ri-dashboard-line', href: '/admin/dashboard' },
  { title: 'Fleet Cars Verification', icon: 'ri-car-line', href: '/admin/cars' },
  { title: 'Driver Directory', icon: 'ri-user-star-line', href: '/admin/drivers' },
  { title: 'Rental Bookings', icon: 'ri-calendar-check-line', href: '/admin/booked-cars' },
  { title: 'Fleet Owners', icon: 'ri-building-line', href: '/admin/owners' },
  { title: 'Customers', icon: 'ri-user-smile-line', href: '/admin/customers' },
  { title: 'Email Templates', icon: 'ri-mail-settings-line', href: '/admin/email-templates' },
  { title: 'Admin Profile & Security', icon: 'ri-shield-user-line', href: '/admin/profile' },
]

const ownerNav = [
  { title: 'Fleet Dashboard', icon: 'ri-dashboard-line', href: '/owner/dashboard' },
  { title: 'My Fleet Cars', icon: 'ri-car-line', href: '/owner/cars' },
  { title: 'Driver Roster', icon: 'ri-user-follow-line', href: '/owner/drivers' },
  { title: 'Add New Car', icon: 'ri-add-circle-line', href: '/owner/cars/create' },
  { title: 'Bookings & Calendar', icon: 'ri-calendar-line', href: '/owner/bookings' },
  { title: 'Owner Profile', icon: 'ri-user-settings-line', href: '/owner/profile' },
  { title: 'Account Security & MFA', icon: 'ri-shield-keyhole-line', href: '/owner/security' },
]

const customerNav = [
  { title: 'Customer Dashboard', icon: 'ri-dashboard-line', href: '/customer/dashboard' },
  { title: 'Browse Fleet', icon: 'ri-car-line', href: '/cars' },
  { title: 'My Bookings', icon: 'ri-book-read-line', href: '/customer/bookings' },
  { title: 'Customer Profile', icon: 'ri-user-smile-line', href: '/customer/profile' },
  { title: 'Account Security & MFA', icon: 'ri-shield-keyhole-line', href: '/customer/security' },
]

const navItems = computed(() => {
  if (role.value === 'Admin') return adminNav
  if (role.value === 'Owner') return ownerNav
  
  return customerNav
})

const isActive = (href: string) => {
  const current = page.url.split('?')[0]
  if (href === `/${rolePrefix.value}/dashboard`) {
    return current === `/${rolePrefix.value}/dashboard` || current === `/${rolePrefix.value}`
  }
  if (href === `/${rolePrefix.value}/profile`) {
    return current === `/${rolePrefix.value}/profile`
  }
  if (href === `/${rolePrefix.value}/security`) {
    return current === `/${rolePrefix.value}/security`
  }
  if (href === '/cars') {
    return current === '/cars' || current.startsWith('/cars/')
  }
  if (href === '/car-calendar') {
    return current.startsWith('/car-calendar')
  }
  
  return current.startsWith(href)
}

const cmsItems = computed(() => {
  if (role.value === 'Admin') {
    return [
      { label: 'Fleet Cars Management', href: '/admin/cars', icon: 'ri-car-line' },
      { label: 'Driver Directory', href: '/admin/drivers', icon: 'ri-user-star-line' },
      { label: 'Booking Records', href: '/admin/booked-cars', icon: 'ri-calendar-check-line' },
      { label: 'Email Templates CMS', href: '/admin/email-templates', icon: 'ri-mail-settings-line' },
      { label: 'Public Showroom', href: '/cars', icon: 'ri-store-2-line' },
      { label: 'Availability Calendar', href: '/car-calendar', icon: 'ri-calendar-line' },
      { label: 'AI Assistant', href: '/ai-chat', icon: 'ri-sparkling-line' },
    ]
  } else if (role.value === 'Owner') {
    return [
      { label: 'My Fleet Cars', href: '/owner/cars', icon: 'ri-car-line' },
      { label: 'Driver Roster', href: '/owner/drivers', icon: 'ri-user-follow-line' },
      { label: 'Add New Car', href: '/owner/cars/create', icon: 'ri-add-circle-line' },
      { label: 'Calendar Schedule', href: '/car-calendar', icon: 'ri-calendar-line' },
      { label: 'Public Showroom', href: '/cars', icon: 'ri-store-2-line' },
      { label: 'AI Assistant', href: '/ai-chat', icon: 'ri-sparkling-line' },
    ]
  } else {
    return [
      { label: 'Browse Cars Showroom', href: '/cars', icon: 'ri-car-line' },
      { label: 'Availability Calendar', href: '/car-calendar', icon: 'ri-calendar-line' },
      { label: 'My Rental Bookings', href: '/customer/bookings', icon: 'ri-book-read-line' },
      { label: 'AI Assistant', href: '/ai-chat', icon: 'ri-sparkling-line' },
    ]
  }
})

const logout = () => {
  const logoutRoute = role.value === 'Admin' 
    ? '/admin/logout' 
    : role.value === 'Owner' 
      ? '/owner/logout' 
      : '/customer/logout'

  router.post(logoutRoute)
}

const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('#profile-dropdown-container')) {
    showProfileMenu.value = false
  }
  if (!target.closest('#cms-dropdown-container')) {
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
  <div class="min-h-screen bg-gray-50/70 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col font-sans">
    <!-- Top Modern Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        <!-- Left: Logo & Portal Badge -->
        <div class="flex items-center gap-3">
          <Link
            href="/"
            class="flex items-center gap-2.5"
          >
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-black text-lg shadow-md shadow-blue-500/20">
              CR
            </div>
            <span class="font-extrabold text-lg tracking-tight text-gray-900 dark:text-white hidden sm:inline">AutoRent</span>
          </Link>
          <span
            :class="role === 'Admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950 dark:text-indigo-300' : role === 'Owner' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950 dark:text-blue-300'"
            class="text-[10px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border"
          >
            {{ role }} Portal
          </span>
        </div>

        <!-- Center: Quick Nav & Owner/Customer Links (NO CMS for non-admin) -->
        <div class="hidden md:flex items-center gap-2">
          <!-- CMS Dropdown ONLY for Admin (if accessed via AppLayout fallback) -->
          <div
            v-if="role === 'Admin'"
            id="cms-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center gap-1.5 transition-colors"
              @click="showCmsMenu = !showCmsMenu; showProfileMenu = false"
            >
              <i class="ri-dashboard-3-line text-sm text-blue-600" />
              <span>CMS & Fleet</span>
              <i
                class="ri-arrow-down-s-line text-xs transition-transform"
                :class="showCmsMenu ? 'rotate-180' : ''"
              />
            </button>

            <!-- CMS Dropdown Menu -->
            <div
              v-if="showCmsMenu"
              class="absolute left-0 mt-2 w-64 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-2xl p-2 z-50 space-y-1"
            >
              <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                Master CMS Suite
              </div>
              <Link
                v-for="item in cmsItems"
                :key="item.label"
                :href="item.href"
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-gray-800 transition-colors"
                @click="showCmsMenu = false"
              >
                <i
                  :class="item.icon"
                  class="text-sm text-blue-600"
                />
                <span>{{ item.label }}</span>
              </Link>
            </div>
          </div>

          <Link
            :href="'/' + rolePrefix + '/dashboard'"
            :class="isActive('/' + rolePrefix + '/dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium'"
            class="px-3 py-2 rounded-xl text-xs transition-colors"
          >
            Dashboard
          </Link>
          <Link
            href="/cars"
            :class="isActive('/cars') ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium'"
            class="px-3 py-2 rounded-xl text-xs transition-colors"
          >
            Browse Fleet
          </Link>
          <Link
            href="/car-calendar"
            :class="isActive('/car-calendar') ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium'"
            class="px-3 py-2 rounded-xl text-xs transition-colors"
          >
            Calendar
          </Link>
        </div>

        <!-- Right Side: Alerts, Home & User Profile Dropdown -->
        <div class="flex items-center gap-3">
          <Link
            href="/"
            title="View Public Website"
            class="p-2 rounded-xl text-gray-500 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 text-sm transition-colors hidden sm:inline-flex items-center gap-1"
          >
            <i class="ri-external-link-line" />
            <span class="text-xs">Live Site</span>
          </Link>

          <!-- Profile Dropdown Container -->
          <div
            id="profile-dropdown-container"
            class="relative"
          >
            <button
              type="button"
              class="flex items-center gap-2.5 p-1.5 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md transition-all cursor-pointer"
              style="padding-right: 0.75rem"
              @click="showProfileMenu = !showProfileMenu; showCmsMenu = false"
            >
              <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                {{ displayName.charAt(0).toUpperCase() }}
              </div>
              <div class="hidden sm:block text-left">
                <p class="text-xs font-bold text-gray-900 dark:text-white leading-tight flex items-center gap-1.5">
                  {{ displayName }}
                  <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block" />
                </p>
                <p class="text-[10px] text-gray-500">
                  {{ role }}
                </p>
              </div>
              <i
                class="ri-arrow-down-s-line text-gray-400 text-xs transition-transform"
                :class="showProfileMenu ? 'rotate-180' : ''"
              />
            </button>

            <!-- Profile Popup Menu (Matching Reference Image) -->
            <div
              v-if="showProfileMenu"
              class="absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-2xl p-3 z-50 space-y-2 animate-in fade-in slide-in-from-top-2 duration-150"
            >
              <!-- User Info Header -->
              <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 text-center">
                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-base flex items-center justify-center mx-auto mb-1.5 shadow-sm">
                  {{ displayName.charAt(0).toUpperCase() }}
                </div>
                <div class="flex items-center justify-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-500" />
                  <span class="text-xs font-bold text-gray-900 dark:text-white">{{ displayName }}</span>
                </div>
                <span class="text-[11px] text-gray-500 block truncate">{{ userEmail }}</span>
                <span class="mt-1 inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                  {{ role }}
                </span>
              </div>

              <!-- Menu Items -->
              <div class="space-y-1">
                <Link
                  :href="'/' + rolePrefix + '/profile'"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-user-3-line text-sm text-gray-500" />
                  <span>Manage profile</span>
                </Link>

                <Link
                  :href="'/' + rolePrefix + '/security'"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-shield-keyhole-line text-sm text-blue-600" />
                  <span>Account security (MFA)</span>
                </Link>

                <Link
                  :href="'/' + rolePrefix + '/dashboard'"
                  class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  @click="showProfileMenu = false"
                >
                  <i class="ri-dashboard-line text-sm text-indigo-600" />
                  <span>Dashboard</span>
                </Link>
              </div>

              <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
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

    <!-- Main Body Layout with Sidebar -->
    <div class="flex-1 flex max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 gap-6">
      <!-- Sidebar Navigation -->
      <aside class="w-64 shrink-0 hidden lg:block">
        <div class="sticky top-24 space-y-4">
          <div class="p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm space-y-1">
            <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">
              Navigation
            </div>
            <Link
              v-for="item in navItems"
              :key="item.title"
              :href="item.href"
              :class="isActive(item.href) ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-bold shadow-xs' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-semibold'"
              class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all"
            >
              <i
                :class="item.icon"
                class="text-sm"
              />
              <span>{{ item.title }}</span>
            </Link>
          </div>

          <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg space-y-2 text-xs">
            <h4 class="font-bold">
              Need Help?
            </h4>
            <p class="text-[11px] text-blue-100">
              Ask our AI assistant for instant fleet and booking support.
            </p>
            <Link
              href="/ai-chat"
              class="inline-block mt-1 px-3 py-1.5 rounded-lg bg-white text-blue-700 font-bold text-[11px]"
            >
              Chat with AI &rarr;
            </Link>
          </div>
        </div>
      </aside>

      <!-- Main Content Area -->
      <main class="flex-1 min-w-0">
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
  </div>
</template>
