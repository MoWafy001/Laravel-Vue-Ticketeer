# Ticketeer Backend

The backend for Ticketeer is a RESTful API built with Laravel. It handles authentication, event management, order processing, and Stripe payment integration.

## Requirements

- PHP >= 8.1
- Composer
- SQLite (default)

## Installation

1.  **Clone the repository** (if you haven't already).

2.  **Install Dependencies**:
    ```bash
    composer install
    ```

3.  **Environment Setup**:
    Copy the example environment file:
    ```bash
    cp .env.example .env
    ```
    
    Generate the application key:
    ```bash
    php artisan key:generate
    ```

4.  **Database Setup**:
    Create the SQLite database file:
    ```bash
    touch database/database.sqlite
    ```
    
    Run migrations and seed the database with test data:
    ```bash
    php artisan migrate --seed
    ```

5.  **JWT Secret**:
    Generate the JWT secret for authentication:
    ```bash
    php artisan jwt:secret
    ```

6.  **Stripe Configuration**:
    Add your Stripe keys to `.env`:
    ```env
    STRIPE_KEY=your_stripe_public_key
    STRIPE_SECRET=your_stripe_secret_key
    STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
    ```

## Running the Server

Start the development server:

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`.

## API Routes

### Authentication
- `POST /api/auth/organizer/register`
- `POST /api/auth/organizer/login`
- `POST /api/auth/buyer/register`
- `POST /api/auth/buyer/login`

### Events
- `GET /api/events` - List all events
- `GET /api/events/{id}` - Get event details

### Buying
- `POST /api/orders` - Create an order
- `POST /api/payments/checkout` - Initiate Stripe checkout
- `POST /api/payments/verify` - Verify Stripe payment
- `GET /api/buyer/tickets` - View purchased tickets

## Testing

Run the test suite:

```bash
php artisan test
```
