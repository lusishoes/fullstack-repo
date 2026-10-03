import { describe, expect, it, vi } from 'vitest'

import type { ApiClientOptions } from '~/api/client'
import { createFetchOptions } from '~/api/client'
import {
  ApiError,
  ForbiddenError,
  NotFoundError,
  UnauthorizedError,
  ValidationError,
} from '~/api/errors'

type RequestHook = (ctx: { options: { headers: Headers } }) => void
type ResponseErrorHook = (ctx: {
  response: { status: number; _data: unknown }
}) => void

function buildOptions(overrides: Partial<ApiClientOptions> = {}) {
  const onUnauthorized = vi.fn()
  const onForbidden = vi.fn()
  const fetchOptions = createFetchOptions({
    baseURL: 'https://api.test',
    getToken: () => null,
    onUnauthorized,
    onForbidden,
    ...overrides,
  })

  return {
    onUnauthorized,
    onForbidden,
    onRequest: fetchOptions.onRequest as RequestHook,
    onResponseError: fetchOptions.onResponseError as ResponseErrorHook,
  }
}

function failWith(status: number, data: unknown) {
  return { response: { status, _data: data } }
}

describe('createFetchOptions / onRequest', () => {
  it('подставляет Bearer-заголовок, когда токен есть', () => {
    const { onRequest } = buildOptions({ getToken: () => 'token-1' })
    const headers = new Headers()

    onRequest({ options: { headers } })

    expect(headers.get('Authorization')).toBe('Bearer token-1')
  })

  it('не ставит заголовок без токена', () => {
    const { onRequest } = buildOptions()
    const headers = new Headers()

    onRequest({ options: { headers } })

    expect(headers.get('Authorization')).toBeNull()
  })
})

describe('createFetchOptions / onResponseError', () => {
  it('на 401 сбрасывает сессию и бросает UnauthorizedError', () => {
    const { onResponseError, onUnauthorized } = buildOptions()

    expect(() => {
      onResponseError(failWith(401, { message: 'Необходимо войти' }))
    }).toThrow(UnauthorizedError)
    expect(onUnauthorized).toHaveBeenCalledOnce()
  })

  it('на 403 зовёт onForbidden и бросает ForbiddenError', () => {
    const { onResponseError, onForbidden } = buildOptions()

    expect(() => {
      onResponseError(failWith(403, {}))
    }).toThrow(ForbiddenError)
    expect(onForbidden).toHaveBeenCalledWith(expect.any(ForbiddenError))
  })

  it('на 404 бросает NotFoundError', () => {
    const { onResponseError } = buildOptions()

    expect(() => {
      onResponseError(failWith(404, {}))
    }).toThrow(NotFoundError)
  })

  it('на 422 бросает ValidationError с полевыми ошибками', () => {
    const { onResponseError } = buildOptions()

    try {
      onResponseError(
        failWith(422, {
          message: 'Переданные данные некорректны',
          errors: { email: ['Укажите email'] },
        }),
      )
      expect.unreachable()
    } catch (error) {
      expect(error).toBeInstanceOf(ValidationError)
      expect((error as ValidationError).fields).toEqual({
        email: ['Укажите email'],
      })
    }
  })

  it('на прочие статусы бросает ApiError с кодом из тела', () => {
    const { onResponseError } = buildOptions()

    try {
      onResponseError(
        failWith(500, { message: 'Сломалось', code: 'fatal_error' }),
      )
      expect.unreachable()
    } catch (error) {
      expect(error).toBeInstanceOf(ApiError)
      expect((error as ApiError).status).toBe(500)
      expect((error as ApiError).code).toBe('fatal_error')
      expect((error as ApiError).message).toBe('Сломалось')
    }
  })
})
