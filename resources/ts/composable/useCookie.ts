import type { CookieParseOptions, CookieSerializeOptions } from 'cookie-es'
import { parse, serialize } from 'cookie-es'
import { destr } from 'destr'

type _CookieOptions = Omit<CookieSerializeOptions & CookieParseOptions, 'decode' | 'encode'>

export interface CookieOptions<T> extends _CookieOptions {
  decode?(value: string): T
  encode?(value: T): string
  default?: () => T | Ref<T>
  watch?: boolean | 'shallow'
}

export type CookieRef<T> = Ref<T>

const CookieDefaults: CookieOptions<unknown> = {
  path: '/',
  watch: true,
  decode: val => destr(decodeURIComponent(val)),
  encode: val => encodeURIComponent(typeof val === 'string' ? val : JSON.stringify(val)),
}

export const useCookie = <T = string | null | undefined>(name: string, _opts?: CookieOptions<T>): CookieRef<T> => {
  const opts = { ...CookieDefaults, ..._opts || {} }
  const cookies = parse(document.cookie)

  const cookie = ref<T | undefined>(cookies[name] as any ?? opts.default?.())

  watch(cookie, () => {
    document.cookie = serializeCookie(name, cookie.value, opts)
  })

  return cookie as CookieRef<T>
}

function serializeCookie(name: string, value: string, opts: CookieSerializeOptions = {}) {
  if (value === null || value === undefined)
    return serialize(name, value, { ...opts, maxAge: -1 })

  return serialize(name, value, opts)
}
