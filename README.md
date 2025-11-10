# Adventure Log – Visual Storytelling Platform

A modern, responsive web application for documenting and sharing adventure stories with **Firebase-powered data**, **Publitio media storage**, and **Render deployment**.

---

## 🚀 Features

- 🧱 **Dynamic Page Builder** – Drag-and-drop content editing
- 🖼 **Publitio Cloud Storage** – Fast, secure, and optimized media delivery
- ✍️ **Adventure Story Management** – Create, edit, and share visual stories
- 💾 **Firebase Realtime Database** – Live data sync and cloud persistence
- 💻 **Responsive UI** – Beautiful across desktop, tablet, and mobile
- 🔐 **Authentication** – Firebase Auth integrated with Laravel Sanctum
- ⚡ **Realtime Updates** – Instant content reflection using Firebase listeners
- 🌈 **Customizable Themes** – Multiple color and layout presets

---

## 🧰 Tech Stack

| Layer              | Technology                         |
| ------------------ | ---------------------------------- |
| **Frontend**       | Vue.js 3, Tailwind CSS, Inertia.js |
| **Backend**        | Laravel 11 (PHP 8.2+)              |
| **Database**       | Firebase Realtime Database         |
| **Authentication** | Firebase Auth + Laravel Sanctum    |
| **File Storage**   | Publitio CDN                       |
| **Deployment**     | Render                             |
| **Realtime**       | Firebase listeners                 |

---

## 🏗 System Architecture

Adventure Log is built as a **single-page application (SPA)** using **Vue.js 3** on the frontend, **Laravel 11** on the backend, and **Inertia.js** to connect them seamlessly.

┌─────────────────┐ ┌──────────────────┐ ┌────────────────────┐
│ Client │◄────►│ Laravel │◄────►│ External │
│ (Vue.js 3) │ │ Backend │ │ Services │
│ │ │ │ │ │
│ • Components │ │ • Controllers │ │ • Firebase RTDB │
│ • Pages │ │ • Models │ │ • Publitio CDN │
│ • Store │ │ • Middleware │ │ • Unsplash API │
└─────────────────┘ └──────────────────┘ └────────────────────┘
│
▼
┌──────────────────┐
│ Database │
│ │
│ • Firebase RTDB │
└──────────────────┘

### Tech Stack Overview

| Layer              | Technology                         |
| ------------------ | ---------------------------------- |
| **Frontend**       | Vue.js 3, Tailwind CSS, Inertia.js |
| **Backend**        | Laravel 11 (PHP 8.2+)              |
| **Database**       | Firebase Realtime Database         |
| **Authentication** | Firebase Auth + Laravel Sanctum    |
| **File Storage**   | Publitio CDN                       |
| **Deployment**     | Render                             |
| **Realtime**       | Firebase listeners                 |

---

### How it Works

1. **Client (Vue + Inertia)**
   - Handles page navigation without full reloads
   - Sends requests to Laravel backend via Inertia
   - Manages reactive state (page content, UI, editing state)

2. **Backend (Laravel)**
   - Handles API endpoints, authentication, and business logic
   - Integrates with **Firebase** for data and **Publitio** for media storage
   - Returns Inertia responses to render frontend pages

3. **External Services**
   - **Firebase RTDB**: Stores page content, user data, and app state
   - **Publitio CDN**: Stores images, videos, and media assets
   - **Render**: Hosts the Laravel app and serves it to the web

## 🧩 Key Components

### 1. Editor Component

- **Purpose**: Core interface for content creation and editing
- **Features**:
  - Real-time content editing
  - Section management (hero, mission, gallery, etc.)
  - Image upload via **Publitio**
  - Live preview
- **State Management**: Vue 3 Composition API (reactive)

### 2. Gallery Component

- **Purpose**: Manage and display image galleries
- **Features**:
  - Responsive grid layout
  - Image preview modal
  - Pagination & lazy loading
  - Edit mode controls

### 3. Page Components

- **Dynamic Rendering**: Content loaded from **Firebase**
- **Theming**: CSS variables allow dynamic theme switching
- **Responsive Design**: Mobile-first approach

---

## ⚙️ State Management

```javascript
// Reactive state structure
{
  pageContent: {
    title: String,
    sections: {
      hero: Object,
      features: Array,
      mission: Object,
      recent: Array
    },
    images: Array
  },
  editingSection: String,
  uiState: {
    mobileMenuOpen: Boolean,
    showImagePreview: Boolean,
    currentPage: Number
  }
}

⚙️ Installation & Setup
🧾 Prerequisites

PHP ≥ 8.2

Composer

Node.js ≥ 18

NPM or Yarn

Firebase project

Publitio account

Render account

🧩 Steps
# 1️⃣ Clone the repository
git clone https://github.com/your-org/adventure-log.git
cd adventure-log

# 2️⃣ Install backend dependencies
composer install

# 3️⃣ Install frontend dependencies
npm install

# 4️⃣ Copy the environment configuration
cp .env.example .env

# 5️⃣ Generate the Laravel app key
php artisan key:generate

# 6️⃣ Build frontend assets
npm run dev   # Development
# or
npm run build # Production

# 7️⃣ Start Laravel server
php artisan serve

```
