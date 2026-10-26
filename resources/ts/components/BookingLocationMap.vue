<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

interface LocationPoint {
  lat: number
  lng: number
  address: string
}

const props = withDefaults(
  defineProps<{
    isOpen?: boolean
    activeTarget?: 'pickup' | 'drop'
    carPricePerKm: number
    carPricePerDay: number
    initialPickup?: string
    initialDrop?: string
  }>(),
  {
    isOpen: false,
    activeTarget: 'pickup',
    carPricePerKm: 0,
    carPricePerDay: 0,
    initialPickup: 'Kathmandu Central Hub',
    initialDrop: 'Tribhuvan International Airport (TIA)',
  },
)

const emit = defineEmits<{
  (e: 'update:isOpen', value: boolean): void
  (e: 'update:pickup', location: LocationPoint): void
  (e: 'update:drop', location: LocationPoint): void
  (e: 'update:distance', distanceKm: number): void
  (e: 'update:pricing', data: { distanceKm: number; distanceCost: number; totalEstimated: number }): void
  (e: 'apply', data: { pickup: LocationPoint; drop: LocationPoint; distanceKm: number }): void
}>()

// Default center: Kathmandu, Nepal
const DEFAULT_CENTER: [number, number] = [27.7172, 85.3240]

const mapContainer = ref<HTMLElement | null>(null)
let mapInstance: L.Map | null = null
let pickupMarker: L.Marker | null = null
let dropMarker: L.Marker | null = null
let routePolyline: L.Polyline | null = null

// Current locations
const pickupPoint = ref<LocationPoint>({
  lat: 27.7126,
  lng: 85.3131,
  address: props.initialPickup || 'Kathmandu City Center',
})

const dropPoint = ref<LocationPoint>({
  lat: 27.6966,
  lng: 85.3591,
  address: props.initialDrop || 'Tribhuvan International Airport (TIA)',
})

// Active mode: 'pickup' or 'drop'
const activeMarkerSelection = ref<'pickup' | 'drop'>(props.activeTarget || 'pickup')

// Distance and routing state
const distanceKm = ref(0)
const estimatedDurationMinutes = ref(0)
const isCalculatingRoute = ref(false)

// Search state
const searchKeyword = ref('')
const searchResults = ref<Array<{ display_name: string; lat: string; lon: string; type?: string }>>([])
const isSearching = ref(false)
const notificationMessage = ref('')
let searchDebounceTimer: ReturnType<typeof setTimeout> | null = null
let notificationTimer: ReturnType<typeof setTimeout> | null = null

// Quick location presets
const quickPresets = [
  { name: 'TIA Airport', lat: 27.6966, lng: 85.3591, address: 'Tribhuvan International Airport (TIA), Kathmandu' },
  { name: 'Thamel Hub', lat: 27.7154, lng: 85.3123, address: 'Thamel Tourism Hub, Kathmandu' },
  { name: 'Patan Durbar', lat: 27.6744, lng: 85.3245, address: 'Patan Durbar Square, Lalitpur' },
  { name: 'Bhaktapur', lat: 27.6710, lng: 85.4298, address: 'Bhaktapur Durbar Square, Bhaktapur' },
  { name: 'Pokhara Lakeside', lat: 28.2096, lng: 83.9585, address: 'Lakeside Baidam, Pokhara' },
]

