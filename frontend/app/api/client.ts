import { HttpStatusCode } from './enum'
import {
  ApiError,
  ForbiddenError,
  NotFoundError,
  UnauthorizedError,
  ValidationError,
} from './errors'

// Тип опций берётся у самого $fetch.create, без прямой зависимости на ofetch.
type FetchOptions = NonNullable<Parameters<typeof $fetch.create>[0]>

export interface ApiClientOptions {
  baseURL: string
  getToken: () => string | null
  onUnauthorized: () => void
  onForbidden?: (error: ForbiddenError) => void
  headers?: Record<string, string>
}

interface ErrorPayload {
  message?: string
  code?: string
  errors?: Record<string, string | string[]>
}

function toErrorPayload(data: unknown): ErrorPayload {
  if (typeof data !== 'object' || data === null) {
    return {}
  }

  return data
}

export function createFetchOptions(options: ApiClientOptions): FetchOptions {
  return {
    baseURL: options.baseURL,
    headers: { Accept: 'application/json', ...options.headers },
    onRequest: ({ options: request }) => {
      const token = options.getToken()

      if (token) {
        request.headers.set('Authorization', `Bearer ${token}`)
      }
    },
    onResponseError: ({ response }) => {
      throwForResponse(response.status, response._data, options)
    },
  }
}

function throwForResponse(
  status: number,
  data: unknown,
  options: ApiClientOptions,
): never {
  const payload = toErrorPayload(data)
  const message = payload.message || 'Ошибка запроса'

  switch (status) {
    case HttpStatusCode.Unauthorized:
      options.onUnauthorized()
      throw new UnauthorizedError(message, data)
    case HttpStatusCode.Forbidden: {
      const forbidden = new ForbiddenError(message, data)

      options.onForbidden?.(forbidden)
      throw forbidden
    }
    case HttpStatusCode.NotFound:
      throw new NotFoundError(message, data)
    case HttpStatusCode.UnprocessableEntity:
      throw new ValidationError(message, payload.errors, data)
    default:
      throw new ApiError(message, status, payload.code ?? null, data)
  }
}

export function createApiClient(options: ApiClientOptions): typeof $fetch {
  return $fetch.create(createFetchOptions(options))
}
