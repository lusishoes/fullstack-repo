// @ts-check
import eslintConfigPrettier from 'eslint-config-prettier'
import simpleImportSort from 'eslint-plugin-simple-import-sort'

import withNuxt from './.nuxt/eslint.config.mjs'

const autoImported = [
  {
    name: 'vue',
    importNames: [
      'ref',
      'computed',
      'reactive',
      'readonly',
      'watch',
      'watchEffect',
      'onMounted',
      'onUnmounted',
      'onBeforeMount',
      'onBeforeUnmount',
      'nextTick',
      'toRef',
      'toRefs',
      'shallowRef',
    ],
    message: '"{{importName}}" автоимпортируется Nuxt, уберите импорт.',
  },
  {
    name: 'pinia',
    message: 'Pinia автоимпортируется Nuxt, уберите импорт.',
  },
  {
    name: 'vue-router',
    message: 'Роутер автоимпортируется Nuxt, уберите импорт.',
  },
]

const internalPatterns = [
  {
    group: ['#app', '#app/*', '#imports', '#components', '#build', '#build/*'],
    message: 'Внутренние модули Nuxt не импортируются руками.',
  },
  {
    group: ['../../*'],
    message: 'Вместо относительного пути через два уровня используйте `~/...`.',
  },
]

export default withNuxt(
  {
    plugins: {
      'simple-import-sort': simpleImportSort,
    },
    rules: {
      'simple-import-sort/imports': 'error',
      'simple-import-sort/exports': 'error',
      curly: ['error', 'all'],
      eqeqeq: ['error', 'always', { null: 'ignore' }],
      'no-console': ['error', { allow: ['warn', 'error'] }],
      'no-debugger': 'error',
      'no-restricted-imports': [
        'error',
        { paths: autoImported, patterns: internalPatterns },
      ],
      // HTTP-статус из ответа приходит числом, и сравнение его с числовым
      // enum (HttpStatusCode) — штатная операция, а не ошибка типов.
      '@typescript-eslint/no-unsafe-enum-comparison': 'off',
      '@typescript-eslint/no-unsafe-enum-assignment': 'off',
      'vue/component-name-in-template-casing': ['error', 'PascalCase'],
      'vue/block-order': ['error', { order: ['script', 'template', 'style'] }],
    },
  },
  {
    files: ['**/__tests__/**', '**/*.test.ts'],
    rules: {
      'no-restricted-imports': 'off',
    },
  },
  eslintConfigPrettier,
)