// Custom modern Leaflet Markers with HTML/SVG
const createCustomIcon = (type: 'pickup' | 'drop') => {
  const isPickup = type === 'pickup'
  const bgColor = isPickup ? '#10b981' : '#6366f1'
  const ringColor = isPickup ? 'rgba(16, 185, 129, 0.35)' : 'rgba(99, 102, 241, 0.35)'
  const iconClass = isPickup ? 'ri-map-pin-2-fill' : 'ri-flag-2-fill'
  const label = isPickup ? 'Pickup' : 'Drop-off'

  return L.divIcon({
    className: 'custom-map-pin-wrapper',
    html: `
      <div style="position: relative; display: flex; flex-direction: column; align-items: center; transform: translate(-50%, -100%); cursor: grab;">
        <div style="
          display: flex;
          align-items: center;
          gap: 4px;
          background: ${bgColor};
          color: #ffffff;
          padding: 4px 10px;
          border-radius: 9999px;
          font-size: 11px;
          font-weight: 700;
          letter-spacing: 0.02em;
          box-shadow: 0 4px 14px ${ringColor};
          white-space: nowrap;
          border: 2px solid #ffffff;
        ">
          <i class="${iconClass}" style="font-size: 13px;"></i>
          <span>${label}</span>
        </div>
        <div style="
          width: 0;
          height: 0;
          border-left: 6px solid transparent;
          border-right: 6px solid transparent;
          border-top: 8px solid ${bgColor};
          margin-top: -1px;
        "></div>
        <div style="
          width: 8px;
          height: 8px;
          background: ${bgColor};
          border-radius: 50%;
          box-shadow: 0 0 0 4px ${ringColor};
          margin-top: 2px;
        "></div>
      </div>
    `,
    iconSize: [0, 0],
    iconAnchor: [0, 0],
  })
}

// Haversine fallback distance formula
const calculateHaversineDistance = (lat1: number, lon1: number, lat2: number, lon2: number): number => {
  const R = 6371
  const dLat = ((lat2 - lat1) * Math.PI) / 180
  const dLon = ((lon2 - lon1) * Math.PI) / 180

  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) * Math.cos((lat2 * Math.PI) / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2)

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

  // Multiply by 1.28 road curvature factor for realistic road distance fallback
  return Math.round(R * c * 1.28 * 10) / 10
}

// Reverse Geocoding with OpenStreetMap Nominatim
const reverseGeocode = async (lat: number, lng: number): Promise<string> => {
  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`

    const res = await fetch(url, {
      headers: {
        'Accept-Language': 'en',
      },
    })

    if (!res.ok) throw new Error('Geocoding service unavailable')
    const data = await res.json()

    if (data && data.display_name) {
      const parts = data.display_name.split(',')
      const concise = parts.slice(0, 3).map((p: string) => p.trim()).join(', ')

      return concise || data.display_name
    }
  } catch (err) {
    console.warn('Reverse geocoding error:', err)
  }

  return `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`
}

// Show flash notification
const showNotice = (text: string) => {
  notificationMessage.value = text
  if (notificationTimer) clearTimeout(notificationTimer)
  notificationTimer = setTimeout(() => {
    notificationMessage.value = ''
  }, 3500)
}

// Notify parent component of distance & pricing updates
const notifyPriceUpdate = () => {
  const km = distanceKm.value || 1
  const perKmRate = Number(props.carPricePerKm) || 0
  const distanceCost = Math.round(km * perKmRate * 100) / 100
  const totalEstimated = distanceCost

  emit('update:pickup', pickupPoint.value)
  emit('update:drop', dropPoint.value)
  emit('update:distance', km)
  emit('update:pricing', {
    distanceKm: km,
    distanceCost,
    totalEstimated,
  })
}

// Fetch driving route using OSRM API
const calculateRoute = async () => {
  if (!mapInstance || !pickupPoint.value || !dropPoint.value) return

  isCalculatingRoute.value = true

  const p = pickupPoint.value
  const d = dropPoint.value

  try {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${p.lng},${p.lat};${d.lng},${d.lat}?overview=full&geometries=geojson`
    const response = await fetch(osrmUrl)
    const json = await response.json()

    if (json.code === 'Ok' && json.routes && json.routes.length > 0) {
      const route = json.routes[0]
      const meters = route.distance
      const seconds = route.duration

      distanceKm.value = Math.max(1, Math.round((meters / 1000) * 10) / 10)
      estimatedDurationMinutes.value = Math.max(5, Math.round(seconds / 60))

      const coordinates = route.geometry.coordinates.map((coord: [number, number]) => [coord[1], coord[0]])

      if (routePolyline) {
        mapInstance.removeLayer(routePolyline)
      }

      routePolyline = L.polyline(coordinates, {
        color: '#2563eb',
        weight: 5,
        opacity: 0.85,
        smoothFactor: 1,
        lineCap: 'round',
        lineJoin: 'round',
      }).addTo(mapInstance)

      const bounds = L.latLngBounds([
        [p.lat, p.lng],
        [d.lat, d.lng],
        ...coordinates,
      ])

      mapInstance.fitBounds(bounds, { padding: [50, 50] })
    } else {
      throw new Error('OSRM routing failed')
    }
  } catch {
    // Fallback to straight line & Haversine
    const fallbackDist = calculateHaversineDistance(p.lat, p.lng, d.lat, d.lng)

    distanceKm.value = fallbackDist
    estimatedDurationMinutes.value = Math.round((fallbackDist / 35) * 60)

    if (routePolyline) {
      mapInstance.removeLayer(routePolyline)
    }

    routePolyline = L.polyline([[p.lat, p.lng], [d.lat, d.lng]], {
      color: '#2563eb',
      weight: 4,
      dashArray: '8, 8',
      opacity: 0.8,
    }).addTo(mapInstance)

    const bounds = L.latLngBounds([[p.lat, p.lng], [d.lat, d.lng]])

    mapInstance.fitBounds(bounds, { padding: [50, 50] })
  } finally {
    isCalculatingRoute.value = false
    notifyPriceUpdate()
  }
}

