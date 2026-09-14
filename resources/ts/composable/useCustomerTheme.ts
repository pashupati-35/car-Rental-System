import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

export type CustomerThemeStyle = 'light' | 'dark' | 'midnight' | 'system'

const activeCustomerTheme = ref<CustomerThemeStyle>('dark')
let customerThemeInitialized = false

export function useCustomerTheme() {
  const page = usePage()

  const serverTheme = computed<CustomerThemeStyle>(() => {
    const auth = page.props.auth as any
    const user = auth?.customer || auth?.user

    return (user?.theme_style as CustomerThemeStyle) || 'dark'
  })

  const applyThemeToDOM = (theme: CustomerThemeStyle) => {
    if (typeof document === 'undefined') return

    const root = document.documentElement
    const body = document.body

    // Remove existing theme classes
    root.classList.remove('dark', 'light', 'theme-midnight', 'theme-light', 'theme-dark', 'theme-customer-blue')
    body.classList.remove('dark', 'light', 'theme-midnight', 'theme-customer-blue')

    let effectiveTheme = theme
    if (theme === 'system' && typeof window !== 'undefined') {
      effectiveTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }

    if (effectiveTheme === 'dark') {
      root.classList.add('dark', 'theme-customer-blue')
      body.classList.add('dark', 'theme-customer-blue')
    } else if (effectiveTheme === 'midnight') {
      root.classList.add('dark', 'theme-midnight', 'theme-customer-blue')
      body.classList.add('dark', 'theme-midnight', 'theme-customer-blue')
    } else {
      root.classList.add('light', 'theme-customer-blue')
      body.classList.add('light', 'theme-customer-blue')
    }

    if (typeof window !== 'undefined') {
      localStorage.setItem('customer_theme_style', theme)
    }
  }

  const setTheme = async (theme: CustomerThemeStyle) => {
    activeCustomerTheme.value = theme
    applyThemeToDOM(theme)

    // Sync auth prop in memory
    const auth = page.props.auth as any
    if (auth?.customer) auth.customer.theme_style = theme
    if (auth?.user) auth.user.theme_style = theme

    // Persist to server database if logged in
    if (auth?.customer || auth?.user) {
      try {
        await axios.post('/customer/theme-style', { theme_style: theme })
      } catch {
        // Non-blocking for offline/instant feel
      }
    }
  }

  const initTheme = () => {
    if (customerThemeInitialized || typeof window === 'undefined') return
    customerThemeInitialized = true

    const saved = localStorage.getItem('customer_theme_style') as CustomerThemeStyle | null
    const initial = serverTheme.value || saved || 'dark'

    activeCustomerTheme.value = initial
    applyThemeToDOM(initial)
  }

  watch(
    serverTheme,
    (newTheme: CustomerThemeStyle) => {
      if (newTheme && newTheme !== activeCustomerTheme.value) {
        activeCustomerTheme.value = newTheme
        applyThemeToDOM(newTheme)
      }
    },
    { immediate: true },
  )

  return {
    theme: activeCustomerTheme,
    serverTheme,
    setTheme,
    initTheme,
    applyThemeToDOM,
  }
}
