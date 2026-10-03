import { z } from 'zod'

// GET /api/health — снято с живого бэкенда (backend/app/Modules/Health).
export const healthDtoSchema = z.object({
  status: z.enum(['ok', 'degraded']),
  services: z.object({
    database: z.boolean(),
    cache: z.boolean(),
  }),
})

export type HealthDto = z.infer<typeof healthDtoSchema>
