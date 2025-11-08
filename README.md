# 🌍 Website Builder Platform

A modern website builder for adventure bloggers built with **Laravel**, **Vue.js**, **Inertia.js**, and **Firebase**.

---

## 🚀 Features

-   Laravel 11 backend (API + Inertia server)
-   Vue 3 frontend using Inertia.js
-   Firebase integration for authentication and storage
-   Fully reactive single-page experience
-   Ready for deployment on Render or similar platforms

---

## 🧰 Requirements

-   PHP >= 8.2
-   Composer
-   Node.js >= 18
-   NPM or Yarn
-   Firebase project (for auth & storage)

---

## ⚙️ Quick Start (Local Development)

```bash
# Clone the repository
git clone <your-repo-url>
cd <project-folder>

# Install backend dependencies
composer install

# Install frontend dependencies
npm install

# Copy the example environment file
cp .env.example .env

# Generate Laravel app key
php artisan key:generate


# Build frontend assets
npm run dev   # for development
# or
npm run build # for production

# Start Laravel server
php artisan serve
```

Live URL: https://your-app-name.onrender.com
