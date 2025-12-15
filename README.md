# ST SSO Project

Single Sign-On (SSO) system built with Laravel and Domain-Driven Design (DDD) architecture.

## Architecture Overview

This project follows **Domain-Driven Design (DDD)** principles with **Bounded Contexts**:

- **SharedKernel**: Common components shared across contexts (Value Objects, Domain Exceptions, Outbox Pattern)
- **IdentityAccess**: Authentication, Authorization, and RBAC (Roles, Permissions, Clients, Tokens)
- **OrganizationalStructure**: User profiles, Faculty, Department management

### Project Structure

```
app/
├── SharedKernel/              # Shared components across contexts
│   ├── Domain/
│   │   ├── ValueObjects/      # Email, Uuid, Timestamp, Status
│   │   ├── Exceptions/        # DomainException, EntityNotFoundException
│   │   └── Repositories/      # OutboxEventRepositoryInterface
│   └── Infrastructure/
│       ├── Http/              # HealthCheckController
│       └── Persistence/       # OutboxEvent (Eloquent model)
│
├── IdentityAccess/            # Authentication & Authorization Context
│   ├── Domain/
│   │   ├── Aggregates/        # UserIdentity, Role, Permission, Client
│   │   ├── Entities/          # AccessToken
│   │   ├── ValueObjects/      # UserIdentityId, Username, PasswordHash, etc.
│   │   ├── Events/            # Domain events
│   │   └── Repositories/      # Repository interfaces
│   ├── Application/
│   │   ├── DTOs/              # Data Transfer Objects
│   │   ├── UseCases/          # Application services (Use Cases)
│   │   └── Services/          # AuthorizationService
│   └── Infrastructure/
│       ├── Http/              # Controllers, Middleware
│       ├── Persistence/       # Eloquent repositories
│       ├── Services/          # PassportTokenGenerator, UserIdentityBridgeService
│       └── Policies/          # Authorization policies
│
├── OrganizationalStructure/   # User Profiles & Organization Context
│   ├── Domain/
│   │   ├── Aggregates/        # User
│   │   ├── Entities/          # Faculty, Department
│   │   ├── ValueObjects/      # UserId, FullName, PhoneNumber, etc.
│   │   └── Repositories/      # Repository interfaces
│   ├── Application/
│   │   ├── DTOs/              # Data Transfer Objects
│   │   ├── UseCases/          # Application services
│   │   └── Services/          # PolicyAuthorizationService
│   └── Infrastructure/
│       ├── Http/              # Controllers
│       ├── Persistence/       # Eloquent repositories
│       ├── Eloquent/          # Eloquent models
│       └── Policies/          # Authorization policies
│
└── Models/                    # Legacy models (for migrations only)
```

## Prerequisites

- **Docker & Docker Compose** installed
- **Make** installed (for running commands easily)
- **mkcert** installed (for generating SSL certificates)

## Setting Up the Project with Docker

### 1. Clone the Repository
```sh
git clone git@github.com:LeHongMinh-ST/st.sso.git
cd st.sso
```

### 2. Install mkcert and Generate SSL Certificates

#### macOS
```sh
brew install mkcert nss
mkcert -install
```

#### Linux
```sh
sudo apt install libnss3-tools -y  # Debian/Ubuntu
sudo yum install nss-tools -y       # CentOS/RHEL
curl -JLO "https://github.com/FiloSottile/mkcert/releases/latest/download/mkcert-$(uname -s)-$(uname -m)"
chmod +x mkcert-$(uname -s)-$(uname -m)
sudo mv mkcert-$(uname -s)-$(uname -m) /usr/local/bin/mkcert
mkcert -install
```

#### Windows (PowerShell as Administrator)
```powershell
choco install mkcert -y
mkcert -install
```

### 3. Generate SSL Certificates
```sh
# Create the certs directory if it doesn't exist
mkdir -p .docker/local/certs

# Generate SSL certificates for the domain st.sso.dev
mkcert -key-file .docker/local/certs/st.sso.dev-key.pem -cert-file .docker/local/certs/st.sso.dev.pem st.sso.dev localhost 127.0.0.1 ::1
```

