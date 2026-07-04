# DocsBuilder AI

A documentation builder platform built with **Laravel 12**, **Inertia.js v2**, **Vue 3**, and **Tailwind CSS v4**.

---

## 🔒 License Notice

**PROPRIETARY & CONFIDENTIAL**

Copyright (c) 2024-2026 Manan Monani. All Rights Reserved.

This repository is **NOT** open-source. Unauthorized copying, distribution, modification, forking, or contribution of this project, via any medium, is strictly prohibited. See the [LICENSE](LICENSE) file for complete details.

---

## 🚀 How to Run the Application

### 1. Prerequisites
Ensure you have the following installed on your machine:
- **PHP 8.2+**
- **Composer 2.x**
- **Node.js 18+ & NPM**

---

### 2. First-Time Setup Instructions

If you are setting up the project for the first time or on a new machine:

```powershell
# 1. Install PHP dependencies
composer install

# 2. Install Frontend dependencies
npm install

# 3. Copy Environment Configuration
cp .env.example .env

# 4. Generate Application Security Key
php artisan key:generate

# 5. Create & Seed Database (SQLite)
# (Creates database/database.sqlite automatically)
php artisan migrate --seed

# 6. Create Storage Symlink
php artisan storage:link
```

---

### 3. Running the App (Daily Development)

To start both the **PHP Laravel server** and the **Vite Frontend HMR server** with a single command:

```powershell
composer run dev
```

> **Note:** If PHP is installed in custom directory (e.g., `C:\php`), include it in your environment path before running:
> ```powershell
> $env:Path = "C:\php;" + $env:Path
> composer run dev
> ```

#### **Application URLs:**
- 🌐 **Web App:** [http://localhost:8000](http://localhost:8000)
- ⚡ **Vite Dev Server:** [http://localhost:5173](http://localhost:5173)

---

## 🔑 Demo Access Credentials

The database seeder prepares default accounts for testing:

| Role | Email | Default Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin@admin.com` | `12345678` |
| **Manager** | `manager@admin.com` | `12345678` |

---

## 🛠 Useful Commands

- **Run Code Formatter (Laravel Pint):**
  ```powershell
  composer lint
  ```
- **Format Frontend Code (Prettier):**
  ```powershell
  npm run format
  ```
- **Lint Frontend Code (ESLint):**
  ```powershell
  npm run lint
  ```
