<script setup lang="ts">
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()
const drawer = ref(true)
const isDark = ref(false)

const auth = computed(() => page.props.auth as any)
const user = computed(() => auth.value?.user || auth.value?.admin || auth.value?.owner || auth.value?.customer)

const role = computed(() => {
  if (auth.value?.admin) return 'Admin'
  if (auth.value?.owner) return 'Owner'
  if (auth.value?.customer) return 'Customer'

  return 'User'
})

const flash = computed(() => page.props.flash as any)

const adminNav = [
  { title: 'Dashboard', icon: 'ri-dashboard-line', href: '/admin/dashboard' },
  { title: 'Cars Management', icon: 'ri-car-line', href: '/admin/cars' },
  { title: 'Bookings', icon: 'ri-calendar-check-line', href: '/admin/booked-cars' },
  { title: 'Customers', icon: 'ri-user-smile-line', href: '/admin/customers' },
  { title: 'Email Templates', icon: 'ri-mail-settings-line', href: '/admin/email-templates' },
  { title: 'AI Assistant', icon: 'ri-robot-line', href: '/ai-chat' },
]

const ownerNav = [
  { title: 'Dashboard', icon: 'ri-dashboard-line', href: '/owner/dashboard' },
  { title: 'My Cars', icon: 'ri-car-line', href: '/owner/cars' },
  { title: 'Add New Car', icon: 'ri-add-circle-line', href: '/owner/cars/create' },
  { title: 'Profile', icon: 'ri-user-settings-line', href: '/owner/profile' },
]

const customerNav = [
  { title: 'Dashboard', icon: 'ri-dashboard-line', href: '/customer/dashboard' },
  { title: 'Browse Cars', icon: 'ri-car-line', href: '/cars' },
  { title: 'My Bookings', icon: 'ri-book-read-line', href: '/customer/bookings' },
  { title: 'Profile', icon: 'ri-user-line', href: '/customer/profile' },
]

const navItems = computed(() => {
  if (role.value === 'Admin') return adminNav
  if (role.value === 'Owner') return ownerNav

  return customerNav
})

const logout = () => {
  const logoutRoute = role.value === 'Admin' 
    ? '/admin/logout' 
    : role.value === 'Owner' 
      ? '/owner/logout' 
      : '/customer/logout'

  router.post(logoutRoute)
}
</script>

<template>
  <VApp :theme="isDark ? 'dark' : 'light'">
    <VNavigationDrawer
      v-model="drawer"
      elevation="2"
      class="border-r border-gray-200 dark:border-gray-800"
    >
      <div class="p-4 flex items-center gap-3 border-b border-gray-100 dark:border-gray-800">
        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-blue-500/30">
          CR
        </div>
        <div>
          <h2 class="font-bold text-gray-900 dark:text-white leading-tight">
            Car Rental
          </h2>
          <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-medium">{{ role }} Portal</span>
        </div>
      </div>

      <VList
        density="comfortable"
        class="p-2 space-y-1"
      >
        <VListItem
          v-for="item in navItems"
          :key="item.title"
          :prepend-icon="item.icon"
          :title="item.title"
          :href="item.href"
          rounded="lg"
          class="transition-colors text-gray-700 dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-gray-800"
          @click.prevent="router.visit(item.href)"
        />
      </VList>

      <template #append>
        <div class="p-4 border-t border-gray-100 dark:border-gray-800">
          <button
            class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/60 font-medium text-sm transition-all"
            @click="logout"
          >
            <i class="ri-logout-box-r-line" />
            Sign Out
          </button>
        </div>
      </template>
    </VNavigationDrawer>

    <VAppBar
      flat
      border="b"
      class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md px-4"
    >
      <VAppBarNavIcon @click="drawer = !drawer" />
      
      <VAppBarTitle class="text-base font-semibold text-gray-800 dark:text-white">
        <slot name="header">
          Car Rental Dashboard
        </slot>
      </VAppBarTitle>

      <VSpacer />

      <div class="flex items-center gap-3">
        <button
          class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors"
          @click="isDark = !isDark"
        >
          <i :class="isDark ? 'ri-sun-line text-yellow-500' : 'ri-moon-line'" />
        </button>

        <div class="flex items-center gap-2 ps-2 border-l border-gray-200 dark:border-gray-700">
          <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
          </div>
          <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hidden sm:inline">{{ user?.name || 'User' }}</span>
        </div>
      </div>
    </VAppBar>

    <VMain class="bg-gray-50/50 dark:bg-gray-950">
      <!-- Flash Alert messages -->
      <div
        v-if="flash?.success"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4"
      >
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-sm">
          <i class="ri-checkbox-circle-fill text-xl text-emerald-600" />
          <span>{{ flash.success }}</span>
        </div>
      </div>
      <div
        v-if="flash?.error"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4"
      >
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 shadow-sm">
          <i class="ri-error-warning-fill text-xl text-rose-600" />
          <span>{{ flash.error }}</span>
        </div>
      </div>

      <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
        <slot />
      </div>
    </VMain>
  </VApp>
</template>
