# Backend

Laravel 13 · PHP 8.4 · Octane (Swoole) · PostgreSQL 17 · Redis 8 · Docker.

Модульная архитектура в духе Porto: общие базовые классы в `app/Ship`, предметные
модули в `app/Modules/{Module}`, админка в `app/Admin/{Module}`. Правила
и устройство описаны в [CLAUDE.md](./CLAUDE.md).

## Первый запуск

```sh
cp .env.example .env
cp .env.testing.example .env.testing
make build
make up
make init
```

После этого:

- API: http://localhost:8000/api/health
- Postgres с хоста: `localhost:5440`, Redis: `localhost:6390`

## Каждый день

```sh
make up          # поднять
make logs-app    # логи Octane, очереди и планировщика
make test        # тесты
make quality     # Pint, Rector, PHPStan, тесты
make help        # все команды
```
