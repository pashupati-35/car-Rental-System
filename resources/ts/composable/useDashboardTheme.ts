import { useTheme } from 'vuetify'

export type ThemeMode = 'light' | 'dark'

const DEFAULT_THEME: ThemeMode = 'light'

export const isThemeMode = (value: unknown): value is ThemeMode =>
  value === 'light' || value === 'dark'

export const normalizeThemeStyle = (value: unknown): ThemeMode =>
  isThemeMode(value) ? value : DEFAULT_THEME

type AuthUser = {
  theme_style?: string | null
}

export function useDashboardTheme() {
  const vuetifyTheme = useTheme()
  const page = usePage()

  const themeCookie = cookieRef<ThemeMode>('theme-style', DEFAULT_THEME)

  const studentThemeStyle = computed((): ThemeMode => {
    const authUser = (page.props.auth as { user?: AuthUser } | undefined)?.user
    const pageUser = (page.props as { user?: AuthUser }).user

    return normalizeThemeStyle(authUser?.theme_style ?? pageUser?.theme_style)
  })

  const applyTheme = (mode: ThemeMode) => {
    vuetifyTheme.change(mode)
    themeCookie.value = mode

    if (typeof document === 'undefined') return

    const isDark = mode === 'dark'

    document.documentElement.classList.toggle('dark', isDark)
    document.body.classList.toggle('student-dark', isDark)
  }

  const darkMode = computed(() => vuetifyTheme.global.current.value.dark)

  const syncAuthThemeStyle = (mode: ThemeMode) => {
    const auth = page.props.auth as { user?: AuthUser } | undefined

    if (auth?.user) auth.user.theme_style = mode
  }

  const persistTheme = async (mode: ThemeMode) => {
    const { apiClient } = await import('@/services/BaseAPIService')

    const response = await apiClient.post<{ status: string; theme_style?: ThemeMode }>(
      '/student/theme-style',
      { theme_style: mode },
    )

    syncAuthThemeStyle(response?.theme_style ?? mode)
  }

  const toggleDarkMode = async () => {
    const next: ThemeMode = darkMode.value ? 'light' : 'dark'

    applyTheme(next)

    try {
      await persistTheme(next)
    } catch {
      // Revert to last saved student preference on failure.
      applyTheme(studentThemeStyle.value)
    }
  }

  watch(
    studentThemeStyle,
    mode => {
      applyTheme(mode)
    },
    { immediate: true },
  )

  return {
    darkMode,
    themeStyle: studentThemeStyle,
    toggleDarkMode,
  }
}
