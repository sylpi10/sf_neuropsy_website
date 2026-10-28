.PHONY: deploy deploy-test backup build-front

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
	--exclude docke/* \
	--exclude tests/ \
	--exclude scripts/ \
	--exclude public/bundles/ \
	--exclude public/assets/

# Dump gzippé de la base de prod dans ~/backups/psytcc/ sur le serveur (15 derniers conservés).
# Le script est envoyé par ssh : rien à installer côté serveur.
backup:
	ssh $(SERVER_USER)@$(SERVER_HOST) 'bash -s' -- "$(SERVER_PATH)" 15 < scripts/db-backup.sh

# Le backup passe avant tout : s'il échoue, le déploiement s'arrête.
# assets/vendor/ (librairies JS de l'importmap) est envoyé par rsync : installé en local par build-front.
deploy: backup build-front
	rsync -av --itemize-changes ./ $(SERVER_USER)@$(SERVER_HOST):~/$(SERVER_PATH) \
		$(RSYNC_EXCLUDES)

	ssh $(SERVER_USER)@$(SERVER_HOST) "\
		cd $(SERVER_PATH) && \
		composer install --no-dev --optimize-autoloader && \
		php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod && \
		php bin/console cache:clear --env=prod && \
		php bin/console asset-map:compile --env=prod \
	"

# Librairies JS de l'importmap (assets/vendor/) et CSS compilé
build-front:
	php bin/console importmap:install
	npm install --no-audit --no-fund
	npm run sass:build

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
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker compose exec php php bin/console doctrine:fixtures:load

logs:
	docker compose logs -f

sass-install:
	npm install

sass-watch:
	npm run sass:watch

sass-build:
	npm run sass:build
