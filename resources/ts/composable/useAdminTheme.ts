import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

export type AdminThemeStyle = 'light' | 'dark' | 'midnight' | 'system'

const activeTheme = ref<AdminThemeStyle>('dark')
let initialized = false

export function useAdminTheme() {
  const page = usePage()

  const serverTheme = computed<AdminThemeStyle>(() => {
    const auth = page.props.auth as any
    const user = auth?.admin || auth?.user
    
    return (user?.theme_style as AdminThemeStyle) || 'dark'
  })

  const applyThemeToDOM = (theme: AdminThemeStyle) => {
    if (typeof document === 'undefined') return

    const root = document.documentElement
    const body = document.body

    // Remove existing theme classes
    root.classList.remove('dark', 'light', 'theme-midnight', 'theme-light', 'theme-dark')
    body.classList.remove('dark', 'light', 'theme-midnight')

    let effectiveTheme = theme
    if (theme === 'system' && typeof window !== 'undefined') {
      effectiveTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }

    if (effectiveTheme === 'dark') {
      root.classList.add('dark')
      body.classList.add('dark')
    } else if (effectiveTheme === 'midnight') {
      root.classList.add('dark', 'theme-midnight')
      body.classList.add('dark', 'theme-midnight')
    } else {
      root.classList.add('light')
      body.classList.add('light')
    }

    if (typeof window !== 'undefined') {
      localStorage.setItem('admin_theme_style', theme)
    }
  }

  const setTheme = async (theme: AdminThemeStyle) => {
    activeTheme.value = theme
    applyThemeToDOM(theme)

    // Sync auth prop in memory
    const auth = page.props.auth as any
    if (auth?.admin) auth.admin.theme_style = theme
    if (auth?.user) auth.user.theme_style = theme

    // Persist to server database
    try {
      await axios.post('/admin/theme-style', { theme_style: theme })
    } catch {
      // Ignored for non-blocking offline/instant feel
    }
  }

  const initTheme = () => {
    if (initialized || typeof window === 'undefined') return
    initialized = true

    const saved = localStorage.getItem('admin_theme_style') as AdminThemeStyle | null
    const initial = serverTheme.value || saved || 'dark'

    activeTheme.value = initial
    applyThemeToDOM(initial)
  }

  watch(
    serverTheme,
    (newTheme: AdminThemeStyle) => {
      if (newTheme && newTheme !== activeTheme.value) {
        activeTheme.value = newTheme
        applyThemeToDOM(newTheme)
      }
    },
    { immediate: true },
  )

  return {
    theme: activeTheme,
    serverTheme,
    setTheme,
    initTheme,
    applyThemeToDOM,
  }
}
