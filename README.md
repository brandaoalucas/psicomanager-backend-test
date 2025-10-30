### Comandos Make de Referência (Linux/macOS)

```bash
make help           # Lista todos os comandos disponíveis
make architecture   # Mostra a estrutura DDD do projeto
make status         # Status completo do sistema
```

### Para Usuários Windows 🪟

**Recomendação**: Use **WSL2** para a melhor experiência de desenvolvimento:

1. **Instalar WSL2:**

    ```cmd
    wsl --install
    ```

2. **Acessar o ambiente Linux:**

    ```cmd
    wsl
    ```

3. \*\*Usar# 🚀 Task Management System

Sistema de gerenciamento de tarefas desenvolvido com **Laravel 12**, **PHP 8.2+**, **MySQL** e **MongoDB** seguindo os princípios de **Domain-Driven Design (DDD)** e **Clean Architecture**.

> **⚠️ Importante:** Este projeto usa **Docker Compose V2** (`docker compose` sem hífen). Se você estiver usando uma versão mais antiga do Docker, atualize para a versão mais recente.

## 🏗️ Arquitetura

### Bounded Contexts

O projeto segue uma arquitetura baseada em **Bounded Contexts** com separação clara de responsabilidades:

-   **🔐 IAM**: Gerenciamento de usuários e autenticação
-   **📋 Task**: Gerenciamento de tarefas (CRUD + estados)
-   **📝 AuditLog**: Sistema de logs de auditoria (MongoDB)

### Estrutura de Camadas (DDD + Clean Architecture)

```
app/BoundedContext/{Context}/
├── Application/
│   ├── Command/          # Command Handlers (CQRS)
│   ├── Query/            # Query Handlers (CQRS)
│   └── Service/          # Application Services
├── Domain/
│   ├── Entity/           # Entidades de Domínio
│   ├── Repository/       # Interfaces de Repositório
│   ├── Enum/             # Enums de Domínio
│   └── EntityFactory/    # Factory de entities
└── Infrastructure/
    ├── Inbound/Http/     # Controllers, Requests, Middleware
    └── Outbound/         # Repositórios, External Services
```

## 🚀 Tecnologias

-   **PHP 8.2+**
-   **Laravel 12** com Route Attributes
-   **MySQL 8.0** (Banco relacional para usuários e tarefas)
-   **MongoDB 7.0** (Banco não-relacional para logs de auditoria)
-   **Docker & Docker Compose V2** (comando `docker compose` sem hífen)
-   **Laravel Sanctum** (Autenticação via tokens)
-   **CQRS Pattern** (Command Query Responsibility Segregation)

## 🏃‍♂️ Instalação e Execução

### Compatibilidade

| Sistema        | Makefile      | Docker Compose V2 | Recomendação                             |
| -------------- | ------------- | ----------------- | ---------------------------------------- |
| 🐧 **Linux**   | ✅ Nativo     | ✅ Nativo         | Use `make` commands                      |
| 🍎 **macOS**   | ✅ Nativo     | ✅ Nativo         | Use `make` commands                      |
| 🪟 **Windows** | ❌ Não nativo | ✅ Nativo         | Use `docker compose` diretamente ou WSL2 |

### Pré-requisitos

-   Docker (com Docker Compose V2)
-   Git

### 1. Clone o repositório

```bash
git clone https://github.com/brandaoalucas/psicomanager-backend-test
cd psicomanager-backend-test
```

### 2. Configure as variáveis de ambiente

```bash
# Linux/macOS com Make
make env

# Ou manualmente em qualquer sistema
cp .env.example .env
```

#### 3. 🐧 Linux / 🍎 macOS (Usando Makefile)

```bash
# Criar estrutura/imagem
make build

# Subir containers
make up

# Instalar dependências
make install

# Executar migrações
make migrate

# Ver status do sistema
make status
```

#### 3. 🪟 Windows (Usando Docker Compose V2)

```cmd
# Copiar arquivo de ambiente
copy .env.example .env

# Subir containers
docker compose up -d

# Instalar dependências
docker compose exec app composer install

# Executar migrações
docker compose exec app php artisan migrate

# Ver logs
docker compose logs -f app

# Parar containers
docker compose down
```

### 5. Comandos por Sistema Operacional

#### 🐧 Linux / 🍎 macOS - Comandos Make

