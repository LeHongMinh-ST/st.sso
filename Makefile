# Makefile for Docker Compose

# Variables
DOCKER_COMPOSE = docker compose -f docker-compose.local.yml
EXEC_PHP = $(DOCKER_COMPOSE) exec app
EXEC_DB = $(DOCKER_COMPOSE) exec db

# Start all containers
up:
	@$(DOCKER_COMPOSE) up -d
	@$(DOCKER_COMPOSE) exec app composer install

# Stop and remove all containers
down:
	@$(DOCKER_COMPOSE) down

# Run migrations
migrate:
	@$(DOCKER_COMPOSE) exec app php artisan migrate

# Run database seeders
seed:
	@$(DOCKER_COMPOSE) exec app php artisan db:seed

# Clear application cache
clear-cache:
	@$(DOCKER_COMPOSE) exec app php artisan cache:clear
	@$(DOCKER_COMPOSE) exec app php artisan config:clear
	@$(DOCKER_COMPOSE) exec app php artisan route:clear
	@$(DOCKER_COMPOSE) exec app php artisan view:clear

# Get logs from a specific service (e.g., make logs service=app)
logs:
	@$(DOCKER_COMPOSE) logs -f $(service)

# Tail logs from a specific service (e.g., make logs-tail service=app)
logs-tail:
	@$(DOCKER_COMPOSE) logs -f --tail=100 $(service)

# Tail logs from a specific service (e.g., make logs-tail service=app)
idea-helper:
	@$(DOCKER_COMPOSE) exec app php artisan ide-helper:models -RW
