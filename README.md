# neuropsy

site web, symfony, docker + make

## Requirements

- Docker
- Docker Compose

## Installation

```bash
git clone <repo>
cd <<project_dir>>
docker compose up -d --build

docker compose exec php composer install

docker compose exec php php bin/console doctrine:migrations:migrate

#optionnel : charger les fixtures
docker compose exec php php bin/console doctrine:fixtures:load
```
