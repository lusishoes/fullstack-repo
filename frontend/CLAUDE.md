# CLAUDE.md

## Project

Nuxt 4 SPA (`ssr: false`), Vue 3 + TypeScript, Pinia, Pinia Colada, Zod, VueUse.
Architecture of requests, stores, mappers and types follows `b2b_cms_nuxt`.
Package manager is **pnpm**.

## Commands

- `pnpm dev` — dev server on :3000 (backend must be up, see `../backend`)
- `pnpm typecheck` — `vue-tsc -b --noEmit` (not `tsc`)
- `pnpm lint` / `pnpm lint:fix` — ESLint (type-aware, via `@nuxt/eslint`)
- `pnpm format` / `pnpm format:check` — Prettier
- `pnpm test` — Vitest once; `pnpm test <path>` for one file
- `pnpm check` — the gate: format → lint → typecheck → tests. Add `pnpm build` for anything visual.

## Data layer

```
transport  app/api/client.ts, errors.ts, http.ts     ($fetch + typed errors + envelope parsing)
  -> domain  app/api/<domain>/dto.ts      Zod schema of the backend payload (snake_case, as is)
             app/api/<domain>/mappers.ts  dto -> domain model
             app/api/<domain>/api.ts      endpoints; the only place a backend URL is written
             app/api/<domain>/queries.ts  Colada keys + defineQueryOptions
    -> component  useQuery(xxxQuery) / useMutation
```

- Domain models live in `app/types/<domain>.ts` (camelCase, one file per entity). Components and
  stores only see domain models, never DTOs.
- Every response is parsed with `parseItem` / `parseList` / `parsePage` / `parseEmpty`: they check the
  `{ success, message, data }` envelope (200 can carry `success: false`) and validate `data` with Zod.
- The transport throws typed errors: `UnauthorizedError` (401, also clears the session),
  `ForbiddenError`, `NotFoundError`, `ValidationError` (422, `fields` per input), `ApiError`.
  Colada's default error type is `ApiError` (`colada.d.ts`). User-facing text: `resolveApiErrorMessage`.
- API functions take an optional `fetcher` (default `useNuxtApp().$api`), so tests pass a mock
  instead of mounting Nuxt. Reference domain: `app/api/health`.
- Server state lives in the Colada cache, not in Pinia. A store holds client state only
  (session token in `stores/auth.ts`). Never put a raw backend response into a store.
- Global Colada options: `colada.options.ts` (`staleTime: 0`, no refetch on window focus).
- Changing the account wipes the Colada cache (`clearQueryCache`, watcher in `stores/auth.ts`).

## Conventions

- Nuxt auto-imports: do not import from `vue`, `pinia`, `vue-router`, `#app`, `#imports`
  (enforced by ESLint, tests are exempt). VueUse is imported explicitly from `@vueuse/core`.
- Components in templates are PascalCase; SFC block order `<script>` → `<template>` → `<style>`.
- Prettier: no semicolons, single quotes, trailing commas, `singleAttributePerLine`, width 80.
- Type the generic, not the variable: `ref<boolean>(false)`, `computed<string>(...)`.
- `composables/` is stateful, `utils/` is stateless; shared UI types/enums in `app/common/`.
- Comments only for what code cannot say.

## Testing

- Specs live in `__tests__/<Name>.test.ts` next to the code, names in Russian.
- Default Vitest environment is `node`; add `// @vitest-environment nuxt` at the top only when the
  spec needs a Nuxt instance (mounted component, `useNuxtApp`, router).

## Gotchas

- `.npmrc` has `minimum-release-age=1440`: a version younger than a day is not installed. If pnpm
  reports `NO_MATURE_MATCHING_VERSION`, lower the range floor instead of removing the guard.
- `@pinia/colada-nuxt` 1.2 requires `@nuxt/kit ^4.5.2`, keep Nuxt in step.
- TypeScript is pinned to 6.x: 7.x is the native compiler and `vue-tsc` is not verified with it.
