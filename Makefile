# Makefile for Docker Compose

# Variables
DOCKER_COMPOSE = docker compose -f docker-compose.local.yml
EXEC_PHP = $(DOCKER_COMPOSE) exec app
EXEC_DB = $(DOCKER_COMPOSE) exec db

# Start all containers
up:
	@$(DOCKER_COMPOSE) up -d
	@$(EXEC_PHP) composer install

# Stop and remove all containers
down:
	@$(DOCKER_COMPOSE) down

# Run migrations
migrate:
	@$(EXEC_PHP) php artisan migrate

# Run migrations rollback
migrate-rollback:
	@$(EXEC_PHP) php artisan migrate:rollback

# Refresh migrations (rollback and re-run)
migrate-refresh:
	@$(EXEC_PHP) php artisan migrate:refresh

# Run database seeders
seed:
	@$(EXEC_PHP) php artisan db:seed

# Run a specific seeder class
seed-class:
	@$(EXEC_PHP) php artisan db:seed --class=$(class)

# Create a new controller
controller:
	@$(EXEC_PHP) php artisan make:controller $(name)
	# @$(EXEC_PHP) chmod 666 /var/www/app/Http/Controllers/$(name).php

# Create a new model
model:
	@$(EXEC_PHP) php artisan make:model $(name)
	# @$(EXEC_PHP) chmod 666 /var/www/app/Models/$(name).php

# Create a new job
job:
	@$(EXEC_PHP) php artisan make:job $(name)
	# @$(EXEC_PHP) chmod 666 /var/www/app/Jobs/$(name).php

# Create a new migration
migration:
	@$(EXEC_PHP) php artisan make:migration $(name)
	# @$(EXEC_PHP) chmod 666 /var/www/database/migrations/*.php

# Clear application cache
clear-cache:
	@$(EXEC_PHP) php artisan cache:clear
	@$(EXEC_PHP) php artisan config:clear
	@$(EXEC_PHP) php artisan route:clear
	@$(EXEC_PHP) php artisan view:clear
# Generate application key
genkey:
	@$(EXEC_PHP) php artisan key:generate

# Get logs from a specific service (e.g., make logs service=app)
logs:
	@if [ -z "$(service)" ]; then \
		echo "Usage: make logs service=<service_name>"; \
	else \
		$(DOCKER_COMPOSE) logs -f $(service); \
	fi

# Tail logs from a specific service (default tail=100)
logs-tail:
	@if [ -z "$(service)" ]; then \
		echo "Usage: make logs-tail service=<service_name>"; \
	else \
		$(DOCKER_COMPOSE) logs -f --tail=100 $(service); \
	fi

# Generate IDE helper files
ide-helper:
	@$(EXEC_PHP) php artisan ide-helper:models -RW