// Update or re-create markers on map
const updateMarkers = () => {
  if (!mapInstance) return

  // Pickup marker
  if (!pickupMarker) {
    pickupMarker = L.marker([pickupPoint.value.lat, pickupPoint.value.lng], {
      icon: createCustomIcon('pickup'),
      draggable: true,
      autoPan: true,
    }).addTo(mapInstance)

    pickupMarker.on('dragend', async () => {
      if (!pickupMarker) return
      const latlng = pickupMarker.getLatLng()

      pickupPoint.value.lat = latlng.lat
      pickupPoint.value.lng = latlng.lng
      pickupPoint.value.address = await reverseGeocode(latlng.lat, latlng.lng)
      showNotice(`Pickup moved: ${pickupPoint.value.address}`)
      calculateRoute()
    })
  } else {
    pickupMarker.setLatLng([pickupPoint.value.lat, pickupPoint.value.lng])
  }

  // Drop-off marker
  if (!dropMarker) {
    dropMarker = L.marker([dropPoint.value.lat, dropPoint.value.lng], {
      icon: createCustomIcon('drop'),
      draggable: true,
      autoPan: true,
    }).addTo(mapInstance)

    dropMarker.on('dragend', async () => {
      if (!dropMarker) return
      const latlng = dropMarker.getLatLng()

      dropPoint.value.lat = latlng.lat
      dropPoint.value.lng = latlng.lng
      dropPoint.value.address = await reverseGeocode(latlng.lat, latlng.lng)
      showNotice(`Drop-off moved: ${dropPoint.value.address}`)
      calculateRoute()
    })
  } else {
    dropMarker.setLatLng([dropPoint.value.lat, dropPoint.value.lng])
  }
}

// Handle map click to place active marker
const handleMapClick = async (e: L.LeafletMouseEvent) => {
  const { lat, lng } = e.latlng

  if (activeMarkerSelection.value === 'pickup') {
    pickupPoint.value.lat = lat
    pickupPoint.value.lng = lng
    updateMarkers()
    pickupPoint.value.address = await reverseGeocode(lat, lng)
    showNotice(`Pickup pinned: ${pickupPoint.value.address}`)

    // Auto switch to drop selection for smooth multi-point setup
    activeMarkerSelection.value = 'drop'
  } else {
    dropPoint.value.lat = lat
    dropPoint.value.lng = lng
    updateMarkers()
    dropPoint.value.address = await reverseGeocode(lat, lng)
    showNotice(`Drop-off pinned: ${dropPoint.value.address}`)
  }

  calculateRoute()
}

// Apply quick preset
const applyPreset = (preset: typeof quickPresets[0], target: 'pickup' | 'drop') => {
  if (target === 'pickup') {
    pickupPoint.value = {
      lat: preset.lat,
      lng: preset.lng,
      address: preset.address,
    }
    showNotice(`Pickup set to: ${preset.name}`)
    activeMarkerSelection.value = 'drop'
  } else {
    dropPoint.value = {
      lat: preset.lat,
      lng: preset.lng,
      address: preset.address,
    }
    showNotice(`Drop-off set to: ${preset.name}`)
  }

  updateMarkers()
  if (mapInstance) {
    mapInstance.flyTo([preset.lat, preset.lng], 14, { duration: 1.2 })
  }
  calculateRoute()
}

