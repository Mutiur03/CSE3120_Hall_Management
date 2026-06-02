# Hall Management

Laravel 12 hall-management application with a Vite frontend. This repository currently uses SQLite for local development, so no MySQL or PostgreSQL server is required.

## Tech Stack

- PHP `^8.2`
- Laravel `^12.0`
- SQLite with PHP `pdo_sqlite`
- Node.js and npm
- Vite `^7.0`
- Tailwind CSS `^4.0`

## Prerequisites

Install these tools before setup:

- [Git](https://git-scm.com/)
- [PHP](https://www.php.net/downloads.php) `8.2` or newer
- [Composer](https://getcomposer.org/download/)
- [Node.js](https://nodejs.org/) with npm

Confirm installations:

```bash
git --version
php --version
composer --version
node --version
npm --version
```

Confirm PHP SQLite support:

```bash
php -m
```

The output must include `pdo_sqlite`. If it is missing, enable the `pdo_sqlite` extension in your active `php.ini`.

## Fresh Clone Setup

### 1. Clone repository

```bash
git clone <repository-url>
cd Hall_Management
```

Replace `<repository-url>` with this repository's Git URL.

### 2. Create local SQLite database

The database file is intentionally ignored by Git. Create it before running migrations.

Windows PowerShell:

```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

macOS or Linux:

```bash
touch database/database.sqlite
```

### 3. Run automated setup

```bash
composer run setup
```

This command:

1. Installs Composer dependencies.
2. Copies `.env.example` to `.env` when `.env` does not exist.
3. Generates `APP_KEY`.
4. Runs database migrations.
5. Installs npm dependencies.
6. Builds frontend assets.

### 4. Start local development

```bash
composer run dev
```

Open [http://localhost:8000](http://localhost:8000).

The development command starts:

- Laravel app server
- Queue listener
- Vite development server

Stop all processes with `Ctrl+C`.

On macOS or Linux with PHP `pcntl`, include Laravel Pail log viewer:

```bash
composer run dev:pail
```

## Manual Setup

Use these commands when you need to run each step separately:

Windows PowerShell:

```powershell
composer install
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
php artisan key:generate
php artisan migrate
npm install
npm run build
```

macOS or Linux:

```bash
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Then start development:

```bash
composer run dev
```

## Environment Configuration

Local configuration lives in `.env`. Do not commit this file.

Default local database settings from `.env.example`:

```dotenv
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Database-backed sessions, queues, and cache work after migrations run.

## Useful Commands

Run tests:

```bash
composer run test
```

Run migrations:

```bash
php artisan migrate
```

Reset database and run migrations again:

```bash
php artisan migrate:fresh
```

Reset database and load seed data:

```bash
php artisan migrate:fresh --seed
```

Build frontend assets:

```bash
npm run build
```

Run only Vite:

```bash
npm run dev
```

Clear Laravel caches:

```bash
php artisan optimize:clear
```

## Seed Data

Run:

```bash
php artisan db:seed
```

Current seeder creates one development user:

```text
Name: Test User
Email: test@example.com
```

## Project Structure

```text
app/                 Application code
config/              Laravel configuration
database/migrations/ Database schema
database/seeders/    Development seed data
resources/           Blade views, CSS, and JavaScript
routes/              Web and console routes
tests/               PHPUnit tests
```

## Troubleshooting

### SQLite driver missing

Error may mention `could not find driver`. Enable `pdo_sqlite` in `php.ini`, then restart your terminal or PHP service.

### SQLite database file missing

Error may mention `database/database.sqlite` does not exist. Create the file:

```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

On macOS or Linux, use `touch database/database.sqlite`.

### Application key missing

Run:

```bash
php artisan key:generate
```

### Frontend assets missing

Error may mention `'vite' is not recognized as an internal or external command`. Install npm dependencies:

Run:

```bash
npm install
npm run build
```

### Pail fails on Windows

Native Windows PHP does not provide the `pcntl` extension required by Laravel Pail. Use:

```bash
composer run dev
```

Use `composer run dev:pail` only on systems with PHP `pcntl`.

### Port 8000 already in use

Run Laravel on another port:

```bash
php artisan serve --port=8001
```
