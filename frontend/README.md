# Ticketeer Frontend

The frontend for Ticketeer is a Single Page Application (SPA) built with Vue 3, Vite, and Tailwind CSS.

## Requirements

- Node.js >= 16
- NPM

## Installation

1.  **Install Dependencies**:
    ```bash
    npm install
    ```

2.  **Environment Setup**:
    Copy the example environment file:
    ```bash
    cp .env.example .env
    ```
    
    Update `.env` with your backend URL and Stripe key:
    ```env
    VITE_API_BASE_URL=http://localhost:8000/api
    VITE_STRIPE_KEY=your_stripe_public_key
    ```

## Running Development Server

Start the Vite development server:

```bash
npm run dev
```

The application will be available at `http://localhost:5173`.

## Building for Production

Build the application for production:

```bash
npm run build
```

The build artifacts will be stored in the `dist/` directory.

## Project Structure

- **src/views/**: Page components (Home, Events, Checkout, etc.)
- **src/components/**: Reusable UI components
- **src/stores/**: Pinia state management stores
- **src/api/**: Axios client and API service modules
- **src/router/**: Vue Router configuration
