import { ref, watch } from 'vue'
import { useAuthEmployee } from '@/composable/useAuthEmployee'

const isDark = ref(false)
let initialized = false

function applyThemeClass(dark: boolean) {
  if (typeof document === 'undefined') return
  document.documentElement.classList.toggle('dark', dark)
}

export function useTheme() {
  function init() {
    if (initialized || typeof window === 'undefined') return
    initialized = true

    // Prefer server-stored employee theme if available
    try {
      const { employee } = useAuthEmployee()
      const serverTheme = employee.value?.theme_style

      if (serverTheme === 'dark' || serverTheme === 'light') {
        isDark.value = serverTheme === 'dark'
      } else {
        const stored = localStorage.getItem('theme')

        isDark.value = stored
          ? stored === 'dark'
          : window.matchMedia('(prefers-color-scheme: dark)').matches
      }
    } catch {
      const stored = localStorage.getItem('theme')

      isDark.value = stored
        ? stored === 'dark'
        : window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    applyThemeClass(isDark.value)
  }

  function toggle() {
    isDark.value = !isDark.value
  }

  function setDark(value: boolean) {
    isDark.value = value
  }

  watch(isDark, (val: boolean) => {
    applyThemeClass(val)
    if (typeof window !== 'undefined') {
      localStorage.setItem('theme', val ? 'dark' : 'light')
    }
  })

  return { isDark, toggle, setDark, init }
}
