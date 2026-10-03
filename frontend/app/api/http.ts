import type { ZodType } from 'zod'
import { z } from 'zod'

import type { ResourcePage } from '~/api/resource'

import { HttpStatusCode } from './enum'
import { ApiError } from './errors'

// Бэкенд умеет ответить 200 с { success: false }: конверт проверяется на
// каждом ответе, HTTP-статуса недостаточно.
const envelopeSchema = z.object({
  success: z.boolean(),
  message: z.string().optional(),
})

const dataEnvelopeSchema = z.object({ data: z.unknown() })

const pageMetaSchema = z.object({
  meta: z.object({ total: z.number() }),
})

function assertSuccess(raw: unknown): void {
  const envelope = envelopeSchema.parse(raw)

  if (!envelope.success) {
    throw new ApiError(envelope.message || 'Ошибка запроса', HttpStatusCode.Ok)
  }
}

function unwrapData(raw: unknown): unknown {
  assertSuccess(raw)

  return dataEnvelopeSchema.parse(raw).data
}

export function parseEmpty(raw: unknown): void {
  assertSuccess(raw)
}

export function parseItem<S extends ZodType, T>(
  schema: S,
  toModel: (dto: z.output<S>) => T,
  raw: unknown,
): T {
  return toModel(schema.parse(unwrapData(raw)))
}

export function parseList<S extends ZodType, T>(
  schema: S,
  toModel: (dto: z.output<S>) => T,
  raw: unknown,
): T[] {
  return z.array(schema).parse(unwrapData(raw)).map(toModel)
}

export function parsePage<S extends ZodType, T>(
  schema: S,
  toModel: (dto: z.output<S>) => T,
  raw: unknown,
): ResourcePage<T> {
  const rows = z.array(schema).parse(unwrapData(raw)).map(toModel)
  const { total } = pageMetaSchema.parse(raw).meta

  return { rows, total }
}
