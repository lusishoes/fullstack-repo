import type { PiniaColadaOptions } from '@pinia/colada'

// Рефетч по фокусу окна выключен: возврат во вкладку не должен дёргать бэк.
export default {
  queryOptions: {
    staleTime: 0,
    refetchOnWindowFocus: false,
  },
} satisfies PiniaColadaOptions
