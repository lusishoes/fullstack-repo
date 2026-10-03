const isVitest = process.env.VITEST === 'true'

export default defineNuxtConfig({
  compatibilityDate: '2026-10-01',
  devtools: { enabled: !isVitest },
  ssr: false,
  modules: isVitest
    ? ['@pinia/nuxt', '@pinia/colada-nuxt']
    : ['@nuxt/eslint', '@pinia/nuxt', '@pinia/colada-nuxt'],
  runtimeConfig: {
    public: {
      // Заполняется из .env: NUXT_PUBLIC_API_BASE.
      apiBase: '',
    },
  },
  typescript: {
    tsConfig: {
      // colada.options.ts лежит в корне (там его ищет @pinia/colada-nuxt)
      // и иначе не попадает ни в один tsconfig.
      include: ['../colada.options.ts', '../colada.d.ts'],
    },
  },
  app: {
    head: {
      htmlAttrs: { lang: 'ru' },
      title: 'Fullstack',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ],
    },
  },
  css: ['~/assets/css/main.css'],
  eslint: {
    config: {
      typescript: {
        tsconfigPath: '.nuxt/tsconfig.app.json',
      },
    },
  },
})
