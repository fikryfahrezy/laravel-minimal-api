# Laravel Minimal API

This project has been trimmed down from the default Laravel skeleton to an API-only baseline, then extended with a versioned todo API, Sanctum token authentication, and Swagger documentation.

## What Changed

- Web routes and the default Blade welcome page were removed.
- Frontend build tooling for Vite, Tailwind, CSS, and JavaScript assets was removed.
- Application routing now loads only `routes/api.php`, plus the default console and health endpoints.
- Opinionated starter migrations and the default `User` model scaffold were removed.
- Optional auth, mail, and third-party service config files were removed.
- Filesystem, cache, and queue config were reduced to local-only minimal drivers, and session config was removed.
- Logging config was reduced to a single file logger with an emergency fallback.
- App, cache, database, and filesystem config were trimmed further to keep only the current runtime essentials.
- The database baseline is default to MySQL.
- API versioning, token auth, todo CRUD, and Swagger docs were added back on a deliberate service/repository architecture.
- Todo listing now supports pagination, filtering, and sorting, and API responses use Laravel resources instead of returning raw models.
- Composer scripts no longer depend on Node.js.

## Available Endpoints

- `GET /api` returns basic application metadata.
- `GET /up` returns Laravel's built-in health check response.

## API Structure

- Versioned base path: `POST /api/v1/...`
- Authentication: Sanctum bearer tokens
- Architecture: controller -> service -> repository

### Auth

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/logout`

### Todos

- `GET /api/v1/todos`
- `POST /api/v1/todos`
- `GET /api/v1/todos/{id}`
- `PUT /api/v1/todos/{id}`
- `DELETE /api/v1/todos/{id}`

Todo list query parameters:

- `search`
- `is_completed`
- `sort_by` with `created_at`, `title`, or `is_completed`
- `sort_direction` with `asc` or `desc`
- `per_page`

## Swagger

- Swagger UI: `GET /api/documentation`
- Generate docs: `composer docs`

## Local Development

Create a MySQL database matching `DB_DATABASE` in `.env`, then run:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer docs
composer run dev
```

## Docker

The repository includes a `Dockerfile` and `compose.yaml` for running the API container only.

Start the stack:

```bash
docker compose up --build
```

What it does:

- starts the API at `http://localhost:8000`
- runs `composer install` and generates `APP_KEY` if needed on container startup

Database note:

- no database container is included
- if you need MySQL, point `.env` to your existing database
- run migrations manually after your database is available

Useful commands:

```bash
docker compose up --build -d
docker compose exec app php artisan migrate
docker compose exec app php artisan test
docker compose exec app composer docs
docker compose down
```

## Testing

```bash
composer test
```

## PHP Quality Checks

PHP already has a built-in syntax linter via `php -l`, and this project already includes Laravel Pint as the formatter.

Available commands:

```bash
composer lint
composer format
composer format:check
```

This repository also includes a ready-to-use pre-commit hook at `.githooks/pre-commit` that:

- runs `php -l` against staged PHP files
- runs `vendor/bin/pint --test` against staged PHP files

To enable it in a Git repository:

```bash
chmod +x .githooks/pre-commit
git config core.hooksPath .githooks
```

## GitHub Actions

The repository includes a CI workflow at `.github/workflows/ci.yml` that runs on pushes and pull requests.

It covers:

- `composer lint`
- `composer format:check`
- `composer test`
