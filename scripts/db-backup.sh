#!/usr/bin/env bash
# Dump gzippé de la base de prod, lancé par `make backup` (et donc avant chaque `make deploy`).
# Exécuté sur le serveur via `ssh ... 'bash -s' -- <chemin projet> < scripts/db-backup.sh`.
# Les identifiants sont lus dans la config Symfony du serveur (DATABASE_URL de .env / .env.local).
#
# Usage : db-backup.sh <chemin du projet> [nombre de dumps à conserver]

set -euo pipefail

PROJECT_PATH="${1:?chemin du projet manquant}"
KEEP="${2:-15}"
BACKUP_DIR="$HOME/backups/psytcc"

cd "$HOME/$PROJECT_PATH" 2>/dev/null || cd "$PROJECT_PATH"

# Fichier d'identifiants temporaire (chmod 600) : le mot de passe n'apparaît ni dans la ligne de commande ni dans `ps`
CNF="$(mktemp)"
OUT=""
cleanup() {
    rm -f "$CNF"
    # Dump incomplet en cas d'échec : on ne le garde pas
    if [ -n "$OUT" ] && [ "${DONE:-0}" != 1 ]; then rm -f "$OUT"; fi
}
trap cleanup EXIT
chmod 600 "$CNF"

DB_NAME="$(php -r '
    require "vendor/autoload.php";
    (new Symfony\Component\Dotenv\Dotenv())->bootEnv(".env");
    $u = parse_url($_SERVER["DATABASE_URL"] ?? "");
    if (!$u || empty($u["path"])) { fwrite(STDERR, "DATABASE_URL introuvable\n"); exit(1); }
    file_put_contents($argv[1], sprintf(
        "[client]\nuser=\"%s\"\npassword=\"%s\"\nhost=\"%s\"\nport=%d\n",
        urldecode($u["user"] ?? ""),
        addcslashes(urldecode($u["pass"] ?? ""), "\\\""),
        $u["host"] ?? "localhost",
        $u["port"] ?? 3306,
    ));
    echo ltrim($u["path"], "/");
' "$CNF")"

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"
OUT="$BACKUP_DIR/${DB_NAME}_$(date +%Y-%m-%d_%H%M%S).sql.gz"

# mariadb-dump sur MariaDB récent (mysqldump y est déprécié), mysqldump sinon
DUMP="$(command -v mariadb-dump || command -v mysqldump)"

# --no-tablespaces : évite l'erreur de privilège PROCESS sur les hébergements mutualisés
"$DUMP" --defaults-extra-file="$CNF" --single-transaction --no-tablespaces --routines --triggers "$DB_NAME" | gzip > "$OUT"
DONE=1
chmod 600 "$OUT"

echo "Backup OK : $OUT ($(du -h "$OUT" | cut -f1))"

# Rotation : on ne garde que les $KEEP derniers dumps
ls -1t "$BACKUP_DIR"/*.sql.gz 2>/dev/null | tail -n +"$((KEEP + 1))" | xargs -r rm -f
