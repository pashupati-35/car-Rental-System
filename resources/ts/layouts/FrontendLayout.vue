<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import MessageBox from '@/components/MessageBox.vue'
import { useFrontendTheme } from '@/composable/useFrontendTheme'
import { useSiteSettings } from '@/composable/useSiteSettings'

const page = usePage()
const { theme, toggleTheme, initTheme } = useFrontendTheme()
const { logoUrl, footerLogoUrl, companyName } = useSiteSettings()
const auth = computed(() => page.props.auth as any)
const user = computed(() => auth.value?.admin || auth.value?.owner || auth.value?.customer || auth.value?.user)
const showProfileMenu = ref(false)

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

const role = computed(() => {
  if (auth.value?.admin) return 'Admin'
  if (auth.value?.owner) return 'Owner'
  if (auth.value?.customer) return 'Customer'
  
  return 'User'
})

const portalName = computed(() => {
  if (auth.value?.admin) return 'Admin'
  if (auth.value?.owner) return 'Fleet Owner'
  if (auth.value?.customer) return 'Customer'
  
  return 'User'
})

const portalDashboardUrl = computed(() => {
  if (auth.value?.admin) {
    const base = auth.value?.adminPortalUrl || ''
    return base ? `${base}/admin/dashboard` : '/admin/dashboard'
  }
  if (auth.value?.owner) {
    return '/owner/dashboard'
  }
  return '/customer/dashboard'
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

const logout = () => {
  const logoutRoute = role.value === 'Admin' 
    ? '/admin/logout' 
    : role.value === 'Owner' 
      ? '/owner/logout' 
      : '/customer/logout'

  router.post(logoutRoute)
}

const showLoginMenu = ref(false)
const showRegisterMenu = ref(false)

const toggleLoginMenu = () => {
  showLoginMenu.value = !showLoginMenu.value
  if (showLoginMenu.value) showRegisterMenu.value = false
}

const toggleRegisterMenu = () => {
  showRegisterMenu.value = !showRegisterMenu.value
  if (showRegisterMenu.value) showLoginMenu.value = false
}

const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('#front-login-dropdown')) {
    showLoginMenu.value = false
  }
  if (!target.closest('#front-register-dropdown')) {
    showRegisterMenu.value = false
  }
}

