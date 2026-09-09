import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import tailwindcss from '@tailwindcss/vite'
import svgLoader from 'vite-svg-loader'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import vuetify from 'vite-plugin-vuetify'
import { fileURLToPath, URL } from 'node:url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

export default defineConfig({
  plugins: [
    tailwindcss(),
    svgLoader(),
    laravel({
      input: [
        'resources/ts/app.ts',
        'resources/css/app.css',
      ],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    vuetify(),
    Components({
      globs: [
        'resources/ts/components/*.vue',
        'resources/ts/components/**/*.vue',
      ],
      dts: true,
    }),
    AutoImport({
      imports: [
        'vue',
        '@vueuse/core',
        {
          '@inertiajs/vue3': [
            'router',
            'useForm',
            'usePage',
            'Link',
            'Head',
          ],
        },
        {
          'ziggy-js': [
            'route',
          ],
        },
        {
          '@unhead/vue': [
            'useHead',
          ],
        },
      ],
      dirs: [
        './resources/ts/composable/**',
        './resources/ts/utils/**',
      ],
      dts: true,
      vueTemplate: true,
    }),
  ],

  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/ts'),
      '@styles': fileURLToPath(
        new URL('./resources/ts/assets/styles', import.meta.url),
      ),
      '@components': path.resolve(
        __dirname,
        'resources/ts/components',
      ),
    },
  },

  server: {
    port: 5173,
    strictPort: false,
    cors: true,
  },
})
