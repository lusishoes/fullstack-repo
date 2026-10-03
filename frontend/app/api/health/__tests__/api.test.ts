import { describe, expect, it, vi } from 'vitest'

import { ApiError } from '~/api/errors'
import { healthApi } from '~/api/health/api'

function fetcherOf(response: unknown) {
  const fetchMock = vi.fn().mockResolvedValue(response)

  return {
    fetchMock,
    fetcher: () => fetchMock as unknown as typeof $fetch,
  }
}

describe('healthApi.fetch', () => {
  it('ходит в /health и отдаёт доменную модель', async () => {
    const { fetchMock, fetcher } = fetcherOf({
      success: true,
      message: '',
      data: {
        status: 'ok',
        services: { database: true, cache: true },
      },
    })

    await expect(healthApi.fetch(undefined, fetcher)).resolves.toEqual({
      isHealthy: true,
      services: { database: true, cache: true },
    })
    expect(fetchMock).toHaveBeenCalledWith('/health', { signal: undefined })
  })

  it('считает degraded нездоровым состоянием', async () => {
    const { fetcher } = fetcherOf({
      success: true,
      data: {
        status: 'degraded',
        services: { database: true, cache: false },
      },
    })

    await expect(healthApi.fetch(undefined, fetcher)).resolves.toEqual({
      isHealthy: false,
      services: { database: true, cache: false },
    })
  })

  it('пробрасывает отказ из конверта', async () => {
    const { fetcher } = fetcherOf({ success: false, message: 'Отказано' })

    await expect(healthApi.fetch(undefined, fetcher)).rejects.toBeInstanceOf(
      ApiError,
    )
  })
})
