# Makefile - Task Management System
.PHONY: help

# ============================================
# DOCKER COMMANDS
# ============================================
up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build --no-cache

logs:
	docker compose logs -f app

# ============================================
# APPLICATION COMMANDS
# ============================================
install:
	docker compose exec app composer install

migrate:
	docker compose exec app php artisan migrate

clear-cache:
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan route:clear

# ============================================
# TESTING COMMANDS (SAFE)
# ============================================
test-safe:
	@echo "🧪 Executando testes com SQLite em memória (SEGURO)"
	docker compose exec -e APP_ENV=testing -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: app php artisan test

test-filter:
	@if [ -z "$(FILTER)" ]; then \
		echo "Use: make test-filter FILTER=TaskController"; \
		exit 1; \
	fi
	@echo "🧪 Executando teste: $(FILTER)"
	docker compose exec -e APP_ENV=testing -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: app php artisan test --filter=$(FILTER)

# ============================================
# UTILITY COMMANDS
# ============================================
status:
	@echo "🔍 Status do Task Management System:"
	@echo "====================================="
	docker compose ps
	@echo ""
	@echo "🌐 URLs disponíveis:"
	@echo "  • Aplicação: http://localhost:8000"
	@echo "  • MySQL: localhost:3306"
	@echo "  • MongoDB: localhost:27017"
	@echo "  • Mongo Express: http://localhost:8081"
	@echo ""

architecture:
	@echo "🏗️  ARQUITETURA DO PROJETO:"
	@echo "==========================="
	@echo ""
	@echo "📂 Estrutura DDD + Clean Architecture:"
	@echo "  app/BoundedContext/"
	@echo "  ├── Task/"
	@echo "  │   ├── Application/          # CQRS Commands & Queries"
	@echo "  │   ├── Domain/              # Entities & Business Logic"
	@echo "  │   ├── Infrastructure/"
	@echo "  │   │   ├── Inbound/Http/    # Controllers (LIMPOS!)"
	@echo "  │   │   ├── Outbound/        # Repositories"
	@echo "  ├── Iam/                    # Identity & Access Management"
	@echo "  ├── AuditLog/               # Audit Trail (MongoDB)"
	@echo "  └── Common/                 # Shared Components"
	@echo ""

help:
	@echo "🚀 Task Management System"
	@echo "========================================================"
	@echo ""
	@echo "🐳 Docker:"
	@echo "  up              - Subir containers"
	@echo "  down            - Parar containers"
	@echo "  build           - Rebuild containers"
	@echo ""
	@echo "📦 Aplicação:"
	@echo "  install         - Instalar dependências"
	@echo "  migrate         - Executar migrações"
	@echo "  clear-cache     - Limpar cache"
	@echo ""
	@echo "🧪 Testes:"
	@echo "  test-safe       - Testes seguros (SQLite)"
	@echo "  test-filter     - Teste específico (FILTER=Nome)"
	@echo ""
	@echo "🔍 Utilitários:"
	@echo "  status          - Status do sistema + docs"
	@echo "  architecture    - Mostrar estrutura
	@echo "  help            - Esta ajuda"
