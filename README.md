# 📁 Drop N Share – Effortless File Sharing via One Link

**🔗 Live Demo:** [dropnshare.arhamnatiq.com](https://dropnshare.arhamnatiq.com)  
**📂 GitHub Repository:** [github.com/arham-natiq25/DropNShare](https://github.com/arham-natiq25/DropNShare)

---

## 📦 Overview

**Drop N Share** is a lightweight and efficient file-sharing tool built with **Laravel (backend)** and **Vue 3 (frontend)**. It allows users to upload multiple files at once, generates a unique shareable link, and enables downloading all files in a compressed **zip folder**.

It’s designed to provide a seamless and clean experience — perfect for quickly sharing large numbers of files without any complexity.

---

## ✨ Features

- 📁 Upload multiple files in one go
- 🔗 Get a single download link instantly
- 🗜️ Files downloaded as a ZIP archive
- ⏳ Optional auto-expiry for links (future scope)
- 📱 Responsive and minimal UI with Vue 3
- 🧰 Backend powered by Laravel file system and ZipArchive

---

## 🛠 Tech Stack

| Layer        | Technology               |
|--------------|---------------------------|
| **Backend**  | Laravel 10+               |
| **Frontend** | Vue 3 + Vite              |
| **Styling**  | Tailwind CSS              |
| **Zip Handling** | Laravel + PHP ZipArchive |
| **Database** | MySQL / SQLite            |

---

## 📸 Screenshots

![Upload Page -Dark](https://github.com/user-attachments/assets/c57769d0-498f-4fdb-b4e4-8a8bb655561f)
![Upload Page -Light](https://github.com/user-attachments/assets/ea1371ea-a17a-402c-b41a-83aee1d3711e)
![Download Page -Dark](https://github.com/user-attachments/assets/1e388a1a-fac9-45a9-bbbf-c31b2d9ef3fc)
![Download Page-Light](https://github.com/user-attachments/assets/e33015d2-fb18-4d6e-93d9-af8d50cbcfa6)



---

## Getting Started

### Backend (Laravel 11, PHP 8.4)

```bash
cd backend

cp .env.example .env
composer install
php artisan key:generate

# SQLite: create the file, then point DB_DATABASE at its absolute path in .env
touch database/database.sqlite
php artisan migrate
php artisan storage:link

php artisan serve          # http://localhost:8000
```

Set these in `backend/.env`:

| Variable | What it does |
|---|---|
| `FRONTEND_URL` | Public URL of the SPA. Builds the shareable link and whitelists CORS. |
| `UPLOAD_MAX_FILE_SIZE_MB` | Per-file cap (default 100). |
| `UPLOAD_MAX_TOTAL_SIZE_MB` | Per-upload cap (default 500). |
| `UPLOAD_MAX_FILES` | Files per upload (default 50). |
| `SHARE_EXPIRY_HOURS` | How long a link lives (default 24). |

These must stay at or below PHP's own `upload_max_filesize` / `post_max_size`.

### Frontend (Vue 3 + Vite)

```bash
cd frontend

cp .env.example .env       # set VITE_API_BASE_URL to your API, including /api
npm install
npm run dev                # http://localhost:5173
```

`npm run build` emits `frontend/dist`, using `.env.production`.

### Expiring old shares

Links stop working after `SHARE_EXPIRY_HOURS` and the archives are deleted by
a scheduled command. In production add:

```
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

Or clean up by hand: `php artisan shares:prune` (`--dry-run` to preview).

---

## API

| Method | Path | Purpose |
|---|---|---|
| `GET` | `/api/health` | Liveness check. |
| `POST` | `/api/upload` | Multipart `files[]`. Returns `share_url`, `download_url`, `expires_at`. |
| `GET` | `/api/share/{uuid}.zip` | Share metadata: file list, total size, expiry, download count. |
| `GET` | `/api/download/{uuid}.zip` | Streams the zip. |

Uploads are throttled to 20/min per IP, downloads to 60/min.

Archives live in `backend/storage/app/private/zips` — outside the document
root, so they can only be reached through the API, which enforces expiry and
counts downloads.

---

## Deployment

See [`deploy/README.md`](deploy/README.md) for the two-site nginx setup,
upload limits, cron, and the SQLite → MySQL switch. `./deploy.sh` rebuilds
everything after a `git pull`.
