# LensLocation

A Laravel-based marketplace that connects **photographers and cinematographers** with **location owners** to book unique shoot locations by the hour.

---

## Table of Contents

- [Overview](#overview)
- [User Roles](#user-roles)
- [Business Logic](#business-logic)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
- [Project Structure](#project-structure)

---

## Overview

LensLocation is a multi-role platform where:

- **Location owners** list their venues (studios, lofts, outdoor spaces, etc.) for rent by the hour.
- **Customers** (photographers, cinematographers) browse approved listings, search and filter by category/city/price, and book locations for their shoots.
- **Admins** oversee the platform — they approve or reject new listings, manage users (block/activate), categorize venues, and receive real-time notifications on platform activity.

## User Roles

| Role       | Description                                                                 |
|------------|-----------------------------------------------------------------------------|
| **Admin**    | Platform administrator. Manages users, approves/rejects listings, manages categories, and monitors notifications. |
| **Owner**    | Venue owner who creates and manages location listings, accepts/rejects bookings, and sets payment details. |
| **Customer** | Photographer/cinematographer who browses locations, books shoots, and manages favorites. |

## Business Logic

### 1. Registration & Authentication

- Users register with **first name, last name, email, phone, password**, and select a role (`owner` or `customer`).
- Phone numbers accept 7–20 digits with an optional leading `+` — local formats (e.g., `03261719539`) are supported.
- **Google OAuth** login is available as an alternative to email/password.
- **Password reset** is handled via email (SMTP) using Laravel's built-in password broker.
- On failed login, accounts marked as **blocked** by an admin are rejected with an informative message.
- New registrations trigger a real-time notification to all admins.

### 2. Listing Lifecycle (Owner → Admin → Customer)

1. An **owner** creates a location listing with a title, description, address, city, category, hourly price, and optional image.
2. The listing enters **pending** status and is invisible to customers.
3. An **admin** reviews the listing and can **approve** or **reject** it.
4. Once **approved**, the listing becomes visible in the customer browse/search interface.

### 3. Booking Workflow (Customer → Owner)

1. A **customer** views an approved listing and books it — specifying a **date/time**, **number of hours**, and optional **shoot type**.
2. The total price is calculated automatically (`price_per_hour × hours`).
3. The booking is created with **pending** status.
4. The **owner** can **accept** or **reject** the booking.
5. A **customer** may cancel their own booking before it is confirmed or completed.
6. Booking statuses: `pending` → `confirmed` → `completed`, or `cancelled` at any point.

### 4. Favorites

- Customers can bookmark listings as **favorites** for quick access.
- Each user can only favorite a listing once (unique constraint on `user_id + location_id`).

### 5. Payment Details

- Owners can store **bank account** (account holder, bank name, account number) and **mobile wallet** (wallet type, wallet number) details in their profile for payouts.

### 6. Notifications (Real-time)

The platform uses **Laravel Reverb** (WebSockets) for real-time push notifications. Admins and owners receive database + broadcast notifications for:

- New user registrations
- New listing submissions
- New booking requests

Users can configure which notification types they receive from their profile settings.

### 7. Directory / Navigation

- `/login` — Landing page; users authenticate here.
- Role-based dashboards redirect on login:
  - **Admin** → `/admin/dashboard`
  - **Owner** → `/owner/listings`
  - **Customer** → `/customer/dashboard`

---

## Tech Stack

| Layer        | Technology                           |
|-------------|--------------------------------------|
| **Backend**  | PHP 8.3, Laravel 13                  |
| **Database** | MySQL (`location_db`)                |
| **Frontend** | Blade templates, Tailwind CSS 4, Vite 8 |
| **WebSockets** | Laravel Reverb (self-hosted)       |
| **OAuth**    | Laravel Socialite (Google)           |
| **Mail**     | SMTP (for password resets)           |
| **Dev tools** | Composer, npm, PHPUnit             |

---

## Getting Started

### Prerequisites

- PHP 8.3+
- Composer
- Node.js 18+
- MySQL 8.0+

### Installation

```bash
# Clone the repository
git clone <repo-url> lens-location
cd lens-location

# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Edit .env with your database, mail, and Reverb settings
# DB_DATABASE=location_db
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Install and build frontend assets
npm install
npm run build
```

### Development

```bash
# Start all services concurrently (server, queue, logs, Vite)
composer run dev
```

Or run individually:

```bash
php artisan serve           # Laravel dev server (port 8000)
php artisan queue:listen    # Queue worker
npm run dev                 # Vite dev server with HMR
php artisan reverb:start    # WebSocket server (if using notifications)
```

### Escrow payout scheduler

**Nothing runs without a scheduler** - in development run:

```bash
php artisan schedule:work
```

In production add a cron entry:

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled commands:

- `bookings:settle` (hourly) - expires unpaid bookings whose date has passed
  and marks paid ones visited, releasing their escrow for payout.
- `payouts:process` (weekly on Mondays and monthly on the 1st, both at 09:00)
  - batch-transfers eligible escrow to owners' Stripe accounts.

Run them manually:

```bash
php artisan bookings:settle
php artisan payouts:process --period=weekly
php artisan payouts:process --period=monthly
```

Verify the schedule with `php artisan schedule:list`.

### Running Tests

```bash
composer run test
```

---

## Project Structure

```
app/
├── Enums/
│   ├── BookingStatus.php    # pending, confirmed, completed, cancelled
│   ├── ListingStatus.php    # pending, approved, rejected
│   ├── Role.php            # admin, owner, customer
│   └── UserStatus.php      # active, blocked
├── Http/Controllers/
│   ├── Admin/              # Dashboard, users, listings, categories, notifications, profile
│   ├── Customer/           # Dashboard, browse, bookings, favorites, profile
│   ├── Owner/              # Dashboard, locations, bookings, profile, notifications
│   └── AuthController.php  # Registration, login, password reset, Google OAuth
├── Models/
│   ├── User.php            # Multi-role user with favorites & payment
│   ├── Location.php        # Listing owned by a user, belongs to a category
│   ├── Category.php        # Location category (admin-managed)
│   ├── Booking.php         # Customer booking of a location
│   ├── Favorite.php        # Customer bookmark on a location
│   └── Payment.php         # Owner bank/wallet details
└── Notifications/          # Email + database notification classes

database/migrations/         # Schema definitions for all tables
routes/web.php              # All web routes with role-based middleware groups
resources/views/            # Blade templates organized by role (admin/owner/customer)
```

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
