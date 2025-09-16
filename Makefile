# Makefile - Task Management System (Clean Architecture + Swagger)
.PHONY: help docs docs-generate docs-validate docs-clean swagger-setup

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
# DOCUMENTATION COMMANDS (CLEAN ARCHITECTURE)
# ============================================
swagger-setup:
	@echo "🎯 Configurando Swagger com Documentação Separada..."
	@echo "📦 Instalando l5-swagger..."
	docker compose exec app composer require darkaonline/l5-swagger
	@echo "📝 Publicando configuração..."
	docker compose exec app php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
	@echo "📁 Criando estrutura por bounded context..."
	docker compose exec app mkdir -p app/BoundedContext/Task/Infrastructure/Documentation
	docker compose exec app mkdir -p app/BoundedContext/Iam/Infrastructure/Documentation
	docker compose exec app mkdir -p app/BoundedContext/AuditLog/Infrastructure/Documentation
	docker compose exec app mkdir -p app/BoundedContext/Common/Infrastructure/Documentation
	@echo "✅ Swagger configurado! Aplique os artifacts e execute: make docs"

docs-generate:
	@echo "📚 Gerando documentação Swagger..."
	docker compose exec app php artisan l5-swagger:generate

docs-validate:
	@echo "🔍 Validando estrutura de documentação..."
	@if docker compose exec app ls app/BoundedContext/*/Infrastructure/Documentation/*.php > /dev/null 2>&1; then \
		echo "✅ Documentação encontrada por bounded context"; \
	else \
		echo "⚠️  Documentação não encontrada - aplique os artifacts"; \
		exit 1; \
	fi

docs-clean:
	@echo "🧹 Limpando documentação gerada..."
	docker compose exec app rm -rf storage/api-docs/*
	docker compose exec app php artisan config:clear

docs: docs-validate docs-clean docs-generate
	@echo "🎉 Documentação gerada com sucesso!"
	@echo "📊 Acesse: http://localhost:8000/api/documentation"

docs-watch:
	@echo "👀 Observando mudanças na documentação..."
	@echo "🔄 Regenerando a cada 5 segundos..."
	@while true; do \
		make docs-generate > /dev/null 2>&1; \
		echo "🔄 Documentação atualizada: $$(date '+%H:%M:%S')"; \
		sleep 5; \
	done

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
	@echo "  • Swagger Docs: http://localhost:8000/api/documentation"
	@echo "  • MySQL: localhost:3306"
	@echo "  • MongoDB: localhost:27017"
	@echo "  • Mongo Express: http://localhost:8081"
	@echo ""
	@echo "📋 Documentação organizada:"
	@if docker compose exec app ls app/BoundedContext/*/Infrastructure/Documentation/ > /dev/null 2>&1; then \
		echo "  ✅ Task Documentation"; \
		echo "  ✅ IAM Documentation"; \
		echo "  ✅ AuditLog Documentation"; \
		echo "  ✅ Common Documentation"; \
	else \
		echo "  ⚠️  Documentação não configurada"; \
	fi

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
	@echo "  │   │   └── Documentation/   # Swagger Docs"
	@echo "  ├── Iam/                    # Identity & Access Management"
	@echo "  ├── AuditLog/               # Audit Trail (MongoDB)"
	@echo "  └── Common/                 # Shared Components"
	@echo ""
	@echo "✅ BENEFÍCIOS DA SEPARAÇÃO:"
	@echo "  • Controllers limpos sem poluição"
	@echo "  • Documentação organizada por domínio"
	@echo "  • Facilita manutenção e evolução"
	@echo "  • Segue princípios SOLID"

help:
	@echo "🚀 Task Management System - Clean Architecture + Swagger"
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
	@echo "📚 Documentação (Clean Architecture):"
	@echo "  swagger-setup   - Configurar Swagger separado"
	@echo "  docs            - Gerar documentação completa"
	@echo "  docs-generate   - Regenerar documentação"
	@echo "  docs-validate   - Validar estrutura"
	@echo "  docs-clean      - Limpar documentação"
	@echo "  docs-watch      - Observar mudanças"
	@echo ""
	@echo "🔍 Utilitários:"
	@echo "  status          - Status do sistema + docs"
	@echo "  architecture    - Mostrar estrutura DDD"
	@echo "  help            - Esta ajuda"
