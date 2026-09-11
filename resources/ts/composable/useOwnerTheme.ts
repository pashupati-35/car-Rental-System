import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

export type OwnerThemeStyle = 'light' | 'dark' | 'midnight' | 'system'

const activeOwnerTheme = ref<OwnerThemeStyle>('dark')
let ownerThemeInitialized = false

export function useOwnerTheme() {
  const page = usePage()

  const serverTheme = computed<OwnerThemeStyle>(() => {
    const auth = page.props.auth as any
    const user = auth?.owner || auth?.user

    return (user?.theme_style as OwnerThemeStyle) || 'dark'
  })

  const applyThemeToDOM = (theme: OwnerThemeStyle) => {
    if (typeof document === 'undefined') return

    const root = document.documentElement
    const body = document.body

    // Remove existing theme classes
    root.classList.remove('dark', 'light', 'theme-midnight', 'theme-light', 'theme-dark', 'theme-owner-emerald')
    body.classList.remove('dark', 'light', 'theme-midnight', 'theme-owner-emerald')

    let effectiveTheme = theme
    if (theme === 'system' && typeof window !== 'undefined') {
      effectiveTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }

    if (effectiveTheme === 'dark') {
      root.classList.add('dark', 'theme-owner-emerald')
      body.classList.add('dark', 'theme-owner-emerald')
    } else if (effectiveTheme === 'midnight') {
      root.classList.add('dark', 'theme-midnight', 'theme-owner-emerald')
      body.classList.add('dark', 'theme-midnight', 'theme-owner-emerald')
    } else {
      root.classList.add('light', 'theme-owner-emerald')
      body.classList.add('light', 'theme-owner-emerald')
    }

    if (typeof window !== 'undefined') {
      localStorage.setItem('owner_theme_style', theme)
    }
  }

  const setTheme = async (theme: OwnerThemeStyle) => {
    activeOwnerTheme.value = theme
    applyThemeToDOM(theme)

    // Sync auth prop in memory
    const auth = page.props.auth as any
    if (auth?.owner) auth.owner.theme_style = theme
    if (auth?.user) auth.user.theme_style = theme

    // Persist to server database
    try {
      await axios.post('/owner/theme-style', { theme_style: theme })
    } catch {
      // Ignored for non-blocking UI feel
    }
  }

  const initTheme = () => {
    if (ownerThemeInitialized || typeof window === 'undefined') return
    ownerThemeInitialized = true

    const saved = localStorage.getItem('owner_theme_style') as OwnerThemeStyle | null
    const initial = serverTheme.value || saved || 'dark'

    activeOwnerTheme.value = initial
    applyThemeToDOM(initial)
  }

  watch(
    serverTheme,
    (newTheme: OwnerThemeStyle) => {
      if (newTheme && newTheme !== activeOwnerTheme.value) {
        activeOwnerTheme.value = newTheme
        applyThemeToDOM(newTheme)
      }
    },
    { immediate: true },
  )

  return {
    theme: activeOwnerTheme,
    serverTheme,
    setTheme,
    initTheme,
    applyThemeToDOM,
  }
}
