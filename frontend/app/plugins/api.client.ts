import { createApiClient } from '~/api/client'
import { useAuthStore } from '~/stores/auth'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()

  const api = createApiClient({
    baseURL: config.public.apiBase,
    getToken: () => useAuthStore().token,
    onUnauthorized: () => {
      useAuthStore().clear()
    },
  })

  return { provide: { api } }
})
