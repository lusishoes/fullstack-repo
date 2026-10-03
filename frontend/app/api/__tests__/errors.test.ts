import { describe, expect, it } from 'vitest'

import {
  ApiError,
  describeError,
  resolveApiErrorMessage,
  ValidationError,
} from '~/api/errors'

describe('ValidationError', () => {
  it('приводит одиночные сообщения полей к массивам', () => {
    const error = new ValidationError('Некорректно', {
      email: 'Укажите email',
      name: ['Слишком длинно', 'Недопустимые символы'],
    })

    expect(error.fields).toEqual({
      email: ['Укажите email'],
      name: ['Слишком длинно', 'Недопустимые символы'],
    })
  })
})

describe('resolveApiErrorMessage', () => {
  it('у 422 берёт первую полевую ошибку', () => {
    const error = new ValidationError('Некорректно', { email: 'Укажите email' })

    expect(resolveApiErrorMessage(error)).toBe('Укажите email')
  })

  it('у ApiError берёт сообщение бэкенда', () => {
    expect(resolveApiErrorMessage(new ApiError('Сломалось', 500))).toBe(
      'Сломалось',
    )
  })

  it('для чужой ошибки отдаёт запасной текст', () => {
    expect(resolveApiErrorMessage(new Error('x'), 'Запасной')).toBe('Запасной')
  })
})

describe('describeError', () => {
  it('отдаёт только имя и сообщение', () => {
    expect(describeError(new ApiError('Сломалось', 500))).toBe(
      'ApiError: Сломалось',
    )
  })
})
