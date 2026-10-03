import type { ApiError } from '~/api/errors'

declare module '@pinia/colada' {
  interface TypesConfig {
    defaultError: ApiError
  }
}

export {}
