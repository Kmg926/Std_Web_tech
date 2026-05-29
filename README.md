# SLEEVE

개인 바이닐 아카이브 + 자켓 디자인 포트폴리오

## Tech Stack

- Frontend: HTML, CSS, JS
- Backend: PHP 8.1
- Database: MySQL
- Hosting: InfinityFree (FTP deploy)

## Local Setup

1. Copy `htdocs/config/config.example.php` to `htdocs/config/config.php`
2. Fill in your local DB credentials inside `config.php`
3. Import the schema: `mysql -u root -p sleeve < sql/schema.sql`
4. Import seed data: `mysql -u root -p sleeve < sql/seed.sql`
5. Point your local web server root at `htdocs/`

> `htdocs/config/config.php` is git-ignored — never commit real credentials.

## Deployment (CI/CD)

Pushing to `main` triggers an automatic GitHub Actions pipeline:

1. **Lint** — PHP syntax check (`php -l`) on all `.php` files under `htdocs/`
2. **Deploy** — FTP upload of `htdocs/` to InfinityFree (only runs if lint passes)

### Required GitHub Secrets

Set these in **Settings > Secrets and variables > Actions** before the first deploy:

| Secret | Description |
|---|---|
| `FTP_SERVER` | InfinityFree FTP host (e.g. `ftpupload.net`) |
| `FTP_USERNAME` | FTP username from InfinityFree control panel |
| `FTP_PASSWORD` | FTP password |

### Files excluded from upload

- `.git*` / `.github/`
- `.claude/`
- `sql/`
- `*.md`, `.gitignore`
- `htdocs/config/config.php` (server config is managed manually on InfinityFree)
