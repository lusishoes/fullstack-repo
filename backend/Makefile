.DEFAULT_GOAL := help

-include .env

RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))

DOCKER_COMPOSE = docker compose
APP = app
DOCKER_EXEC = $(DOCKER_COMPOSE) exec -u appuser $(APP)
DOCKER_TEST_EXEC = $(DOCKER_COMPOSE) exec -e XDEBUG_MODE=off -u appuser $(APP)

PHP = $(DOCKER_EXEC) php
ARTISAN = $(PHP) artisan
COMPOSER = $(DOCKER_EXEC) composer
BACKUP_DIR ?= ./database/backups

help: ## Список команд
	@awk 'BEGIN {FS = ":.*## "}; /^[a-zA-Z0-9_.-]+:.*## / { printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2 }' Makefile

build: ## Собрать образы
	$(DOCKER_COMPOSE) build

up: ## Поднять сервисы
	$(DOCKER_COMPOSE) up -d --remove-orphans

down: ## Остановить сервисы
	$(DOCKER_COMPOSE) down

restart: ## Перезапустить сервисы
	$(DOCKER_COMPOSE) restart

logs: ## Логи всех сервисов
	$(DOCKER_COMPOSE) logs -f

logs-app: ## Логи приложения
	$(DOCKER_COMPOSE) logs -f $(APP)

ps: ## Состояние сервисов
	$(DOCKER_COMPOSE) ps

shell: ## Шелл приложения
	$(DOCKER_EXEC) bash

root-shell: ## Шелл приложения от root
	$(DOCKER_COMPOSE) exec $(APP) bash

db-shell: ## psql
	$(DOCKER_COMPOSE) exec db psql -U ${DB_USERNAME} -d ${DB_DATABASE}

redis-shell: ## redis-cli
	$(DOCKER_COMPOSE) exec redis redis-cli

init: ## Первая установка: vendor, node_modules, ключ, миграции
	$(COMPOSER) install --no-interaction --prefer-dist
	$(DOCKER_EXEC) npm install --no-fund --no-audit
	$(ARTISAN) key:generate --no-interaction
	$(ARTISAN) migrate --no-interaction

composer-install: ## composer install
	$(COMPOSER) install

composer-update: ## composer update
	$(COMPOSER) update

artisan: ## artisan с аргументами: make artisan route:list
	$(ARTISAN) $(RUN_ARGS)

migrate: ## Миграции
	$(ARTISAN) migrate

migrate-fresh: ## Пересоздать базу с сидерами
	$(ARTISAN) migrate:fresh --seed

seed: ## Сидеры
	$(ARTISAN) db:seed

route-list: ## Маршруты
	$(ARTISAN) route:list --except-vendor

clear-all: ## Сбросить все кэши
	$(ARTISAN) optimize:clear

tinker: ## Tinker
	$(ARTISAN) tinker

octane-reload: ## Перезапустить воркеры Octane
	$(ARTISAN) octane:reload

ide-helper: ## Обновить файлы ide-helper
	$(ARTISAN) ide-helper:generate
	$(ARTISAN) ide-helper:models -N
	$(ARTISAN) ide-helper:meta

test: ## Все тесты параллельно
	$(DOCKER_TEST_EXEC) php artisan test --env=testing --parallel --recreate-databases

test-file: ## Один файл: make test-file FILE=app/Modules/Health/Tests/Feature/HealthTest.php
	$(DOCKER_TEST_EXEC) php artisan test --env=testing --compact $(FILE)

test-filter: ## По имени: make test-filter FILTER="часть имени"
	$(DOCKER_TEST_EXEC) php artisan test --env=testing --compact --filter="$(FILTER)"

pint: ## Pint, проверка
	$(DOCKER_EXEC) ./vendor/bin/pint --test

pint-fix: ## Pint, починка
	$(DOCKER_EXEC) ./vendor/bin/pint

rector: ## Rector
	$(DOCKER_EXEC) ./vendor/bin/rector

rector-dry: ## Rector без записи
	$(DOCKER_EXEC) ./vendor/bin/rector --dry-run

stan: ## PHPStan
	$(DOCKER_EXEC) ./vendor/bin/phpstan analyze --memory-limit=1G

stan-baseline: ## Пересобрать baseline PHPStan
	$(DOCKER_EXEC) ./vendor/bin/phpstan analyze -b --memory-limit=1G

quality: ## Гейт: форматтер, rector, phpstan, тесты
	$(MAKE) pint-fix
	$(MAKE) rector
	$(MAKE) stan
	$(MAKE) test

backup-db: ## Дамп базы в database/backups
	@mkdir -p $(BACKUP_DIR)
	$(DOCKER_COMPOSE) exec -T db pg_dump -U ${DB_USERNAME} ${DB_DATABASE} > $(BACKUP_DIR)/backup_$(shell date +%Y%m%d_%H%M%S).sql

restore-db: ## Восстановить дамп: make restore-db FILE=backup.sql
	$(DOCKER_COMPOSE) exec -T db psql -U ${DB_USERNAME} -d ${DB_DATABASE} < $(BACKUP_DIR)/$(FILE)

clean: ## Снести контейнеры, тома и образы
	$(DOCKER_COMPOSE) down -v --rmi local --remove-orphans

.PHONY: help build up down restart logs logs-app ps shell root-shell db-shell redis-shell init composer-install composer-update artisan migrate migrate-fresh seed route-list clear-all tinker octane-reload ide-helper test test-file test-filter pint pint-fix rector rector-dry stan stan-baseline quality backup-db restore-db clean

%:
	@true