// Swap pickup and drop-off locations
const swapLocations = () => {
  const temp = { ...pickupPoint.value }

  pickupPoint.value = { ...dropPoint.value }
  dropPoint.value = temp
  updateMarkers()
  showNotice('Swapped Pickup & Drop-off locations')
  calculateRoute()
}

// Browser Geolocation
const locateMe = () => {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by your browser')

    return
  }

  navigator.geolocation.getCurrentPosition(
    async position => {
      const { latitude, longitude } = position.coords

      if (activeMarkerSelection.value === 'pickup') {
        pickupPoint.value.lat = latitude
        pickupPoint.value.lng = longitude
        updateMarkers()
        if (mapInstance) {
          mapInstance.flyTo([latitude, longitude], 15)
        }
        pickupPoint.value.address = await reverseGeocode(latitude, longitude)
        showNotice(`Pickup set to current GPS location: ${pickupPoint.value.address}`)
        activeMarkerSelection.value = 'drop'
      } else {
        dropPoint.value.lat = latitude
        dropPoint.value.lng = longitude
        updateMarkers()
        if (mapInstance) {
          mapInstance.flyTo([latitude, longitude], 15)
        }
        dropPoint.value.address = await reverseGeocode(latitude, longitude)
        showNotice(`Drop-off set to current GPS location: ${dropPoint.value.address}`)
      }
      calculateRoute()
    },
    error => {
      console.warn('Geolocation failed:', error.message)
      alert('Unable to retrieve your current location. Please click on the map.')
    },
  )
}

// Live Search with Nominatim
const onSearchInput = () => {
  if (searchDebounceTimer) clearTimeout(searchDebounceTimer)

  if (!searchKeyword.value || searchKeyword.value.trim().length < 2) {
    searchResults.value = []

    return
  }

  searchDebounceTimer = setTimeout(() => {
    searchAddress()
  }, 400)
}

