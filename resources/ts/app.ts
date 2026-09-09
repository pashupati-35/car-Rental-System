import { createApp, h, DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createHead, useHead } from '@vueuse/head'
import { registerPlugins } from './utils/plugins'
import './styles/tailwind.css'

const head = createHead()
const DEFAULT_HEAD_TITLE = 'Car Rental System'

createInertiaApp({
  title: title => (title ? `${title} - ${DEFAULT_HEAD_TITLE}` : DEFAULT_HEAD_TITLE),
  resolve: async name => {
    const pages = import.meta.glob('./pages/**/*.vue')
    let importer: (() => Promise<unknown>) | undefined = pages[`./pages/${name}.vue`]

    if (!importer) {
      const entry = Object.entries(pages).find(([path]) =>
        path.toLowerCase().endsWith(`/${name.toLowerCase()}.vue`),
      )
      importer = entry ? entry[1] : undefined
    }

    if (!importer) {
      throw new Error(`Page not found: ./pages/${name}.vue`)
    }

    const page = (await importer()) as { default: DefineComponent & { layout?: DefineComponent } }
    page.default.layout = page.default.layout || undefined

    return page.default
  },

  setup({ el, App, props, plugin }) {
    const app = createApp({
      setup() {
        useHead({ title: DEFAULT_HEAD_TITLE })
        return () => h(App, props)
      },
    })
      .use(plugin)
      .use(head)

    registerPlugins(app)
    app.mount(el)
  },

  progress: {
    color: '#3b82f6',
    showSpinner: true,
  },
})
