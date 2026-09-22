# Reservations

A portfolio restaurant table reservation application built as a Laravel monolith with Blade and Livewire.

Guests can browse available tables by date and time. Registered users can create reservations, view their reservation history, and cancel their own reservations.

## Tech Stack

- PHP 8.2+
- Laravel 12
- Livewire 3
- Blade and Tailwind CSS
- MySQL 8
- Laravel Sail
- Vite
- PHPUnit

## Architecture

- Livewire components handle interactive screens and validation while Blade renders the views.
- Services contain reservation, table availability, and user-creation business logic.
- Eloquent models define persistence and relationships between users, reservations, and tables.
- `ReservationPolicy` allows a reservation to be cancelled only by its owner.
- Reservation creation runs inside a database transaction. The selected table rows are locked before availability is checked, preventing concurrent requests from creating conflicting reservations.

## Domain Rules

- Reservations are made for a start date, start time, duration, and one or more tables.
- A table cannot be used by overlapping reservations. Back-to-back reservations are allowed.
- Selected table IDs must exist and be unique within a reservation.
- Users can cancel only their own reservations.
- The reservation-to-table pivot table prevents duplicate table attachments within one reservation.

## Local Setup

Install PHP dependencies and create the environment file:

```bash
./setup.sh
```

Start the application containers, generate the application key, and create the seeded local database:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

Install frontend dependencies and start Vite:

```bash
npm install
npm run dev
```

Open `http://localhost` unless `APP_PORT` changes the port.

## Demo Account

The local database seeder creates the following account:

```text
Email: test@example.com
Password: password
```

Use these credentials only in a local development environment.

## Tests

Run the full test suite through Sail:

```bash
./vendor/bin/sail artisan test
```

The test suite covers service-layer reservation rules, Livewire validation and authentication flows, reservation authorization, and table availability.
