import { healthApi } from '~/api/health/api'
import type { Health } from '~/types/health'

export const healthKeys = {
  root: ['health'] as const,
}

export const healthQuery = defineQueryOptions({
  key: healthKeys.root,
  query: ({ signal }): Promise<Health> => healthApi.fetch(signal),
})