#### 🪟 Windows - Comandos Docker Compose V2

| Função                    | Comando Windows                                                                                               |
| ------------------------- | ------------------------------------------------------------------------------------------------------------- |
| **Copiar env**            | `copy .env.example .env` (ou `make env` no WSL)                                                               |
| **Subir containers**      | `docker compose up -d`                                                                                        |
| **Parar containers**      | `docker compose down`                                                                                         |
| **Rebuild containers**    | `docker compose build --no-cache`                                                                             |
| **Ver logs**              | `docker compose logs -f app`                                                                                  |
| **Instalar dependências** | `docker compose exec app composer install`                                                                    |
| **Executar migrações**    | `docker compose exec app php artisan migrate`                                                                 |
| **Limpar cache**          | `docker compose exec app php artisan config:clear && docker compose exec app php artisan cache:clear`         |
| **Testes seguros**        | `docker compose exec -e APP_ENV=testing -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: app php artisan test` |
| **Status dos containers** | `docker compose ps`                                                                                           |

#### 🪟 Alternativas para Windows

**Opção 1: WSL2 (Recomendado)**

```bash
# Instalar WSL2 e usar comandos make normalmente
wsl --install
# Depois usar make normalmente dentro do WSL
```

**Opção 2: Git Bash com Make**

```bash
# Instalar make via Chocolatey
choco install make

# Ou via Scoop
scoop install make
```

**Opção 3: Usar PowerShell com Aliases**

```powershell
# Criar aliases no PowerShell (adicionar ao $PROFILE)
function Start-App { docker compose up -d }
function Stop-App { docker compose down }
function Install-Dependencies { docker compose exec app composer install }
function Run-Migrations { docker compose exec app php artisan migrate }

# Usar como:
Start-App
Install-Dependencies
Run-Migrations
```

### 🌐 Serviços Disponíveis

Após inicializar o projeto, os seguintes serviços estarão disponíveis:

-   **🌐 Aplicação**: http://localhost:8000
-   **🗄️ MySQL**: localhost:3306
-   **🍃 MongoDB**: localhost:27017
-   **📊 Mongo Express**: http://localhost:8081 (admin/admin)

## 📋 Documentação da API

### 🔐 Autenticação

#### **POST** `/sanctum/token` - Login

**Requisição:**

```json
{
    "email": "admin@test.com",
    "password": "123456"
}
```

**Resposta:**

```json
{
    "user": {
        "id": 1,
        "name": "Administrator",
        "username": "admin",
        "email": "admin@example.com"
    },
    "token": "1|abc123...xyz789"
}
```

> **Nota:** Para todas as rotas protegidas, inclua o header:
>
> ```
> Authorization: Bearer {token}
> ```

### 📋 Gerenciamento de Tarefas

#### **POST** `/tasks` - Criar Tarefa

**Requisição:**

```json
{
    "title": "Minha Nova Tarefa",
    "description": "Descrição detalhada da tarefa",
    "status": "pending"
}
```

**Resposta:**

```json
{
    "id": 1,
    "title": "Minha Nova Tarefa",
    "description": "Descrição detalhada da tarefa",
    "status": "pending",
    "priority": 1,
    "user_id": 1,
    "created_at": "2025-09-16T10:00:00Z",
    "updated_at": "2025-09-16T10:00:00Z"
}
```

#### **GET** `/tasks` - Listar Tarefas

**Parâmetros de Query:**

-   `status`: `pending|in_progress|completed`
-   `page`: Número da página
-   `per_page`: Itens por página

**Exemplo:**

```bash
GET /tasks?status=pending&page=1&per_page=10
```

