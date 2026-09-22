# Deploying Drop N Share

Two sites, one repo:

| Site | Domain | Document root | PHP |
|---|---|---|---|
| Frontend (Vue SPA) | `dropnshare.arhamnatiq.com` (single **e**) | `/www/wwwroot/Drop-and-Share/frontend/dist` | not needed |
| Backend (Laravel API) | `dropnsharee.arhamnatiq.com` (double **e**) | `/www/wwwroot/Drop-and-Share/backend/public` | 8.4 |

The frontend calls the backend at `https://dropnsharee.arhamnatiq.com/api`
(compiled in from `frontend/.env.production`).

---

## 1. Create the two sites in the panel

**Frontend** — create the site with root
`/www/wwwroot/Drop-and-Share/frontend/dist`, PHP version **static / none**.
Then add the SPA fallback from [`nginx-frontend.conf`](nginx-frontend.conf).

Without the fallback, a shared link like `/download/<uuid>.zip` 404s, because
only `/` exists as a real file.

**Backend** — create the site with root
`/www/wwwroot/Drop-and-Share/backend`, PHP version **8.4**, then set the
site's **Run directory** to `/public`. Apply the settings in
[`nginx-backend.conf`](nginx-backend.conf) — in particular
`client_max_body_size 550M`, or large uploads die with a 413 before PHP runs.

Issue SSL for both (Let's Encrypt in the panel). The frontend is served over
HTTPS, so the API must be HTTPS too or the browser blocks the request as
mixed content.

## 2. Upload limits

`backend/.user.ini` (and a copy in `backend/public/.user.ini`) raises PHP's
limits to match what the app advertises:

```
upload_max_filesize=120M
post_max_size=550M
memory_limit=512M
max_execution_time=600
max_file_uploads=60
```

The app's own limits live in `backend/.env`:

```
UPLOAD_MAX_FILE_SIZE_MB=100
UPLOAD_MAX_TOTAL_SIZE_MB=500
UPLOAD_MAX_FILES=50
SHARE_EXPIRY_HOURS=24
```

Keep the `.env` numbers **at or below** the PHP numbers, and
`client_max_body_size` in nginx at or above `post_max_size`.

## 3. Cron: expire old shares

Share links die after `SHARE_EXPIRY_HOURS`. The archives on disk are removed
by the scheduler, so add one cron entry:

```
* * * * * cd /www/wwwroot/Drop-and-Share/backend && php artisan schedule:run >> /dev/null 2>&1
```

Run it manually any time with `php artisan shares:prune`
(add `--dry-run` to see what it would delete).

## 4. Redeploying after a `git pull`

```bash
cd /www/wwwroot/Drop-and-Share
./deploy.sh
```

That reinstalls both dependency trees, runs migrations, rebuilds the SPA and
resets ownership to `www:www`.

## 5. Moving from SQLite to MySQL

The schema is portable; only `.env` changes.

```bash
# 1. Create the database and user in the panel, then in backend/.env:
#      DB_CONNECTION=mysql
#      DB_HOST=127.0.0.1
#      DB_PORT=3306
#      DB_DATABASE=dropnshare
#      DB_USERNAME=dropnshare
#      DB_PASSWORD=...
#    (comment out the two SQLite lines)

cd /www/wwwroot/Drop-and-Share/backend
php artisan config:clear
php artisan migrate --force
```

To carry existing rows across, dump them from SQLite first:

```bash
sqlite3 database/database.sqlite .dump > /tmp/dropnshare-sqlite.sql
```

`shares` and `share_files` are the only tables holding real data; the rest
(`cache`, `jobs`, `sessions`, `users`) are framework scaffolding and can be
left to the fresh migration.

## API

| Method | Path | Purpose |
|---|---|---|
| `GET` | `/api/health` | liveness check |
| `POST` | `/api/upload` | multipart `files[]`, returns `share_url` + `download_url` |
| `GET` | `/api/share/{uuid}.zip` | share metadata (file list, size, expiry) |
| `GET` | `/api/download/{uuid}.zip` | streams the archive |

`/api/upload` is throttled to 20 requests/minute per IP, `/api/download` to 60.
