<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import MessageBox from '@/components/MessageBox.vue'

const page = usePage()
const auth = computed(() => page.props.auth as any)
const user = computed(() => auth.value?.admin || auth.value?.owner || auth.value?.customer || auth.value?.user)
const showProfileMenu = ref(false)

const flashSuccess = ref('')
const flashError = ref('')

watch(
  () => page.props.flash as any,
  (newFlash) => {
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

const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('#front-profile-dropdown')) {
    showProfileMenu.value = false
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
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <Link
          href="/"
          class="flex items-center gap-2.5"
        >
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-blue-500/20">
            CR
          </div>
          <span class="font-extrabold text-xl tracking-tight text-gray-900 dark:text-white">AutoRent</span>
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

        <div class="flex items-center gap-3">
          <!-- When Logged In: Show Profile Dropdown & Dashboard link -->
          <template v-if="user">
            <Link
              :href="'/' + rolePrefix + '/dashboard'"
              class="hidden sm:inline-flex px-3.5 py-2 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 text-xs font-bold hover:bg-blue-100 transition-colors"
            >
              Dashboard &rarr;
            </Link>

            <!-- Profile Dropdown Menu on Right Side -->
            <div
              id="front-profile-dropdown"
              class="relative"
            >
              <button
                type="button"
                class="flex items-center gap-2 p-1.5 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md transition-all"
                style="padding-right: 0.75rem"
                @click="showProfileMenu = !showProfileMenu"
              >
                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                  {{ displayName.charAt(0).toUpperCase() }}
                </div>
                <span class="text-xs font-bold text-gray-800 dark:text-white hidden sm:inline">{{ displayName }}</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500" />
                <i
                  class="ri-arrow-down-s-line text-gray-400 text-xs transition-transform"
                  :class="showProfileMenu ? 'rotate-180' : ''"
                />
              </button>

              <!-- Dropdown Card -->
              <div
                v-if="showProfileMenu"
                class="absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-2xl p-3 z-50 space-y-2 animate-in fade-in slide-in-from-top-2 duration-150"
              >
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
                    :href="'/' + rolePrefix + '/profile'"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    @click="showProfileMenu = false"
                  >
                    <i class="ri-shield-keyhole-line text-sm text-blue-600" />
                    <span>Account security</span>
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
                    class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 font-semibold text-xs transition-colors"
                    @click="logout"
                  >
                    <span>Logout</span>
                    <i class="ri-logout-box-r-line" />
                  </button>
                </div>
              </div>
            </div>
          </template>

          <!-- When Guest: Show Sign In and Register -->
          <template v-else>
            <Link
              href="/customer/login"
              class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
            >
              Sign In
            </Link>

            <Link
              href="/customer/register"
              class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all inline-block"
            >
              Register
            </Link>

            <Link
              href="/owner/login"
              class="hidden sm:inline-flex px-3 py-2 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold hover:bg-emerald-100 transition-colors"
            >
              Fleet Owner Portal
            </Link>
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
              <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                CR
              </div>
              <span class="font-extrabold text-lg text-white">AutoRent Fleet</span>
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
          &copy; 2026 AutoRent Car Rental & Fleet Management System. All rights reserved.
        </div>
      </div>
    </footer>
  </div>
</template>
