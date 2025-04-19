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

##  Getting Started

### ⚙️ Backend (Laravel)

```bash
git clone https://github.com/arham-natiq25/DropNShare.git
cd DropNShare/backend

cp .env.example .env
composer install
php artisan key:generate

# Set up DB credentials in .env
php artisan migrate
php artisan storage:link

php artisan serve

---
### ⚙️ Frontend (Vue.js)
``
cd DropNShare/frontend

npm install
npm run dev