### 4. Add Domain to Hosts File
Add the following line to your system's hosts file:

**macOS/Linux:**
```sh
echo "127.0.0.1 st.sso.dev" | sudo tee -a /etc/hosts
```

**Windows (Run PowerShell as Administrator):**
```powershell
Add-Content -Path "C:\Windows\System32\drivers\etc\hosts" -Value "127.0.0.1 st.sso.dev"
```

### 5. Start Docker Containers

#### Mac/Linux (Using Make)
```sh
make up
```

#### Windows (Using Docker Compose Directly)
```powershell
docker-compose up -d --build
```
This will build and start the necessary services.

### 6. Access the Project
- App: [https://st.sso.dev:8882](https://st.sso.dev:8882)

### 7. Stopping the Containers

#### Mac/Linux (Using Make)
```sh
make down
```

#### Windows (Using Docker Compose Directly)
```powershell
docker-compose down

```

## Setting Up the Project basic

### 1. Clone the Repository
```sh
git clone git@github.com:LeHongMinh-ST/st.sso.git
cd st.sso
```
### 2. Copy .env.example to .env
```sh
cp .env.example .env
```
### 3. Run composer install
```sh
composer install
php artisan key:generate
```
### 4. Run npm install
```sh
npm install
```
### 5. Run npm run build
```sh
npm run build
```

### 6. Run migrations
```sh
php artisan migrate
php artisan storage:link
php artisan db:seed
```

### 7. Run dev
```sh
composer run dev  
```

## Development Guidelines

### DDD Conventions

This project follows Domain-Driven Design principles. Key conventions:

1. **Bounded Contexts**: Each context (`IdentityAccess`, `OrganizationalStructure`) is isolated
2. **Layers**: Domain → Application → Infrastructure
3. **Value Objects**: Immutable, validated in constructor
4. **Aggregates**: Root entities that maintain consistency boundaries
5. **Domain Events**: Used for cross-context communication
6. **Outbox Pattern**: Ensures reliable event delivery

### Code Style

- **PHP**: Follows PSR-12 standards (enforced by Laravel Pint)
- **Strict Types**: All PHP files use `declare(strict_types=1);`
- **PHPDoc**: All classes, methods, and properties are documented
- **Tests**: Unit tests for Domain layer, Feature tests for Application/Infrastructure

### Running Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage --min=90

# Run specific test suite
php artisan test tests/Unit/IdentityAccess/
php artisan test tests/Feature/IdentityAccess/
```

### Code Formatting

```bash
# Format code
./vendor/bin/pint

# Format specific directory
./vendor/bin/pint app/IdentityAccess/
```

## API Documentation

API documentation is available at `/api-docs` (when configured).

### Authentication

- **OAuth2**: Token-based authentication using Laravel Passport
- **Endpoints**: `/api/oauth/token`, `/api/oauth/token/validate`, `/api/oauth/token/revoke`

### API Endpoints

- **Users**: `/api/users` (supports both integer ID and UUID)
- **Faculties**: `/api/faculties` (supports both integer ID and UUID)
- **Health Check**: `/api/health`, `/api/health/detailed`

## Architecture Decision Records (ADR)

Architecture decisions are documented in `.ai-knowledge/adr/`:

- **ADR-001**: Shared Kernel Design
- **ADR-002**: Bounded Contexts Structure (to be created)
- **ADR-003**: Event-Driven Architecture (to be created)
- **ADR-004**: Outbox Pattern Implementation (to be created)
- **ADR-005**: Authentication & Authorization Strategy (to be created)
- **ADR-006**: API Design Decisions (to be created)

## Migration Status

This project is in the process of migrating from monolithic Laravel to DDD architecture:

- ✅ **Phase 1**: Shared Kernel migration
- ✅ **Phase 2**: IdentityAccess Domain layer
- ✅ **Phase 3**: IdentityAccess Application & Infrastructure layers
- ✅ **Phase 4**: OrganizationalStructure Domain & Application layers
- ✅ **Phase 5**: API endpoints migration
- ⏳ **Phase 6**: Cleanup & Optimization (in progress)

See `.ai-knowledge/plans/migrate_ddd/` for detailed migration plans.