const isActive = (href: string) => {
  const current = page.url.split('?')[0]
  if (href === '/') return current === '/'
  if (href === '/cars') return current === '/cars' || current.startsWith('/cars/')
  if (href === '/car-calendar') return current.startsWith('/car-calendar')
  if (href === '/ai-chat') return current.startsWith('/ai-chat')
  
  return current.startsWith(href)
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
  <div class="min-h-screen flex flex-col bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans transition-colors duration-200">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 shadow-sm transition-colors duration-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <Link
          href="/"
          class="flex items-center gap-2.5"
        >
          <img
            v-if="logoUrl"
            :src="logoUrl"
            :alt="companyName"
            class="h-9 max-w-[150px] object-contain"
          />
          <template v-else>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-blue-500/20">
              CR
            </div>
            <span class="font-extrabold text-xl tracking-tight text-gray-900 dark:text-white">{{ companyName }}</span>
          </template>
        </Link>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
          <Link
            href="/"
            :class="isActive('/') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium'"
            class="transition-colors"
          >
            Home
          </Link>
          <Link
            href="/cars"
            :class="isActive('/cars') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium'"
            class="transition-colors"
          >
            Browse Fleet
          </Link>
          <Link
            href="/car-calendar"
            :class="isActive('/car-calendar') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium'"
            class="transition-colors"
          >
            Availability Calendar
          </Link>
          <Link
            href="/ai-chat"
            :class="isActive('/ai-chat') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium'"
            class="transition-colors flex items-center gap-1.5"
          >
            <span class="text-amber-500">✨</span> AI Assistant
          </Link>
        </nav>

        <div class="flex items-center gap-2.5">
          <!-- Theme Toggle Switcher -->
          <button
            type="button"
            class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 flex items-center justify-center text-base transition-all cursor-pointer shadow-2xs"
            :title="theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
            @click="toggleTheme"
          >
            <i
              v-if="theme === 'dark'"
              class="ri-sun-line text-amber-400 text-base"
            />
            <i
              v-else
              class="ri-moon-line text-slate-700 text-base"
            />
          </button>
          <!-- Login / Sign In Choice Dropdown -->
          <div
            id="front-login-dropdown"
            class="relative"
          >
            <button
              type="button"
              class="px-3.5 py-2 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors flex items-center gap-1.5 cursor-pointer"
              @click="toggleLoginMenu"
            >
              <i class="ri-login-box-line text-sm text-blue-600 dark:text-blue-400" />
              <span>Sign In</span>
              <i
                class="ri-arrow-down-s-line text-xs transition-transform duration-200"
                :class="{ 'rotate-180': showLoginMenu }"
              />
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showLoginMenu"
              class="absolute right-0 mt-2 w-72 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-2xl py-2 z-50 animate-in fade-in zoom-in-95 duration-150"
            >
              <div class="px-3.5 py-2 border-b border-gray-100 dark:border-gray-800 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                Choose Sign In Portal
              </div>

              <!-- Customer Login -->
              <a
                href="/customer/login"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-blue-50/60 dark:hover:bg-blue-950/30 transition-colors group cursor-pointer"
                @click="showLoginMenu = false"
              >
                <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                  <i class="ri-user-line text-base" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 flex items-center justify-between">
                    <span>Customer Login</span>
                    <i class="ri-external-link-line text-xs opacity-60" />
                  </div>
                  <div class="text-[11px] text-gray-500 truncate">Book vehicles & view bookings</div>
                </div>
              </a>

              <!-- Owner Login -->
              <a
                href="/owner/login"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 transition-colors group cursor-pointer"
                @click="showLoginMenu = false"
              >
                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                  <i class="ri-car-line text-base" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 flex items-center justify-between">
                    <span>Fleet Owner Partner</span>
                    <i class="ri-external-link-line text-xs opacity-60" />
                  </div>
                  <div class="text-[11px] text-gray-500 truncate">Manage fleet cars & earnings</div>
                </div>
              </a>            
            </div>
          </div>

          <!-- Register Choice Dropdown -->
          <div
            id="front-register-dropdown"
            class="relative"
          >
            <button
              type="button"
              class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5 cursor-pointer"
              @click="toggleRegisterMenu"
            >
              <span>Register</span>
              <i
                class="ri-arrow-down-s-line text-xs transition-transform duration-200"
                :class="{ 'rotate-180': showRegisterMenu }"
              />
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showRegisterMenu"
              class="absolute right-0 mt-2 w-72 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-2xl py-2 z-50 animate-in fade-in zoom-in-95 duration-150"
            >
              <div class="px-3.5 py-2 border-b border-gray-100 dark:border-gray-800 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                Create An Account
              </div>

              <!-- Customer Register -->
              <a
                href="/customer/register"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-blue-50/60 dark:hover:bg-blue-950/30 transition-colors group cursor-pointer"
                @click="showRegisterMenu = false"
              >
                <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                  <i class="ri-user-add-line text-base" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 flex items-center justify-between">
                    <span>Customer Account</span>
                    <i class="ri-external-link-line text-xs opacity-60" />
                  </div>
                  <div class="text-[11px] text-gray-500 truncate">Book vehicles & special discounts</div>
                </div>
              </a>

              <!-- Owner Register -->
              <a
                href="/owner/register"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 transition-colors group cursor-pointer border-t border-gray-50 dark:border-gray-800"
                @click="showRegisterMenu = false"
              >
                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                  <i class="ri-car-add-line text-base" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 flex items-center justify-between">
                    <span>Fleet Owner Partner</span>
                    <i class="ri-external-link-line text-xs opacity-60" />
                  </div>
                  <div class="text-[11px] text-gray-500 truncate">Earn revenue listing your vehicles</div>
                </div>
              </a>
            </div>
          </div>

          <!-- Active Portal Shortcut & Logout (if user is logged in) -->
          <template v-if="user">
            <a
              :href="portalDashboardUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="hidden sm:inline-flex px-3 py-2 rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50/60 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-xs font-bold hover:bg-blue-100 transition-colors items-center gap-1.5"
              :title="'Go to ' + portalName + ' Dashboard'"
            >
              <i class="ri-dashboard-line text-xs" />
              <span>{{ portalName }}</span>
              <i class="ri-external-link-line text-[10px] opacity-70" />
            </a>

            <button
              type="button"
              class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold transition-colors cursor-pointer"
              title="Sign Out"
              @click="logout"
            >
              <i class="ri-logout-box-r-line text-base" />
            </button>
          </template>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <div
        v-if="flashSuccess || flashError"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6"
      >
        <MessageBox
          v-model="flashSuccess"
          type="success"
          class="mb-4"
        />
        <MessageBox
          v-model="flashError"
          type="error"
          class="mb-4"
        />
      </div>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-400 py-12 border-t border-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8 border-b border-gray-800">
          <div class="space-y-3">
            <div class="flex items-center gap-2">
              <img
                v-if="footerLogoUrl"
                :src="footerLogoUrl"
                :alt="companyName"
                class="h-8 max-w-[140px] object-contain"
              />
              <template v-else>
                <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                  CR
                </div>
                <span class="font-extrabold text-lg text-white">{{ companyName }}</span>
              </template>
            </div>
            <p class="text-xs text-gray-400 leading-relaxed">
              Premium car rental marketplace with zero-overlap calendar booking, dedicated chauffeur rosters, and instant verification.
            </p>
          </div>

          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-200 mb-3">
              Customer Services
            </h4>
            <ul class="space-y-2 text-xs">
              <li>
                <Link
                  href="/cars"
                  class="hover:text-white transition-colors"
                >
                  Browse Available Cars
                </Link>
              </li>
              <li>
                <Link
                  href="/car-calendar"
                  class="hover:text-white transition-colors"
                >
                  Calendar Schedule
                </Link>
              </li>
              <li>
                <Link
                  href="/customer/register"
                  class="hover:text-white transition-colors"
                >
                  Customer Register
                </Link>
              </li>
              <li>
                <Link
                  href="/customer/login"
                  class="hover:text-white transition-colors"
                >
                  Customer Sign In
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-200 mb-3">
              Fleet Owner Portal
            </h4>
            <ul class="space-y-2 text-xs">
              <li>
                <Link
                  href="/owner/register"
                  class="hover:text-emerald-400 transition-colors"
                >
                  Register as Fleet Owner
                </Link>
              </li>
              <li>
                <Link
                  href="/owner/login"
                  class="hover:text-emerald-400 transition-colors"
                >
                  Owner Login Portal
                </Link>
              </li>
              <li>
                <Link
                  href="/owner/dashboard"
                  class="hover:text-emerald-400 transition-colors"
                >
                  Fleet Roster & Earnings
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-200 mb-3">
              Support & Help
            </h4>
            <ul class="space-y-2 text-xs">
              <li>
                <Link
                  href="/ai-chat"
                  class="hover:text-amber-400 transition-colors flex items-center gap-1"
                >
                  <span>✨</span> AI Fleet Assistant
                </Link>
              </li>
              <li>
                <Link
                  href="/car-calendar"
                  class="hover:text-white transition-colors"
                >
                  Availability Schedule
                </Link>
              </li>
              <li>
                <Link
                  href="/cars"
                  class="hover:text-white transition-colors"
                >
                  Vehicle Directory
                </Link>
              </li>
            </ul>
          </div>
        </div>

        <div class="pt-6 text-center text-xs text-gray-500">
          &copy; 2026 Car Rental & Fleet Management System. All rights reserved.
        </div>
      </div>
    </footer>
  </div>
</template>
