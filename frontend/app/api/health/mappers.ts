import type { Health } from '~/types/health'

import type { HealthDto } from './dto'

export function toHealth(dto: HealthDto): Health {
  return {
    isHealthy: dto.status === 'ok',
    services: {
      database: dto.services.database,
      cache: dto.services.cache,
    },
  }
}
