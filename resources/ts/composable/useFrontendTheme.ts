import { ref } from 'vue'

export type FrontendTheme = 'light' | 'dark'

const activeTheme = ref<FrontendTheme>('light')
let isInitialized = false

const applyThemeToDOM = (theme: FrontendTheme) => {
  if (typeof document === 'undefined') return

  const root = document.documentElement
  const body = document.body

  if (theme === 'dark') {
    root.classList.add('dark')
    root.classList.remove('light')
    body.classList.add('dark')
    body.classList.remove('light')
  } else {
    root.classList.remove('dark')
    root.classList.add('light')
    body.classList.remove('dark')
    body.classList.add('light')
  }

  if (typeof window !== 'undefined') {
    try {
      localStorage.setItem('frontend_theme', theme)
    } catch {
      // Ignored for restricted storage
    }
  }
}

export function useFrontendTheme() {
  const initTheme = () => {
    if (typeof window === 'undefined') return
    if (isInitialized) {
      applyThemeToDOM(activeTheme.value)

      return
    }

    isInitialized = true

    let savedTheme: FrontendTheme | null = null
    try {
      savedTheme = localStorage.getItem('frontend_theme') as FrontendTheme | null
    } catch {
      savedTheme = null
    }

    // Default to 'light' theme unless explicitly set to 'dark'
    const initialTheme: FrontendTheme = savedTheme === 'dark' ? 'dark' : 'light'

    activeTheme.value = initialTheme
    applyThemeToDOM(initialTheme)
  }

  const toggleTheme = () => {
    const nextTheme: FrontendTheme = activeTheme.value === 'dark' ? 'light' : 'dark'

    activeTheme.value = nextTheme
    applyThemeToDOM(nextTheme)
  }

  const setTheme = (theme: FrontendTheme) => {
    activeTheme.value = theme
    applyThemeToDOM(theme)
  }

  return {
    theme: activeTheme,
    initTheme,
    toggleTheme,
    setTheme,
    applyThemeToDOM,
  }
}
