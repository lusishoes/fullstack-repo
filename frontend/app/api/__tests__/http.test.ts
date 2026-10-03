import { describe, expect, it } from 'vitest'
import { z, ZodError } from 'zod'

import { ApiError } from '~/api/errors'
import { parseEmpty, parseItem, parseList, parsePage } from '~/api/http'

const itemSchema = z.object({ id: z.number(), title: z.string() })

function toTitle(dto: z.output<typeof itemSchema>): string {
  return dto.title
}

describe('parseItem', () => {
  it('проверяет конверт и маппит data', () => {
    const raw = { success: true, message: '', data: { id: 1, title: 'A' } }

    expect(parseItem(itemSchema, toTitle, raw)).toBe('A')
  })

  it('бросает ApiError на 200 с success: false', () => {
    const raw = { success: false, message: 'Отказано' }

    expect(() => parseItem(itemSchema, toTitle, raw)).toThrow(ApiError)
    expect(() => parseItem(itemSchema, toTitle, raw)).toThrow('Отказано')
  })

  it('бросает ZodError, когда форма data разошлась с контрактом', () => {
    const raw = { success: true, data: { id: '1', title: 'A' } }

    expect(() => parseItem(itemSchema, toTitle, raw)).toThrow(ZodError)
  })
})

describe('parseList', () => {
  it('маппит каждый элемент массива', () => {
    const raw = {
      success: true,
      data: [
        { id: 1, title: 'A' },
        { id: 2, title: 'B' },
      ],
    }

    expect(parseList(itemSchema, toTitle, raw)).toEqual(['A', 'B'])
  })
})

describe('parsePage', () => {
  it('собирает rows и total из data и meta', () => {
    const raw = {
      success: true,
      data: [{ id: 1, title: 'A' }],
      meta: { total: 40 },
    }

    expect(parsePage(itemSchema, toTitle, raw)).toEqual({
      rows: ['A'],
      total: 40,
    })
  })
})

describe('parseEmpty', () => {
  it('пропускает успешный конверт без data', () => {
    expect(() => {
      parseEmpty({ success: true })
    }).not.toThrow()
  })

  it('бросает ApiError на success: false', () => {
    expect(() => {
      parseEmpty({ success: false })
    }).toThrow(ApiError)
  })
})
