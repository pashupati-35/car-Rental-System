export const convertFormData = (data: Record<string, any>): FormData => {
  const formData = new FormData()

  const append = (key: string, val: any) => {
    if (val === undefined || val === null) return

    // File
    if (val instanceof File) {
      formData.append(key, val)

      return
    }

    // Array → key[]
    if (Array.isArray(val)) {
      if (val.length === 0) return // don't append empty arrays
      val.forEach((item: any) => {
        if (item instanceof File) formData.append(`${key}[]`, item)
        else if (typeof item === 'object') formData.append(`${key}[]`, JSON.stringify(item))
        else formData.append(`${key}[]`, String(item))
      })

      return
    }

    // Booleans / numbers / strings
    if (typeof val === 'boolean') {
      formData.append(key, val ? '1' : '0')

      return
    }

    if (typeof val === 'number') {
      formData.append(key, String(val))

      return
    }

    formData.append(key, String(val))
  }

  for (const [key, value] of Object.entries(data))
    append(key, value)

  return formData
}

export { resolveMediaUrl, resolveImageUrl } from './helpers'
