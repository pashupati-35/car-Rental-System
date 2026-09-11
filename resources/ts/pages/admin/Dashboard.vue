<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import DashboardAnalyticsCharts from './components/DashboardAnalyticsCharts.vue'

interface BookingTrendPoint {
  id: number
  booking_id: number
  date: string
  day_name?: string
  amount: number
  status: string
  customer_name?: string
  car_name?: string
  car_number?: string
}

const props = defineProps<{
  stats?: {
    totalCars: number
    pendingCarsCount: number
    verifiedCarsCount: number
    rejectedCarsCount: number
    totalOwners: number
    totalCustomers: number
    totalDrivers: number
    totalBookings: number
    confirmedBookings: number
    pendingBookings: number
    totalRevenue: number
  }
  pendingCars?: Array<any>
  recentCars?: Array<any>
  recentBookings?: any
  bookingTrends?: BookingTrendPoint[]
  recentOwners?: Array<any>
  recentCustomers?: Array<any>
  cmsStats?: Record<string, number>
}>()

const statsData = ref({
  totalCars: props.stats?.totalCars ?? 0,
  pendingCarsCount: props.stats?.pendingCarsCount ?? 0,
  verifiedCarsCount: props.stats?.verifiedCarsCount ?? 0,
  rejectedCarsCount: props.stats?.rejectedCarsCount ?? 0,
  totalOwners: props.stats?.totalOwners ?? 0,
  totalCustomers: props.stats?.totalCustomers ?? 0,
  totalDrivers: props.stats?.totalDrivers ?? 0,
  totalBookings: props.stats?.totalBookings ?? 0,
  confirmedBookings: props.stats?.confirmedBookings ?? 0,
  pendingBookings: props.stats?.pendingBookings ?? 0,
  totalRevenue: props.stats?.totalRevenue ?? 0,
})

const pendingCarsList = ref<any[]>(props.pendingCars || [])
const refreshing = ref(false)

const cmsModules = [
  { id: 'faqs', label: 'FAQs', icon: 'ri-question-line', color: 'indigo' },
  { id: 'blogs', label: 'Blogs & Articles', icon: 'ri-article-line', color: 'blue' },
  { id: 'services', label: 'Services', icon: 'ri-customer-service-2-line', color: 'emerald' },
  { id: 'teams', label: 'Team Members', icon: 'ri-team-line', color: 'purple' },
  { id: 'testimonials', label: 'Testimonials', icon: 'ri-chat-smile-2-line', color: 'amber' },
  { id: 'notices', label: 'Notices', icon: 'ri-notification-3-line', color: 'rose' },
  { id: 'sliders', label: 'Sliders & Banners', icon: 'ri-slideshow-3-line', color: 'cyan' },
  { id: 'popups', label: 'Popups & Alerts', icon: 'ri-window-line', color: 'pink' },
  { id: 'pages', label: 'Custom Pages', icon: 'ri-file-list-3-line', color: 'violet' },
  { id: 'partners', label: 'Partners', icon: 'ri-hand-heart-line', color: 'teal' },
  { id: 'careers', label: 'Careers', icon: 'ri-briefcase-line', color: 'orange' },
  { id: 'enquiries', label: 'Enquiries & Leads', icon: 'ri-mail-unread-line', color: 'blue' },
  { id: 'contacts', label: 'Contact Messages', icon: 'ri-contacts-book-line', color: 'emerald' },
  { id: 'menus', label: 'Navigation Menus', icon: 'ri-menu-line', color: 'slate' },
  { id: 'albums', label: 'Photo Albums', icon: 'ri-gallery-line', color: 'indigo' },
  { id: 'site-settings', label: 'Site Settings & SEO', icon: 'ri-settings-4-line', color: 'slate' },
]

const refreshDashboard = () => {
  refreshing.value = true
  router.reload({
    only: ['stats', 'pendingCars', 'bookingTrends', 'cmsStats'],
    onFinish: () => {
      refreshing.value = false
      if (props.stats) statsData.value = { ...props.stats }
      if (props.pendingCars) pendingCarsList.value = props.pendingCars
    },
  })
}

const verifyCar = (carId: number) => {
  router.patch(`/admin/cars/${carId}/verify`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      pendingCarsList.value = pendingCarsList.value.filter((c: any) => c.id !== carId)
      statsData.value.pendingCarsCount = Math.max(0, statsData.value.pendingCarsCount - 1)
      statsData.value.verifiedCarsCount += 1
    },
  })
}

const rejectCar = (carId: number) => {
  router.patch(`/admin/cars/${carId}/reject`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      pendingCarsList.value = pendingCarsList.value.filter((c: any) => c.id !== carId)
      statsData.value.pendingCarsCount = Math.max(0, statsData.value.pendingCarsCount - 1)
      statsData.value.rejectedCarsCount += 1
    },
  })
}
</script>

