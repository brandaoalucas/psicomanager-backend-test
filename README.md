# Task Management System

Sistema de gerenciamento de tarefas desenvolvido com Laravel 12, PHP 8.2+, MySQL e MongoDB seguindo os princípios de Domain-Driven Design (DDD) e Clean Architecture.

## 🏗️ Arquitetura

O projeto segue uma arquitetura baseada em **Bounded Contexts** com separação clara de responsabilidades:

### Bounded Contexts

-   **Iam**: Gerenciamento de usuários e autenticação
-   **Task**: Gerenciamento de tarefas
-   **AuditLog**: Sistema de logs de auditoria (MongoDB)

### Estrutura de Camadas

```
app/BoundedContext/{Context}/
├── Application/
│   ├── Command/          # Command Handlers (CQRS)
│   └── Service/          # Application Services
├── Domain/
│   ├── Entity/           # Entidades de Domínio
│   ├── Repository/       # Interfaces de Repositório
│   ├── Enum/             # Enums de Domínio
│   └── ValueObject/      # Value Objects
└── Infrastructure/
    ├── Inbound/Http/     # Controllers e Requests
    └── Outbound/         # Implementações de Repositório
```

## 🚀 Tecnologias

-   **PHP 8.2+**
-   **Laravel 12**
-   **MySQL 8.0** (Banco relacional para tarefas)
-   **MongoDB 7.0** (Banco não-relacional para logs)
-   **Docker & Docker Compose**
-   **Laravel Sanctum** (Autenticação)

## 📋 Funcionalidades

### Endpoints da API

#### Autenticação

#### Tarefas

#### Logs de Auditoria

## 🐳 Docker

### Pré-requisitos

-   Docker
-   Docker Compose

### Executando o Projeto

1. **Clone o repositório**

```bash
git clone https://github.com/brandaoalucas/psicomanager-backend-test
cd psicomanager-backend-test
```

2. **Configure as variáveis de ambiente**

```bash
cp .env.example .env
```

3. **Execute o Docker Compose**

```bash
docker-compose up -d
```

4. **Instale as dependências**

```bash
docker-compose exec app composer install
```

5. **Gere a chave da aplicação**

```bash
docker-compose exec app php artisan key:generate
```

6. **Execute as migrações**

```bash
docker-compose exec app php artisan migrate
```

### Serviços Disponíveis

-   **Aplicação**: http://localhost:8000
-   **MySQL**: localhost:3306
-   **MongoDB**: localhost:27017
-   **Mongo Express**: http://localhost:8081 (admin/admin)

## 🗄️ Bancos de Dados

### MySQL (Tarefas e Usuários)

```sql
-- Tabela de usuários
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Tabela de tarefas
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 🔧 Desenvolvimento

### Estrutura de Comandos

O projeto utiliza o padrão CQRS (Command Query Responsibility Segregation):

```php
// Exemplo de Command
class CreateTaskCommand {
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly TaskStatus $status,
        public readonly User $loggedUser
    ) {}
}

// Exemplo de Command Handler
class CreateTaskCommandHandler {
    public function handle(CreateTaskCommand $command): Task {
        // Lógica de negócio
    }
}
```

### Logs de Auditoria

Todos os eventos importantes são registrados no MongoDB:

```php
// Exemplo de uso
$CreateAuditLogCommand = new CreateAuditLogCommand(
    userId: $user->id,
    referenceType: 'Task',
    referenceId: $task->id,
    action: 'created',
    description: "Task '{$task->title}' was created"
);

$CreateAuditLogHandler->handle($auditLogCommand);
```

## 🧪 Testes

```bash
# Executar testes
docker-compose exec app php artisan test

# Executar testes com coverage
docker-compose exec app php artisan test --coverage
```

## 📊 Monitoramento

### Logs da Aplicação

```bash
docker-compose logs -f app
```

### Logs do MySQL

```bash
docker-compose logs -f mysql
```

### Logs do MongoDB

```bash
docker-compose logs -f mongo
```

## �� Segurança

-   Autenticação via Laravel Sanctum
-   Validação de dados em todas as requisições
-   Rate limiting para tentativas de login
-   Sanitização de inputs
-   CORS configurado

## 📈 Performance

-   Índices otimizados no MySQL e MongoDB
-   Queries preparadas
-   Cache de configurações
-   Eager loading de relacionamentos

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request
