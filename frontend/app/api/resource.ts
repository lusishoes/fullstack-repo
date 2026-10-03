export interface ResourcePage<T> {
  rows: T[]
  total: number
}

export type ResourceFetcher<T, Q> = (
  query: Q,
  signal: AbortSignal,
) => Promise<ResourcePage<T>>

/** Фабрика клиента: в коде это `useNuxtApp().$api`, в тестах — мок. */
export type Fetcher = () => typeof $fetch