// Search address using Nominatim
const searchAddress = async () => {
  if (!searchKeyword.value || searchKeyword.value.trim().length < 2) return

  isSearching.value = true
  try {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchKeyword.value)}&limit=6`

    const res = await fetch(url, {
      headers: {
        'Accept-Language': 'en',
      },
    })

    const data = await res.json()

    searchResults.value = data || []
  } catch (err) {
    console.warn('Search error:', err)
  } finally {
    isSearching.value = false
  }
}

const clearSearch = () => {
  searchKeyword.value = ''
  searchResults.value = []
}

const selectSearchResult = (item: { display_name: string; lat: string; lon: string }) => {
  const lat = parseFloat(item.lat)
  const lng = parseFloat(item.lon)
  const parts = item.display_name.split(',')
  const formattedAddress = parts.slice(0, 3).map(p => p.trim()).join(', ')

  if (activeMarkerSelection.value === 'pickup') {
    pickupPoint.value = {
      lat,
      lng,
      address: formattedAddress,
    }
    showNotice(`Pickup set to: ${formattedAddress}`)
    activeMarkerSelection.value = 'drop'
  } else {
    dropPoint.value = {
      lat,
      lng,
      address: formattedAddress,
    }
    showNotice(`Drop-off set to: ${formattedAddress}`)
  }

  searchResults.value = []
  searchKeyword.value = ''
  updateMarkers()
  if (mapInstance) {
    mapInstance.flyTo([lat, lng], 15, { duration: 1.2 })
  }
  calculateRoute()
}

// Initialize Leaflet Map
const initMap = () => {
  if (!mapContainer.value) return

  if (!mapInstance) {
    mapInstance = L.map(mapContainer.value, {
      center: DEFAULT_CENTER,
      zoom: 12,
      zoomControl: true,
    })

    // OpenStreetMap standard tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19,
    }).addTo(mapInstance)

    mapInstance.on('click', handleMapClick)

    updateMarkers()
    calculateRoute()
  }

  // Force Leaflet to recalculate container bounds
  setTimeout(() => {
    if (mapInstance) {
      mapInstance.invalidateSize()
      calculateRoute()
    }
  }, 250)
}

const closeModal = () => {
  emit('update:isOpen', false)
}

const confirmAndApply = () => {
  notifyPriceUpdate()
  emit('apply', {
    pickup: pickupPoint.value,
    drop: dropPoint.value,
    distanceKm: distanceKm.value,
  })
  closeModal()
}

// Watch for modal opening
watch(
  () => props.isOpen,
  (open: boolean) => {
    if (open) {
      if (props.activeTarget) {
        activeMarkerSelection.value = props.activeTarget
      }
      nextTick(() => {
        initMap()
      })
    }
  },
)

watch(
  () => props.activeTarget,
  (target: 'pickup' | 'drop' | undefined) => {
    if (target) {
      activeMarkerSelection.value = target
    }
  },
)

// Re-calculate route if rates change
watch(
  () => props.carPricePerKm,
  () => {
    notifyPriceUpdate()
  },
)

onMounted(() => {
  if (props.isOpen) {
    nextTick(() => {
      initMap()
    })
  }
})

onUnmounted(() => {
  if (searchDebounceTimer) clearTimeout(searchDebounceTimer)
  if (notificationTimer) clearTimeout(notificationTimer)
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
})
</script>

<template>
  <!-- Interactive Map Modal Popup -->
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-md p-3 sm:p-6 overflow-y-auto animate-in fade-in duration-200"
    role="dialog"
    aria-modal="true"
  >
    <div
      class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-2xl w-full max-w-5xl flex flex-col max-h-[92vh] overflow-hidden relative"
      @click.stop
    >
      <!-- Modal Header -->
      <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-white/80 dark:bg-gray-900/80 backdrop-blur-md z-20">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-lg shadow-sm">
            <i class="ri-road-map-fill" />
          </div>
          <div>
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
              <span>Interactive Route & Distance Map</span>
              <span
                v-if="distanceKm > 0"
                class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-mono text-xs font-bold"
              >
                {{ distanceKm }} KM
              </span>
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Search any location or click and drag pins directly on the map
            </p>
          </div>
        </div>

        <button
          type="button"
          aria-label="Close map"
          class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center justify-center text-lg cursor-pointer"
          @click="closeModal"
        >
          <i class="ri-close-line" />
        </button>
      </div>

      <!-- Live Notification Banner -->
      <div
        v-if="notificationMessage"
        class="bg-blue-600 text-white px-4 py-1.5 text-xs font-semibold flex items-center justify-center gap-2 animate-in slide-in-from-top duration-150"
      >
        <i class="ri-checkbox-circle-fill" />
        <span>{{ notificationMessage }}</span>
      </div>

      <!-- Map Controls & Search Bar Top Strip -->
      <div class="p-4 bg-gray-50/90 dark:bg-gray-850 border-b border-gray-100 dark:border-gray-800 space-y-3 z-20">
        <div class="flex flex-wrap items-center justify-between gap-2.5 text-xs">
          <!-- Mode Switcher Tabs -->
          <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xs">
            <span class="ps-2 pe-1 font-bold text-gray-400 text-[11px] uppercase tracking-wider">Mode:</span>
            <button
              type="button"
              class="px-3 py-1.5 rounded-xl font-bold transition-all flex items-center gap-1.5 cursor-pointer"
              :class="activeMarkerSelection === 'pickup'
                ? 'bg-emerald-600 text-white shadow-xs'
                : 'text-gray-600 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700'"
              @click="activeMarkerSelection = 'pickup'"
            >
              <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse" />
              <span>Set Pickup Pin</span>
            </button>

            <button
              type="button"
              class="px-3 py-1.5 rounded-xl font-bold transition-all flex items-center gap-1.5 cursor-pointer"
              :class="activeMarkerSelection === 'drop'
                ? 'bg-indigo-600 text-white shadow-xs'
                : 'text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700'"
              @click="activeMarkerSelection = 'drop'"
            >
              <span class="w-2 h-2 rounded-full bg-indigo-300 animate-pulse" />
              <span>Set Drop-off Pin</span>
            </button>
          </div>

          <!-- Auxiliary Tools -->
          <div class="flex items-center gap-2">
            <button
              type="button"
              title="Swap Pickup and Drop-off"
              class="px-3 py-1.5 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 transition-all cursor-pointer flex items-center gap-1.5 font-bold shadow-xs"
              @click="swapLocations"
            >
              <i class="ri-arrow-up-down-line text-sm text-blue-600" />
              <span>Swap Points</span>
            </button>

            <button
              type="button"
              title="Use My Current GPS Coordinates"
              class="px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900 transition-all cursor-pointer flex items-center gap-1.5 font-bold shadow-xs"
              @click="locateMe"
            >
              <i class="ri-crosshair-2-line text-sm" />
              <span>My GPS</span>
            </button>
          </div>
        </div>

        <!-- Prominent Live Search Location Bar -->
        <div class="relative">
          <div class="flex items-center gap-2">
            <div class="relative flex-1">
              <i class="ri-search-2-line absolute start-3.5 top-3 text-gray-400 text-base" />
              <input
                v-model="searchKeyword"
                type="text"
                placeholder="Search location, landmark, hotel, street (e.g. Airport, Thamel, Patan, Lakeside)..."
                class="w-full ps-10 pe-10 py-2.5 text-xs sm:text-sm rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-xs"
                @input="onSearchInput"
                @keyup.enter="searchAddress"
              >
              <button
                v-if="searchKeyword"
                type="button"
                aria-label="Clear search"
                class="absolute end-3 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1"
                @click="clearSearch"
              >
                <i class="ri-close-circle-fill text-base" />
              </button>
            </div>

            <button
              type="button"
              class="px-4 py-2.5 rounded-2xl bg-gray-900 hover:bg-gray-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs sm:text-sm font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-sm shrink-0"
              :disabled="isSearching"
              @click="searchAddress"
            >
              <i
                v-if="isSearching"
                class="ri-loader-4-line animate-spin text-sm"
              />
              <span v-else>Search</span>
            </button>
          </div>

          <!-- Live Search Results Dropdown -->
          <div
            v-if="searchResults.length > 0"
            class="absolute start-0 end-0 top-full mt-2 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl p-2 z-50 max-h-64 overflow-y-auto space-y-1"
          >
            <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 flex items-center justify-between">
              <span>Matching Locations (Click to set as {{ activeMarkerSelection === 'pickup' ? 'Pickup' : 'Drop-off' }})</span>
              <span class="text-blue-500">{{ searchResults.length }} results</span>
            </div>
            <button
              v-for="(item, idx) in searchResults"
              :key="idx"
              type="button"
              class="w-full text-start px-3 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-xs text-gray-800 dark:text-gray-200 flex items-start justify-between gap-3 transition-colors cursor-pointer group"
              @click="selectSearchResult(item)"
            >
              <div class="flex items-start gap-2 min-w-0">
                <i
                  class="mt-0.5 text-sm"
                  :class="activeMarkerSelection === 'pickup' ? 'ri-map-pin-2-fill text-emerald-500' : 'ri-flag-2-fill text-indigo-500'"
                />
                <span class="truncate font-medium text-gray-800 dark:text-gray-200">{{ item.display_name }}</span>
              </div>
              <span
                class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-md transition-colors"
                :class="activeMarkerSelection === 'pickup'
                  ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300'
                  : 'bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300'"
              >
                Set {{ activeMarkerSelection === 'pickup' ? 'Pickup' : 'Drop-off' }} &rarr;
              </span>
            </button>
          </div>
        </div>

        <!-- Quick Destination Presets -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 text-[11px] scrollbar-none">
          <span class="text-gray-400 font-bold shrink-0">Popular:</span>
          <button
            v-for="preset in quickPresets"
            :key="preset.name"
            type="button"
            class="px-2.5 py-1 rounded-xl bg-white hover:bg-blue-50 dark:bg-gray-800 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 shrink-0 transition-all cursor-pointer font-semibold shadow-2xs"
            @click="applyPreset(preset, activeMarkerSelection)"
          >
            + {{ preset.name }}
          </button>
        </div>
      </div>

      <!-- Leaflet Map Container -->
      <div class="relative flex-1 min-h-[380px] sm:min-h-[440px] bg-gray-100 dark:bg-gray-800">
        <div
          ref="mapContainer"
          class="w-full h-full min-h-[380px] sm:min-h-[440px] z-10"
        />

        <!-- Floating Interactive Guide Banner -->
        <div class="absolute bottom-4 start-4 z-20 pointer-events-none">
          <div class="px-3 py-1.5 rounded-2xl bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border border-gray-200/80 dark:border-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-200 shadow-lg flex items-center gap-2">
            <span
              class="w-2 h-2 rounded-full"
              :class="activeMarkerSelection === 'pickup' ? 'bg-emerald-500' : 'bg-indigo-500'"
            />
            <span>Click map to place <strong>{{ activeMarkerSelection === 'pickup' ? 'Pickup' : 'Drop-off' }}</strong> pin, or drag existing pins</span>
          </div>
        </div>

        <!-- Route Calculation Loading Overlay -->
        <div
          v-if="isCalculatingRoute"
          class="absolute inset-0 bg-black/20 backdrop-blur-xs z-30 flex items-center justify-center"
        >
          <div class="px-4 py-2.5 rounded-2xl bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white shadow-2xl flex items-center gap-2.5 border border-gray-200 dark:border-gray-700">
            <i class="ri-loader-4-line text-blue-600 animate-spin text-base" />
            <span>Calculating driving distance & road route...</span>
          </div>
        </div>
      </div>

      <!-- Modal Footer & Pricing Breakdown -->
      <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 z-20 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center text-xs">
          <!-- Pickup Address -->
          <div class="p-2.5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40 min-w-0">
            <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-300 font-bold text-[11px] mb-0.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500" />
              <span>Pickup Location</span>
            </div>
            <p class="text-gray-800 dark:text-gray-200 font-semibold truncate text-[11px]">
              {{ pickupPoint.address }}
            </p>
          </div>

          <!-- Drop-off Address -->
          <div class="p-2.5 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200/60 dark:border-indigo-900/40 min-w-0">
            <div class="flex items-center gap-1.5 text-indigo-700 dark:text-indigo-300 font-bold text-[11px] mb-0.5">
              <span class="w-2 h-2 rounded-full bg-indigo-500" />
              <span>Drop-off Location</span>
            </div>
            <p class="text-gray-800 dark:text-gray-200 font-semibold truncate text-[11px]">
              {{ dropPoint.address }}
            </p>
          </div>

          <!-- Live Trip Rate Calculation -->
          <div class="p-2.5 rounded-2xl bg-blue-50/80 dark:bg-gray-800 border border-blue-200/60 dark:border-gray-700 flex items-center justify-between">
            <div>
              <div class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                Road Route Distance
              </div>
              <div class="flex items-baseline gap-1.5">
                <span class="text-base font-black text-blue-600 dark:text-blue-400 font-mono">{{ distanceKm }} km</span>
                <span class="text-[11px] text-gray-500">({{ estimatedDurationMinutes }} mins est.)</span>
              </div>
            </div>

            <div class="text-end">
              <div class="text-[10px] text-gray-500">
                Rate: ${{ carPricePerKm }}/km
              </div>
              <div class="text-base font-black text-gray-900 dark:text-white">
                ${{ (distanceKm * Number(carPricePerKm)).toFixed(2) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-2.5 pt-1">
          <button
            type="button"
            class="px-4 py-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition-colors cursor-pointer"
            @click="closeModal"
          >
            Cancel
          </button>

          <button
            type="button"
            class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-500/25 transition-all flex items-center gap-2 cursor-pointer"
            @click="confirmAndApply"
          >
            <i class="ri-check-line text-base" />
            <span>Confirm & Apply Route (${{ (distanceKm * Number(carPricePerKm)).toFixed(2) }})</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/* Leaflet custom map pin styling */
.custom-map-pin-wrapper {
  background: transparent !important;
  border: none !important;
}
</style>
