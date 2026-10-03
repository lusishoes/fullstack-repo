import { defineVitestConfig } from '@nuxt/test-utils/config'

export default defineVitestConfig({
  test: {
    globals: true,
    // По умолчанию лёгкий `node`. Спекам, которым нужен инстанс Nuxt
    // (смонтированный компонент, useNuxtApp, $fetch-обёртки), окружение
    // включается директивой `// @vitest-environment nuxt` в начале файла.
    environment: 'node',
    environmentOptions: {
      nuxt: {
        domEnvironment: 'happy-dom',
      },
    },
    include: ['app/**/*.{test,spec}.ts'],
    exclude: ['node_modules', 'dist', '.nuxt', 'e2e'],
    clearMocks: true,
  },
})
