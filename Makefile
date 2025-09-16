# Makefile na raiz do projeto
.PHONY: test tinker logs build up down

# Comandos para desenvolvimento
up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build --no-cache

test:
	docker compose exec app php artisan test

tinker:
	docker compose exec app php artisan tinker

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

# Composer
install:
	docker compose exec app composer install

update:
	docker compose exec app composer update

# Logs
logs:
	docker compose logs -f app

logs-mongo:
	docker compose logs -f mongo

# Limpeza
clear-cache:
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan cache:clear
