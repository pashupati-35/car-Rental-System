import { useCookie } from "@/composable/useCookie"

//  IsEmpty
export const isEmpty = (value: unknown): boolean => {
  if (value === null || value === undefined || value === '')
    return true

  return !!(Array.isArray(value) && value.length === 0)
}

//  IsNullOrUndefined
export const isNullOrUndefined = (value: unknown): value is undefined | null => {
  return value === null || value === undefined
}

//  IsEmptyArray
export const isEmptyArray = (arr: unknown): boolean => {
  return Array.isArray(arr) && arr.length === 0
}

//  IsObject
export const isObject = (obj: unknown): obj is Record<string, unknown> =>
  obj !== null && !!obj && typeof obj === 'object' && !Array.isArray(obj)

//  IsToday
export const isToday = (date: Date) => {
  const today = new Date()

  return (
    date.getDate() === today.getDate()
    && date.getMonth() === today.getMonth()
    && date.getFullYear() === today.getFullYear()
  )
}
export const namespaceConfig = (str: string) => `futech-solution-${str}`

export const cookieRef = <T>(key: string, defaultValue: T) => {
  return useCookie<T>(namespaceConfig(key), { default: () => defaultValue })
}

/**
 * Universal media and image URL resolver.
 * Handles objects { original, thumb }, relative paths, uploads, storage, and full URLs.
 */
export const resolveMediaUrl = (image?: any, imagePath?: any, defaultFolder = ''): string => {
  if (!image && !imagePath) return ''

  // If passed an entire model / resource object as first argument
  if (image && typeof image === 'object' && ('image_url' in image || 'image_path' in image || 'avatar_url' in image)) {
    const candidate = image.image_url || image.avatar_url || image.image_path || image.image || image.avatar
    if (candidate && candidate !== image) {
      return resolveMediaUrl(candidate, null, defaultFolder)
    }
  }

  // 1. Check imagePath object
  if (imagePath && typeof imagePath === 'object') {
    if (typeof imagePath.original === 'string' && imagePath.original.trim()) {
      return resolveMediaUrl(imagePath.original, null, defaultFolder)
    }
    if (typeof imagePath.thumb === 'string' && imagePath.thumb.trim()) {
      return resolveMediaUrl(imagePath.thumb, null, defaultFolder)
    }
    if (typeof imagePath.image_url === 'string' && imagePath.image_url.trim()) {
      return resolveMediaUrl(imagePath.image_url, null, defaultFolder)
    }
  }

  // 2. Check if imagePath is a string
  if (typeof imagePath === 'string' && imagePath.trim()) {
    const trimmed = imagePath.trim()
    if (trimmed.startsWith('http://') || trimmed.startsWith('https://') || trimmed.startsWith('blob:') || trimmed.startsWith('data:')) {
      return trimmed
    }
    const clean = trimmed.replace(/^\/+/, '')
    if (clean.startsWith('uploads/') || clean.startsWith('storage/')) {
      return `/${clean}`
    }
    
    return defaultFolder ? `/uploads/${defaultFolder.replace(/^\/+|\/+$/g, '')}/${clean}` : `/uploads/${clean}`
  }

  // 3. Check image object
  if (image && typeof image === 'object') {
    if (typeof image.original === 'string' && image.original.trim()) {
      return resolveMediaUrl(image.original, null, defaultFolder)
    }
    if (typeof image.thumb === 'string' && image.thumb.trim()) {
      return resolveMediaUrl(image.thumb, null, defaultFolder)
    }
    if (typeof image.image_url === 'string' && image.image_url.trim()) {
      return resolveMediaUrl(image.image_url, null, defaultFolder)
    }
  }

  // 4. Check if image is a string
  if (typeof image === 'string' && image.trim()) {
    const trimmed = image.trim()
    if (trimmed.startsWith('http://') || trimmed.startsWith('https://') || trimmed.startsWith('blob:') || trimmed.startsWith('data:')) {
      return trimmed
    }
    const clean = trimmed.replace(/^\/+/, '')
    if (clean.startsWith('uploads/') || clean.startsWith('storage/')) {
      return `/${clean}`
    }
    if (defaultFolder) {
      const folderClean = defaultFolder.replace(/^\/+|\/+$/g, '')
      if (clean.startsWith(folderClean + '/')) {
        return `/uploads/${clean}`
      }
      
      return `/uploads/${folderClean}/${clean}`
    }
    
    return `/uploads/${clean}`
  }

  return ''
}

export const resolveImageUrl = resolveMediaUrl

