import type { App } from 'vue'

const blockedTags = new Set([
  'base',
  'button',
  'embed',
  'form',
  'iframe',
  'input',
  'link',
  'math',
  'meta',
  'object',
  'option',
  'script',
  'select',
  'style',
  'svg',
  'textarea',
])

const urlAttributes = new Set([
  'action',
  'formaction',
  'href',
  'poster',
  'src',
  'xlink:href',
])

function isSafeUrl(value: string): boolean {
  const url = value.trim().replace(/[\u0000-\u001F\u007F\s]+/g, '')
  const lowerUrl = url.toLowerCase()

  if (
    lowerUrl.startsWith('#') ||
    lowerUrl.startsWith('/') ||
    lowerUrl.startsWith('./') ||
    lowerUrl.startsWith('../')
  ) {
    return true
  }

  if (lowerUrl.startsWith('data:image/')) {
    return true
  }

  try {
    return ['http:', 'https:', 'mailto:', 'tel:'].includes(new URL(url).protocol)
  } catch {
    return false
  }
}

function sanitizeElement(element: Element): void {
  Array.from(element.attributes).forEach(attribute => {
    const attributeName = attribute.name.toLowerCase()

    if (
      attributeName.startsWith('on') ||
      attributeName === 'srcdoc' ||
      attributeName === 'style' ||
      attributeName.startsWith('xmlns')
    ) {
      element.removeAttribute(attribute.name)

      return
    }

    if (urlAttributes.has(attributeName) && !isSafeUrl(attribute.value)) {
      element.removeAttribute(attribute.name)
    }
  })

  if (element.tagName.toLowerCase() === 'a' && element.getAttribute('target') === '_blank') {
    element.setAttribute('rel', 'noopener noreferrer')
  }
}

export function sanitizeHtml(value: unknown): string {
  if (value === null || value === undefined) {
    return ''
  }

  const document = new DOMParser().parseFromString(String(value), 'text/html')

  Array.from(document.body.querySelectorAll('*')).forEach(element => {
    if (blockedTags.has(element.tagName.toLowerCase())) {
      element.remove()

      return
    }

    sanitizeElement(element)
  })

  return document.body.innerHTML
}

function renderSafeHtml(
  element: HTMLElement,
  binding: { value: string | null | undefined },
): void {
  element.innerHTML = sanitizeHtml(binding.value)
}
export default function registerSafeHtmlDirective(app: App): void {
  app.directive('safe', {
    mounted: renderSafeHtml,
    updated: renderSafeHtml,
  })
}
