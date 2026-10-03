# fullstack-repo

| Каталог                   | Что внутри                                                                  |
| ------------------------- | --------------------------------------------------------------------------- |
| [`backend/`](./backend)   | Laravel 13, PHP 8.4, Octane, PostgreSQL, Redis, Docker; ядро `app/Ship`     |
| [`frontend/`](./frontend) | Nuxt 4, TypeScript, Pinia, Pinia Colada, Zod; слой запросов в `app/api`     |

## Быстрый старт

```sh
cd backend
cp .env.example .env && cp .env.testing.example .env.testing
make build && make up && make init

cd ../frontend
cp .env.example .env
pnpm install
pnpm dev
```

- Фронтенд: http://localhost:3000
- API: http://localhost:8000/api/health
