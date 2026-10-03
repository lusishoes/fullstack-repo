# Frontend

Nuxt 4 (SPA) · Vue 3 · TypeScript · Pinia · Pinia Colada · Zod.

Запросы идут через слой `app/api`: транспорт с типизированными ошибками, по
каждому домену схема ответа (`dto.ts`), маппер в доменный тип (`mappers.ts`),
эндпоинты (`api.ts`) и запросы Colada (`queries.ts`). Подробно в
[CLAUDE.md](./CLAUDE.md).

## Запуск

Сначала поднимите бэкенд (`../backend`, `make up`).

```sh
cp .env.example .env
pnpm install
pnpm dev
```

Откройте http://localhost:3000: главная показывает состояние бэкенда, Postgres
и Redis.

## Проверки

```sh
pnpm check   # формат, линт, типы, тесты
pnpm build
```
