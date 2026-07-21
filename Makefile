.PHONY: deploy

ifneq (,$(wildcard .env.deploy))
include .env.deploy
export
endif

ifneq (,$(wildcard .env.deploy))
include .env.deploy
export
endif

RSYNC_EXCLUDES = \
	--exclude node_modules/ \
	--exclude vendor/ \
	--exclude .git/ \
	--exclude .env \
	--exclude .env.local \
	--exclude .env.deploy \
	--exclude var/ \
	--exclude Makefile \
	--exclude docke/*
	--exclude tests/ \
	--exclude public/bundles/

deploy:
	rsync -av --itemize-changes ./ $(SERVER_USER)@$(SERVER_HOST):~/$(SERVER_PATH) \
		$(RSYNC_EXCLUDES)

	ssh $(SERVER_USER)@$(SERVER_HOST) "\
		cd $(SERVER_PATH) && \
		composer install --no-dev --optimize-autoloader && \
		php bin/console cache:clear --env=prod \
	"

deploy-test:
	rsync -av --itemize-changes --dry-run ./ $(SERVER_USER)@$(SERVER_HOST):~/$(SERVER_PATH) \
		$(RSYNC_EXCLUDES)


up:
	docker compose up -d

up build:
	docker compose up -d --build

down:
	docker compose down

bash:
	docker compose exec php bash

cc:
	docker compose exec php php bin/console cache:clear

composer i:
	docker compose exec php composer install

migrations:
	docker compose exec php php bin/console doctrine:migrations:migrate

fixtures:
    docker compose exec php php bin/console doctrine:fixtures:load

logs:
	docker compose logs -f
