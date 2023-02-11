pull:
	@echo "Pulling everything..."
	@git pull
	@docker-compose exec php composer install
	$(MAKE) migrate
	@echo "Everything pulled."

migration:
	@echo "Creating migration..."
	@docker-compose exec php php bin/console make:migration
	@echo "Migration created."

migrate:
	@echo "Migrating..."
	@docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	@echo "Migration done."

db:
	@echo "Creating database..."
	@docker-compose exec php php bin/console doctrine:database:create --if-not-exists
	@echo "Database created."

seed:
	@echo "Loading fixtures..."
	@docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
	@echo "Fixtures loaded."

reset:
	@echo "Resetting database..."
	@docker-compose exec php php bin/console doctrine:database:drop --force
	@docker-compose exec php php bin/console doctrine:database:create

	@docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction
	@echo "Database reset."

install:
	@echo "Installing dependencies..."
	@docker-compose up -d
	@docker-compose exec php composer install
	$(MAKE) db
	$(MAKE) migrate
	$(MAKE) fixtures
	@echo "Dependencies installed."

test:
	@echo "Running tests..."
	@docker-compose exec php bin/phpunit
	@docker-compose exec php composer validate
	@docker-compose exec php bin/console doctrine:schema:validate --skip-sync
	@echo "Tests completed."

clear:
	@echo "Clearing cache..."
	@docker-compose exec php php bin/console cache:clear
	@echo "Cache cleared."

dtc:
	@docker compose exec php bin/console d:d:d --force
	@docker compose exec php bin/console d:d:c
	@docker compose exec php bin/console d:s:u --force
	