**Resposta:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Tarefa 1",
            "description": "Descrição da tarefa",
            "status": "pending",
            "priority": 1,
            "user_id": 1,
            "created_at": "2025-09-16T10:00:00Z"
        }
    ],
    "current_page": 1,
    "total": 25,
    "per_page": 10,
    "last_page": 3
}
```

#### **GET** `/tasks/{id}` - Obter Tarefa por ID

**Resposta:**

```json
{
    "id": 1,
    "title": "Minha Tarefa",
    "description": "Descrição da tarefa",
    "status": "pending",
    "priority": 1,
    "user_id": 1,
    "created_at": "2025-09-16T10:00:00Z",
    "updated_at": "2025-09-16T10:00:00Z"
}
```

#### **PUT** `/tasks/{id}` - Atualizar Tarefa

**Requisição:**

```json
{
    "title": "Título Atualizado",
    "description": "Nova descrição",
    "priority": 2
}
```

**Resposta:**

```json
1
```

#### **PATCH** `/tasks/{id}/start` - Iniciar Tarefa

Altera o status para `in_progress`.

#### **PATCH** `/tasks/{id}/complete` - Completar Tarefa

Altera o status para `completed`.

#### **PATCH** `/tasks/{id}/restart` - Reiniciar Tarefa

Altera o status para `pending`.

#### **DELETE** `/tasks/{id}` - Excluir Tarefa

Soft delete da tarefa.

**Resposta:**

```json
1
```

### 📝 Logs de Auditoria

#### **GET** `/audit-logs` - Listar Logs

**Parâmetros de Query:**

-   `reference_type`: `Task|User`
-   `reference_id`: ID da referência
-   `user_id`: ID do usuário
-   `page`: Número da página

**Exemplo:**

```bash
GET /audit-logs?reference_type=Task&reference_id=1&page=1
```

**Resposta:**

```json
{
    "data": [
        {
            "_id": "66e7a1b4c5d6e7f8a9b0c1d2",
            "user_id": 1,
            "reference_type": "Task",
            "reference_id": 1,
            "action": "created",
            "description": "Task 'Minha Tarefa' was created",
            "created_at": "2025-09-16T10:00:00Z"
        }
    ],
    "current_page": 1,
    "total": 15,
    "per_page": 10
}
```

### 📊 Health Check

#### **GET** `/health` - Status da Aplicação

**Resposta:**

```json
{
    "status": "ok",
    "timestamp": "2025-09-16T10:00:00Z",
    "services": {
        "mysql": "connected",
        "mongodb": "connected"
    }
}
```

## 🗄️ Estrutura do Banco de Dados

### MySQL (Dados Relacionais)

#### Tabela `users`

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Tabela `tasks`

```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'PENDING',
    priority INTEGER DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### MongoDB (Logs de Auditoria)

#### Collection `audit_logs`

```javascript
{
  "_id": ObjectId("..."),
  "user_id": 1,
  "reference_type": "Task",
  "reference_id": 1,
  "action": "created",
  "description": "Task 'My Task' was created",
  "created_at": ISODate("2025-09-16T10:00:00Z")
}
```

## 🔧 Desenvolvimento

### Padrões Utilizados

#### CQRS (Command Query Responsibility Segregation)

**Exemplo de Command:**

```php
class CreateTaskCommand {
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly TaskStatus $status,
        public readonly User $loggedUser
    ) {}
}
```

**Exemplo de Command Handler:**

```php
class CreateTaskCommandHandler {
    public function handle(CreateTaskCommand $command): Task {
        // Lógica de negócio e validações
        // Criação da tarefa
        // Registro de logs de auditoria
    }
}
```

### Eventos de Auditoria

Todos os eventos importantes são automaticamente registrados no MongoDB:

-   ✅ Criação de tarefas
-   ✏️ Atualização de tarefas
-   🗑️ Exclusão de tarefas
-   🔄 Mudanças de status

## 🧪 Testes

### Executar Testes

#### 🐧 Linux / 🍎 macOS

```bash
# Todos os testes (modo seguro com SQLite)
make test-safe

# Teste específico
make test-filter FILTER=TaskController

# Testes no container
docker compose exec app php artisan test
```

#### 🪟 Windows

```cmd
REM Todos os testes (modo seguro com SQLite)
docker compose exec -e APP_ENV=testing -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: app php artisan test

REM Teste específico
docker compose exec -e APP_ENV=testing -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: app php artisan test --filter=TaskController

REM Testes no container
docker compose exec app php artisan test

REM Testes com coverage
docker compose exec app php artisan test --coverage
```

### Estrutura de Testes

O projeto possui testes de integração cobrindo:

-   ✅ Autenticação via Sanctum
-   ✅ CRUD completo de tarefas
-   ✅ Mudanças de status das tarefas
-   ✅ Logs de auditoria
-   ✅ Validações de entrada
-   ✅ Tratamento de erros

## 🚦 Monitoramento e Logs

