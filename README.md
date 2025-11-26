# Ticketeer

Ticketeer is a modern event ticketing platform built with a Laravel backend and a Vue.js frontend. It allows organizers to manage events and sell tickets, and buyers to browse events, purchase tickets via Stripe, and manage their bookings.

## Project Structure

- **backend/**: Laravel API handling business logic, database interactions, and authentication.
- **frontend/**: Vue.js Single Page Application (SPA) for the user interface.

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.1
- **Composer**
- **Node.js** >= 16
- **NPM**
- **SQLite** (or another database driver if configured)

## Quick Start

### 1. Setup Backend

Navigate to the backend directory and set up the Laravel application:

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
```

### 2. Setup Frontend

Navigate to the frontend directory and set up the Vue application:

```bash
cd frontend
cp .env.example .env
npm install
```

### 3. Run the Application

You need to run both the backend and frontend servers simultaneously.

**Backend (Terminal 1):**
```bash
cd backend
php artisan serve
```

**Frontend (Terminal 2):**
```bash
cd frontend
npm run dev
```

Access the application at `http://localhost:5173`.

## Features

- **Organizers**: Create companies, manage events, create ticket types, scan tickets.
- **Buyers**: Browse events, add tickets to cart, secure checkout with Stripe, view purchased tickets.
- **Payments**: Integrated Stripe payment flow with verification.