<template>
  <AdminLayout>
    <Head title="Master Control Dashboard - Super Admin" />

    <div class="space-y-8">
      <!-- Admin Hero Operations Hub Banner -->
      <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden border border-indigo-700/50">
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none" />
        <div class="space-y-2 relative z-10">
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-white/20 text-indigo-100 text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-md">
              Super Administrator Command Hub
            </span>
            <span class="flex items-center gap-1.5 text-[11px] text-emerald-400 font-medium">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              Live Sync
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
            System Operations & Fleet Intelligence
          </h1>
          <p class="text-xs text-indigo-200/90 max-w-xl leading-relaxed">
            Full administrative authority over vehicle inspections, fleet owner credentials, customer directory, driver rosters, zero-conflict booking schedules, and global CMS control.
          </p>
        </div>

        <div class="flex flex-wrap gap-2.5 relative z-10 shrink-0">
          <button
            type="button"
            class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs backdrop-blur-md transition-all flex items-center gap-2 cursor-pointer"
            :disabled="refreshing"
            @click="refreshDashboard"
          >
            <i
              class="ri-refresh-line"
              :class="refreshing ? 'animate-spin' : ''"
            />
            <span>{{ refreshing ? 'Syncing...' : 'Refresh Metrics' }}</span>
          </button>

          <Link
            href="/admin/cms"
            class="px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-1.5"
          >
            <i class="ri-layout-masonry-line" />
            <span>Master CMS Suite</span>
          </Link>
        </div>
      </div>

      <!-- 6 Key Metric KPI Cards Grid - Fully Clickable Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 sm:gap-5">
        <!-- Card 1: Total Fleet Cars -->
        <Link
          href="/admin/cars"
          class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-md transition-all flex flex-col justify-between group cursor-pointer"
        >
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-indigo-600 transition-colors">
                Fleet Vehicles
              </p>
              <h3 class="text-3xl font-black text-slate-900 dark:text-white">
                {{ statsData.totalCars }}
              </h3>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shadow-xs group-hover:scale-110 transition-transform">
              🚗
            </div>
          </div>
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 mt-3 space-y-1">
            <div class="flex items-center gap-1.5 text-[11px]">
              <span class="text-emerald-600 font-bold">{{ statsData.verifiedCarsCount }} Verified</span>
              <span class="text-slate-300 dark:text-slate-700">&bull;</span>
              <span class="text-amber-500 font-bold">{{ statsData.pendingCarsCount }} Pend</span>
            </div>
            <span class="text-xs text-indigo-600 dark:text-indigo-400 font-bold group-hover:underline inline-flex items-center gap-1">
              Manage &rarr;
            </span>
          </div>
        </Link>

        <!-- Card 2: Fleet Owners -->
        <Link
          href="/admin/owners"
          class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-md transition-all flex flex-col justify-between group cursor-pointer"
        >
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-emerald-600 transition-colors">
                Fleet Owners
              </p>
              <h3 class="text-3xl font-black text-slate-900 dark:text-white">
                {{ statsData.totalOwners }}
              </h3>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shadow-xs group-hover:scale-110 transition-transform">
              🏢
            </div>
          </div>
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 mt-3 space-y-1">
            <p class="text-[11px] text-slate-500 truncate">
              Partner accounts
            </p>
            <span class="text-xs text-indigo-600 dark:text-indigo-400 font-bold group-hover:underline inline-flex items-center gap-1">
              Directory &rarr;
            </span>
          </div>
        </Link>

        <!-- Card 3: System Drivers -->
        <Link
          href="/admin/drivers"
          class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all flex flex-col justify-between group cursor-pointer"
        >
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-blue-600 transition-colors">
                System Drivers
              </p>
              <h3 class="text-3xl font-black text-slate-900 dark:text-white">
                {{ statsData.totalDrivers }}
              </h3>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shadow-xs group-hover:scale-110 transition-transform">
              👨‍✈️
            </div>
          </div>
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 mt-3 space-y-1">
            <p class="text-[11px] text-slate-500 truncate">
              Roster drivers
            </p>
            <span class="text-xs text-indigo-600 dark:text-indigo-400 font-bold group-hover:underline inline-flex items-center gap-1">
              Directory &rarr;
            </span>
          </div>
        </Link>

        <!-- Card 4: Customers -->
        <Link
          href="/admin/customers"
          class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-purple-300 dark:hover:border-purple-700 hover:shadow-md transition-all flex flex-col justify-between group cursor-pointer"
        >
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-purple-600 transition-colors">
                Customers
              </p>
              <h3 class="text-3xl font-black text-slate-900 dark:text-white">
                {{ statsData.totalCustomers }}
              </h3>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shadow-xs group-hover:scale-110 transition-transform">
              👥
            </div>
          </div>
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 mt-3 space-y-1">
            <p class="text-[11px] text-slate-500 truncate">
              Registered clients
            </p>
            <span class="text-xs text-indigo-600 dark:text-indigo-400 font-bold group-hover:underline inline-flex items-center gap-1">
              Directory &rarr;
            </span>
          </div>
        </Link>

        <!-- Card 5: Total Bookings -->
        <Link
          href="/admin/booked-cars"
          class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-amber-300 dark:hover:border-amber-700 hover:shadow-md transition-all flex flex-col justify-between group cursor-pointer"
        >
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-amber-600 transition-colors">
                Bookings
              </p>
              <h3 class="text-3xl font-black text-slate-900 dark:text-white">
                {{ statsData.totalBookings }}
              </h3>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shadow-xs group-hover:scale-110 transition-transform">
              📅
            </div>
          </div>
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 mt-3 space-y-1">
            <div class="flex items-center gap-1.5 text-[11px]">
              <span class="text-emerald-600 font-bold">{{ statsData.confirmedBookings }} Conf</span>
              <span class="text-slate-300 dark:text-slate-700">&bull;</span>
              <span class="text-amber-500 font-bold">{{ statsData.pendingBookings }} Pend</span>
            </div>
            <span class="text-xs text-indigo-600 dark:text-indigo-400 font-bold group-hover:underline inline-flex items-center gap-1">
              Review Roster &rarr;
            </span>
          </div>
        </Link>

        <!-- Card 6: Total Volume / Revenue -->
        <Link
          href="/admin/booked-cars"
          class="p-5 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-md transition-all flex flex-col justify-between group cursor-pointer"
        >
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-emerald-600 transition-colors">
                Rental Revenue
              </p>
              <h3 class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">
                ${{ Number(statsData.totalRevenue).toLocaleString() }}
              </h3>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shadow-xs group-hover:scale-110 transition-transform">
              💵
            </div>
          </div>
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 mt-3 space-y-1">
            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-bold block group-hover:underline">
              View Financials &rarr;
            </span>
          </div>
        </Link>
      </div>

      <!-- Pending Fleet Verification Queue -->
      <div
        v-if="pendingCarsList.length > 0"
        class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-amber-200/80 dark:border-amber-900/40 shadow-xs space-y-4"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold">
              <i class="ri-alert-line text-lg" />
            </div>
            <div>
              <h3 class="font-extrabold text-base text-slate-900 dark:text-white">
                Vehicle Approvals Queue ({{ pendingCarsList.length }} Pending)
              </h3>
              <p class="text-xs text-slate-500">
                New vehicles submitted by fleet owners awaiting administrative verification.
              </p>
            </div>
          </div>

          <Link
            href="/admin/cars?status=pending"
            class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline"
          >
            View All Pending &rarr;
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="car in pendingCarsList"
            :key="car.id"
            class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between gap-3"
          >
            <div class="flex items-start gap-3">
              <img
                :src="car.image ? '/' + car.image : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=120&auto=format&fit=crop&q=80'"
                class="w-16 h-14 object-cover rounded-xl border border-slate-200 shrink-0"
              >
              <div class="min-w-0">
                <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">
                  {{ car.car_name || car.brand }} {{ car.car_model || car.model }}
                </h4>
                <p class="text-[11px] text-slate-500 font-mono">
                  {{ car.car_number || 'N/A' }}
                </p>
                <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold">
                  Owner: {{ car.owner?.full_name || 'Fleet Partner' }}
                </p>
              </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
              <span class="font-bold text-slate-900 dark:text-white">${{ car.car_price_per_day || car.price_per_day }}/day</span>
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer"
                  @click="verifyCar(car.id)"
                >
                  Approve
                </button>
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors cursor-pointer"
                  @click="rejectCar(car.id)"
                >
                  Reject
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Interactive Analytics, Dynamic Line Graph, Bar & Pie Charts Suite -->
      <DashboardAnalyticsCharts
        :stats="statsData"
        :booking-trends="props.bookingTrends || []"
      />

      <!-- Master CMS Suite Control Matrix (ONLY FOR ADMIN PORTAL) -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div>
            <div class="flex items-center gap-2">
              <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-extrabold uppercase">
                Admin Exclusive
              </span>
              <h3 class="font-extrabold text-lg text-slate-900 dark:text-white">
                Master CMS Control Matrix (16 Content Modules)
              </h3>
            </div>
            <p class="text-xs text-slate-500">
              Manage public website landing content, sliders, services, FAQs, blogs, and site-wide configurations.
            </p>
          </div>

          <Link
            href="/admin/cms"
            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors inline-flex items-center gap-1.5 self-start sm:self-auto"
          >
            <i class="ri-layout-masonry-line" />
            <span>Open Full CMS Manager</span>
          </Link>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-8 gap-3 sm:gap-3.5 pt-2">
          <Link
            v-for="mod in cmsModules"
            :key="mod.id"
            :href="'/admin/cms?module=' + mod.id"
            class="p-3 sm:p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-indigo-50/70 dark:hover:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 hover:border-indigo-200 transition-all flex items-center justify-between group cursor-pointer"
          >
            <div class="flex items-center gap-2.5 min-w-0">
              <div class="w-8 h-8 rounded-xl bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform shrink-0">
                <i :class="mod.icon" />
              </div>
              <div class="truncate">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block truncate group-hover:text-indigo-600 transition-colors">
                  {{ mod.label }}
                </span>
                <span class="text-[10px] text-slate-400">
                  Manage Content
                </span>
              </div>
            </div>
            <i class="ri-arrow-right-s-line text-slate-300 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all text-xs" />
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
