import { HttpStatusCode } from './enum'

export class ApiError extends Error {
  constructor(
    message: string,
    public readonly status: number,
    public readonly code: string | null = null,
    public readonly payload: unknown = null,
  ) {
    super(message)
    this.name = 'ApiError'
  }
}

export class UnauthorizedError extends ApiError {
  constructor(message: string, payload: unknown = null) {
    super(message, HttpStatusCode.Unauthorized, null, payload)
    this.name = 'UnauthorizedError'
  }
}

export class ForbiddenError extends ApiError {
  constructor(message: string, payload: unknown = null) {
    super(message, HttpStatusCode.Forbidden, null, payload)
    this.name = 'ForbiddenError'
  }
}

export class NotFoundError extends ApiError {
  constructor(message: string, payload: unknown = null) {
    super(message, HttpStatusCode.NotFound, null, payload)
    this.name = 'NotFoundError'
  }
}

export class ValidationError extends ApiError {
  readonly fields: Record<string, string[]>

  constructor(
    message: string,
    errors: Record<string, string | string[]> = {},
    payload: unknown = null,
  ) {
    super(message, HttpStatusCode.UnprocessableEntity, null, payload)
    this.name = 'ValidationError'
    this.fields = normalizeFields(errors)
  }
}

function normalizeFields(
  errors: Record<string, string | string[]>,
): Record<string, string[]> {
  const fields: Record<string, string[]> = {}

  for (const [field, messages] of Object.entries(errors)) {
    fields[field] = Array.isArray(messages) ? messages : [messages]
  }

  return fields
}

// Строка для console.error. Целиком объект ошибки логировать нельзя: у
// FetchError через свойства достижимо тело запроса — в консоль утекли бы
// пароль или контакты пользователя.
export function describeError(error: unknown): string {
  if (error instanceof Error) {
    return `${error.name}: ${error.message}`
  }

  return String(error)
}

// Сообщение для пользователя. У 422 берётся первая полевая ошибка: она
// конкретнее общего «Переданные данные некорректны».
export function resolveApiErrorMessage(
  error: unknown,
  fallback = 'Что-то пошло не так',
): string {
  if (error instanceof ValidationError) {
    const firstFieldMessage = Object.values(error.fields)[0]?.[0]

    return firstFieldMessage ?? (error.message || fallback)
  }

  if (error instanceof ApiError) {
    return error.message || fallback
  }

  return fallback
}
