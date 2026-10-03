import { useLocalStorage } from '@vueuse/core'

import { clearQueryCache } from '~/api/queries'

const AUTH_TOKEN_KEY = 'auth:token'

// Стор хранит только сессию. Серверные данные живут в кэше Colada
// (app/api/<домен>/queries.ts) и в стор не копируются.
export const useAuthStore = defineStore('auth', () => {
  const storedToken = useLocalStorage<string>(AUTH_TOKEN_KEY, '', {
    flush: 'sync',
  })

  const token = computed<string | null>(() => storedToken.value || null)
  const isAuthenticated = computed<boolean>(() => token.value !== null)

  // Кэш Colada переживает смену аккаунта: без сноса запросы следующего
  // пользователя отдали бы данные предыдущего. Вотчер, а не экшены, потому
  // что вход в соседней вкладке меняет storage в обход стора.
  watch(token, () => {
    clearQueryCache()
  })

  function setToken(value: string): void {
    storedToken.value = value
  }

  function clear(): void {
    storedToken.value = ''
  }

  return { token, isAuthenticated, setToken, clear }
})
