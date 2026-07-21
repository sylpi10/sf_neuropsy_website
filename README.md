# neuropsy hautes alpes

site web, symfony, docker + make

## Requirements

- Make
- Docker
- Docker Compose

## Installation

```bash
git clone <repo>
cd <<project_dir>>

make up build
ou
docker compose up -d --build

make composer i
ou
docker compose exec php composer install

make migrations
ou
docker compose exec php php bin/console doctrine:migrations:migrate

#optionnel : charger les fixtures
# make fixtures
ou
docker compose exec php php bin/console doctrine:fixtures:load
```