### Logs da Aplicação

#### 🐧 Linux / 🍎 macOS

```bash
# Ver logs em tempo real
make logs

# Logs específicos do MySQL
docker compose logs -f mysql

# Logs específicos do MongoDB
docker compose logs -f mongo
```

#### 🪟 Windows

```cmd
REM Ver logs em tempo real
docker compose logs -f app

REM Logs específicos do MySQL
docker compose logs -f mysql

REM Logs específicos do MongoDB
docker compose logs -f mongo

REM Status dos containers
docker compose ps
```

### Estrutura de Logs

-   **Application Logs**: Armazenados em `storage/logs/`
-   **Audit Logs**: Armazenados no MongoDB
-   **Error Logs**: Integrados com o sistema de logs do Laravel

## 🔒 Segurança

### Funcionalidades Implementadas

-   🔐 **Autenticação via Laravel Sanctum**
-   🛡️ **Validação rigorosa de dados**
-   🧹 **Sanitização de inputs**

### Variáveis de Ambiente Importantes

```bash
# Chave de criptografia da aplicação
APP_KEY=base64:...

# Configurações de banco
DB_PASSWORD=secret
MYSQL_ROOT_PASSWORD=secret

# Configurações MongoDB
MONGO_INITDB_ROOT_PASSWORD=secret

# Senha master para testes (desenvolvimento apenas)
SANCTUM_MASTER_PASSWORD=123456
```

## 📈 Performance e Escalabilidade

### Otimizações Implementadas

-   🗃️ **Índices otimizados** no MySQL e MongoDB
-   🔍 **Queries preparadas** para prevenir SQL injection
-   ⚡ **Cache de configurações** do Laravel
-   🔄 **Eager loading** de relacionamentos
-   📄 **Paginação eficiente** em todas as listagens

### Considerações de Escalabilidade

-   📊 **Separação de responsabilidades** via DDD
-   🔄 **CQRS** para separar leitura e escrita
-   🎯 **Repository Pattern** para abstração de dados
-   🧩 **Bounded Contexts** para modularidade

## 🛠️ Solução de Problemas

### Problemas Comuns

**1. Erro de conexão com o banco:**

```bash
# Linux/macOS
make down && make up && make status

# Windows
docker compose down && docker compose up -d && docker compose ps
```

**2. Problemas de permissão (Linux/macOS):**

```bash
sudo chown -R $(whoami):$(whoami) .
make clear-cache  # ou: docker compose exec app php artisan config:clear
```

**3. Containers não sobem:**

```bash
docker system prune -f
# Linux/macOS: make build
# Windows: docker compose build --no-cache
```

**4. Erro de migração:**

```bash
# Linux/macOS: make migrate
# Windows: docker compose exec app php artisan migrate
```

**5. Windows: "make: command not found"**

```cmd
REM Use docker compose diretamente:
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate

REM Ou instale WSL2:
wsl --install
```

### Logs de Debug

```bash
# Ver logs detalhados
docker-compose logs -f app

# Debug de conectividade
make status
```

## 📚 Recursos Adicionais

### Documentação Técnica

-   [Laravel 12 Documentation](https://laravel.com/docs/12.x)
-   [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum)
-   [MongoDB Laravel Driver](https://github.com/mongodb/laravel-mongodb)
-   [Route Attributes](https://github.com/spatie/laravel-route-attributes)

### Comandos Make de Referência

```bash
make help           # Lista todos os comandos disponíveis
make architecture   # Mostra a estrutura DDD do projeto
make status         # Status completo do sistema
```

## 🤝 Contribuição

### Diretrizes

1. 🧪 **Sempre adicione testes** para novas funcionalidades
2. 📏 **Siga os padrões** de código estabelecidos (PSR-12)
3. 📝 **Documente** mudanças significativas
4. ✅ **Valide** que todos os testes passam antes do commit

### Estrutura de Commits

```bash
git commit -m "feat(task): add priority field to tasks"
git commit -m "fix(auth): resolve token expiration issue"
git commit -m "docs(readme): update API documentation"
```

---

## 💡 Notas Finais

Este projeto demonstra uma implementação robusta e escalável de um sistema de gerenciamento de tarefas, seguindo as melhores práticas da indústria.

**Desenvolvido com ❤️ em Laravel 12 + PHP 8.2**
