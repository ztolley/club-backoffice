# Football Club Backoffice

A online system to help manage a football (soccer) club, primariy manage players, their contact details, health, injuries and player development.

The application is built to support a UI JPL Warrior club but is freely available for any club that wants to use it.

The application is written in PHP so that it can be run on most web hosting platforms. As most clubs use Wordpress for their website and many use cheap shared webhosting that primarily supports PHP the application has been built using PHP in combination with Laravel and Filament. Laravel and Filament are easy to use and allow rapid development of web admin tools with relatively little programming experience.

## License

This applicaion is open-source and licenced under The [MIT license](https://opensource.org/licenses/MIT). Laravel framework is a major piece of this and is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Install Notes

The app uses laravels built in database migration tools. Before the application can be logged into an admin user and basic permissions need to be put in place, so a set of database seed scripts have been created. These install a user 'admin @example.com' with the password 'password123'.

```
php artisan migrate:fresh
php artisan db:seed --class=ShieldSeeder
php artisan db:seed
```

After installation a one off command is required to allow access to server storage when writing and sending emails.

```
php artisan storage:link
```

## Migration notes

In production sync the files across and then use the following to make any database updates

```
php artisan migrate
```

## Development

This project can be run fully in Docker for local development.

1. Start services:
```bash
docker compose up --build
```

2. The app container now bootstraps itself on startup:
- runs `php artisan migrate --force`
- runs `php artisan db:seed --class=ShieldSeeder --force`
- runs `php artisan db:seed --class=DatabaseSeeder --force` if teams/players/applicants are empty
- creates `public/storage` symlink if missing

3. On first run, only install Composer dependencies manually if needed:
```bash
docker compose exec -T app php artisan key:generate
```

4. Open:
- App: `http://localhost:8003`
- Vite dev server (HMR): `http://localhost:5173`

Code changes in the project directory are mounted directly into containers, so PHP and Blade changes are visible on refresh and frontend assets update through Vite.

## Running tests

Run tests in Docker (recommended), so PHP version and extensions match the app runtime.

```bash
docker compose exec app php artisan test
```

Useful test commands:

```bash
docker compose exec app php artisan test --filter=AdminPanelSmokeTest
docker compose exec app php artisan test tests/Feature/AdminPanelSmokeTest.php
docker compose exec app php artisan test --parallel
```

For browser tests with the VS Code Playwright extension, use:

```bash
npm run test:e2e
```

## Recommended VS Code extensions

This repo includes extension recommendations in `.vscode/extensions.json`.

- `ms-playwright.playwright` (browser E2E test runner and traces)
- `recca0120.vscode-phpunit` (run `php artisan test` from VS Code Testing UI, in Docker)
- `xdebug.php-debug` (PHP step debugging)
- `bmewburn.vscode-intelephense-client` (PHP IntelliSense)
- `onecentlin.laravel-blade` (Blade syntax support)
- `ms-azuretools.vscode-docker` (Docker/Compose tooling)
- `editorconfig.editorconfig` (consistent formatting rules)

After installing recommended extensions:
- open **Command Palette -> Extensions: Show Recommended Extensions**
- open the **Testing** panel in VS Code to run PHPUnit and Playwright tests

## Automated checks

GitHub Actions is configured to run on every push to `main` and every pull request.

- PHP job:
  - installs Composer dependencies
  - runs migrations against SQLite
  - runs `php artisan test`
  - runs `composer audit`
- Frontend job:
  - installs npm dependencies
  - runs `npm run build`
  - runs `npm audit`
- Production security job:
  - builds a release-like copy of the repo (excluding dev-only folders)
  - runs `composer install --no-dev`
  - runs `composer audit --no-dev`
  - runs Semgrep (`p/security-audit`) against the release artifact

Dependabot is also configured for weekly updates for Composer, npm, and GitHub Actions dependencies.

## Branch deployment (IONOS)

A deployment workflow is included at `.github/workflows/deploy-ionos.yml`.

Current trigger (for testing):
- runs on every push to non-`main` branches
- can also be run manually via `workflow_dispatch`

The workflow:
- installs production Composer dependencies (`--no-dev`)
- builds frontend assets (`npm run build`)
- creates a release-like artifact
- syncs files to your server over SSH/rsync
- runs post-deploy Laravel commands on the server:
  - `php artisan optimize:clear`
  - `php artisan storage:link` (if missing)
  - optional `php artisan migrate --force` when enabled
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`

Required GitHub repository secrets:
- `DEPLOY_SSH_HOST`
- `DEPLOY_SSH_PORT` (optional, defaults to `22`)
- `DEPLOY_SSH_USER`
- `DEPLOY_SSH_PRIVATE_KEY`
- `DEPLOY_PATH` (e.g. `~/club-backoffice`)
- `DEPLOY_RUN_MIGRATIONS` (optional: set to `true` to run migrations during deploy)

Notes:
- keep your server `.env` outside GitHub; this workflow does not upload `.env` files
- document root should remain pointed at `~/club-backoffice/public`

## Security scanning notes

Enlightn currently does not support Laravel 12 in released packages, so this project uses a production-artifact scan in CI as a fallback.
Once Enlightn adds Laravel 12 support, it can be added as an additional scanner.
