# CDDP — Centralised Disinformation Data Platform

An internal research platform combining a **Data Room** (document/artifact library) and a **Community Forum** for analysts and researchers.

---

## Quick Deploy

```bash
# 1. Clone and enter the project
git clone <your-repo-url> cddp
cd cddp

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Create the SQLite database and run migrations
touch database/database.sqlite
php artisan migrate --force

# 5. Link storage (for uploaded files)
php artisan storage:link

# 6. Install JS dependencies and build assets
npm install
npm run build

# 7. (Optional) Seed demo data
php artisan db:seed
```

Then start the server:
```bash
php artisan serve
```

---

## User Roles

| Role | Can do |
|---|---|
| **Admin** | Everything + user management, categories, tags, feature artifacts |
| **User** | Upload artifacts, post forum threads/replies, comment |
| **Read-only** | Browse, download, read — no posting |

New registrations default to **read-only**. Admins promote users via `/admin/users`.

---

## Demo Accounts (after seeding)

| Role | Email | Password |
|---|---|---|
| Admin | `admin@demo.test` | `password` |
| User | `researcher@demo.test` | `password` |
| Read-only | `readonly@demo.test` | `password` |

All demo records are prefixed `[TEST]` and can be removed with `php artisan migrate:fresh`.

---

## Tech Stack

- **Laravel 13** + SQLite (zero external DB dependency)
- **Tailwind CSS 3** + **Alpine.js** — minimal, editorial design
- **Vite 5** (Node 18 compatible)

---

## Requirements

- PHP 8.3+
- Node.js 18+
- Composer
