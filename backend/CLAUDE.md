# CLAUDE.md

## Project Overview
- Laravel 13, PHP ^8.4, Octane on Swoole, PostgreSQL 17, Redis 8 (cache, queue, sessions).
- Everything runs in Docker (`docker-compose.yml`); use `make` targets, they exec into the `app` container.
- Modular architecture (Porto-style, hand-rolled, no apiato package): domain modules live in `app/Modules/{Module}`.
- Administrative modules live in `app/Admin/{Module}` and mirror domain module names.
- Shared abstractions/base classes are in `app/Ship`, cross-module services and integrations in `app/Services`.

## How modules are wired
- `App\Ship\Providers\ShipServiceProvider` is the only provider in `bootstrap/providers.php`.
- It auto-loads from every `app/Modules/*` and `app/Admin/*`: `Providers/*` (registered), `Database/Migrations`, `Commands`.
- Routes are auto-loaded by `App\Ship\Providers\RouteServiceProvider`:
  - `app/Modules/{Module}/Routes/api.php` → prefix `/api`, middleware `api`;
  - `app/Modules/{Module}/Routes/web.php` → middleware `web`;
  - `app/Admin/{Module}/Routes/api.php` → prefix `/admin`, middleware `api`, `auth:sanctum`.
- Model factories are located by convention (`App\Ship\Traits\FactoryLocatorTrait`):
  `App\Modules\{Module}\Models\X` → `App\Modules\{Module}\Database\Factories\XFactory`.
- API errors are rendered by `App\Ship\Exceptions\Handlers\ApiExceptionRenderer` into
  `{ success: false, message, code?, errors? }`; success goes through `Controller::success()` into
  `{ success: true, message, data }`. The frontend checks `success` on every response.

## Architecture and Structure
- Flow: Route → Controller → DTO when needed → Action → Task/Repository → Resource → JsonResponse.
  Reference implementation: `app/Modules/Health`.
- Controllers stay thin: accept a validated DTO for body/query input, inject an Action, and return a Resource/JsonResponse. Do not call Tasks/Repositories directly.
- Admin controllers live in `app/Admin/{Module}/Controllers` and must be `__invoke` controllers.
- Actions (business use cases) do not call other Actions except explicit local `*SubAction` patterns.
- Tasks are atomic operations with `run()` and do not call Actions.
- Repositories extend `App\Ship\Parents\Repository`; query processors implement `App\Ship\Interfaces\Process`
  and are applied via `throughProcessors()`.
- DTO/Data classes extend `App\Ship\Parents\Dto` (spatie/laravel-data) and live in `app/Modules/{Module}/Dto`,
  `app/Admin/{Module}/Dto` or `app/Ship/Dto`.
- Admin code uses resources from `app/Admin/{Module}/Resources`, never from `app/Modules/{Module}/Resources`.
- Exceptions: only custom classes extending `App\Ship\Exceptions\Abstract*Exception`, no bare `\Exception`.
- Third-party integrations live in `app/Modules/{Module}/Integrations` behind contracts.
- Models, migrations, and factories stay in `app/Modules/{Module}` even when used by admin features.

## Naming and Database
- Controllers: singular noun + `Controller`.
- Tables: plural, `snake_case`. Pivot tables: two singular model names in alphabetical order (`entity_tag`).
- Foreign keys `model_id`; booleans `is_`/`has_`, `active` as the enabled flag; instants `*_at`.
- Migrations are required for every schema change; add indexes/relationships immediately.

## Tests (Pest)
- Module tests: `app/Modules/{Module}/Tests/Feature|Unit`, admin tests: `app/Admin/{Module}/Tests/Feature|Unit`.
- Groups and paths are configured in `tests/Pest.php`.
- Tests run against the separate `fullstack_test` database (forced in `phpunit.xml`, created by
  `docker/postgres/initdb`). Never point tests at the working database.
- One `it(...)` per case, descriptions in Russian, no `$this` in tests/hooks (use `Pest\Laravel\*` functions).
- Use factories and states; do not hardcode paths, use `route(...)`.

## Code Style (Pint / PSR-12)
- Pint config in `pint.json`: short arrays, single quotes, strict comparisons, blank line before
  `return/throw/if/foreach/...`, trailing commas in multiline, mandatory types, `?Type` for nullables.
- `declare(strict_types=1);` in every PHP file.
- PHPDoc over inline comments; comment only what the code cannot say.

## Octane
- The app is a long-running process: never keep request state in singletons or static properties;
  prefer `$this->app->scoped()` for per-request services.
- In dev Octane runs with `--watch --poll`, code changes reload workers automatically. Polling is
  required: file events from a Windows bind mount never reach the container.

## Commands
- `make up` / `make down` / `make logs-app` / `make shell`
- `make init` — first install: composer, npm, key, migrations
- `make test`, `make test-file FILE=...`, `make test-filter FILTER="..."`
- `make pint-fix`, `make rector`, `make stan`
- `make quality` — the gate: Pint → Rector → PHPStan (level 6) → tests

## Gotchas
- On Docker Desktop for Windows the bind mount appears as `root:root 755`; `docker/php/boot/10-permissions.sh`
  hands files to `appuser` on container start. If `make` commands fail with "Permission denied",
  run `docker compose restart app`.
- DB and Redis connections come from `.env`, not from container environment: container env vars would
  override `.env.testing` and point tests at the working database.
- `pecl.php.net` and `deb.nodesource.com` are unreachable from some networks: Xdebug is built from GitHub
  sources, Node comes from Debian repos.
