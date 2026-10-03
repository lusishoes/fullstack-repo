import { parseItem } from '~/api/http'
import type { Fetcher } from '~/api/resource'
import type { Health } from '~/types/health'

import { healthDtoSchema } from './dto'
import { toHealth } from './mappers'

const api: Fetcher = () => useNuxtApp().$api

export const healthApi = {
  fetch: (signal?: AbortSignal, fetcher: Fetcher = api): Promise<Health> =>
    fetcher()('/health', { signal }).then((raw) =>
      parseItem(healthDtoSchema, toHealth, raw),
    ),
}
