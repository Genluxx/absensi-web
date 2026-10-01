#!/bin/sh
set -eu

if [ "${APP_ENV:-production}" = "production" ]; then
    case "${APP_DEBUG:-false}" in
        false|0|no|NO) ;;
        *) echo "APP_DEBUG must be false in production." >&2; exit 1 ;;
    esac

    : "${APP_KEY:?APP_KEY must be configured in production.}"
    : "${DB_HOST:?DB_HOST must be configured in production.}"
    : "${DB_DATABASE:?DB_DATABASE must be configured in production.}"
    : "${DB_USERNAME:?DB_USERNAME must be configured in production.}"
    : "${DB_PASSWORD:?DB_PASSWORD must be configured in production.}"
    : "${INITIAL_ADMIN_NAME:?INITIAL_ADMIN_NAME must be configured in production.}"
    : "${INITIAL_ADMIN_USERNAME:?INITIAL_ADMIN_USERNAME must be configured in production.}"
    : "${INITIAL_ADMIN_EMAIL:?INITIAL_ADMIN_EMAIL must be configured in production.}"
    : "${INITIAL_ADMIN_PASSWORD:?INITIAL_ADMIN_PASSWORD must be configured in production.}"
fi

attempt=1
until php artisan migrate --force; do
    if [ "$attempt" -ge 10 ]; then
        echo "Database migration failed after 10 attempts; check database configuration and migration logs." >&2
        exit 1
    fi

    echo "Database is not ready; retrying migration ($attempt/10)." >&2
    attempt=$((attempt + 1))
    sleep 3
done

if [ "${APP_ENV:-production}" = "production" ] || [ "${RUN_SEEDERS:-false}" = "true" ]; then
    php artisan db:seed --force
fi

exec apache2-foreground