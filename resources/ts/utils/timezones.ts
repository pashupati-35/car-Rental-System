import { ref, onMounted } from 'vue'
import axios from 'axios'

let cachedTimezones: string[] = []

/**
 * Fetch all standard timezones directly from the backend showTimeZone() endpoint.
 */
export const fetchTimezones = async (): Promise<string[]> => {
  if (cachedTimezones.length > 0) {
    return cachedTimezones
  }

  try {
    const res = await axios.get('/timezones')
    if (res.data?.timezones && Array.isArray(res.data.timezones)) {
      cachedTimezones = res.data.timezones
      return cachedTimezones
    }
  } catch (err) {
    console.warn('Failed to fetch timezones from endpoint, using default.', err)
  }

  return ['Asia/Kathmandu', 'UTC']
}

/**
 * Composable to provide the complete list of timezones from showTimeZone() / DateTimeZone::listIdentifiers().
 */
export function useTimezones(initialTimezones?: string[]) {
  const timezones = ref<string[]>(
    initialTimezones && initialTimezones.length > 0
      ? initialTimezones
      : (cachedTimezones.length > 0 ? cachedTimezones : [])
  )
  const loading = ref(false)

  const loadTimezones = async () => {
    if (timezones.value.length > 0) {
      if (cachedTimezones.length === 0) {
        cachedTimezones = timezones.value
      }
      return
    }

    loading.value = true
    try {
      const list = await fetchTimezones()
      timezones.value = list
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    loadTimezones()
  })

  return {
    timezones,
    loading,
    loadTimezones,
  }
}
