
import { getCurrentInstance, inject } from 'vue'
import { useErrors } from './useHelpers'

type SnotifyLike = {
  success: (message: string) => void
  error: (message: string) => void
  info: (message: string) => void
}

let fallbackSnotify: SnotifyLike | undefined

const { showValidationError } = useErrors()

export const initSnotifyHelper = (instance: SnotifyLike) => {
  fallbackSnotify = instance
}

const getSnotify = (): SnotifyLike | undefined => {
  const inst = getCurrentInstance()
  if (inst) {
    // If you actually provide via app.provide('vue3-notify', snotify)
    const injected = inject<SnotifyLike>('vue3-notify')
    if (injected) return injected

    // Or via globalProperties.$snotify
    const gp = (inst.appContext.config.globalProperties as any) || {}
    const globalSnotify = gp.$snotify as SnotifyLike | undefined
    if (globalSnotify) return globalSnotify
  }

  return fallbackSnotify
}

export const showSuccess = (message: string) => {
  getSnotify()?.success(message)
}

export const showInfo = (message: string) => {
  getSnotify()?.info(message)
}

export const showErrorMsg = (message: string) => {
  getSnotify()?.error(message)
}

export const showError = (error: any) => {
  // Axios error compatibility
  const resp = error?.response ?? error
  const status = resp?.status
  const data = resp?.data ?? {}

  // Prefer backend messages when present
  const backendMessage = data?.message || data?.msg

  // Helper: extract first Laravel validation error
  const extractFirstError = (errors: any): string | null => {
    if (!errors || typeof errors !== 'object') return null

    const firstKey = Object.keys(errors)[0]
    if (!firstKey) return null

    const val = errors[firstKey]
    if (Array.isArray(val) && val.length) return val[0]
    if (typeof val === 'string') return val

    return null
  }

  if (status === 401) {
    return showErrorMsg(backendMessage || 'Unauthorized')
  }

  if (status === 404) {
    return showErrorMsg(backendMessage || 'Not found')
  }

  if (status === 422) {
    showValidationError(data?.errors)

    const first = extractFirstError(data?.errors)
    
    return showErrorMsg(first || backendMessage || 'Validation error')
  }

  if (status === 500) {
    return showErrorMsg(backendMessage || 'Server error')
  }

  return showErrorMsg(backendMessage || 'Internal server error')
}
