# Heartbeat Lab

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Version">
    <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php">
    <img src="https://img.shields.io/badge/Type%20Coverage-100%25-success?style=for-the-badge">
</p>

Real-time website monitoring built with Laravel, Livewire, and Filament. Track website uptime, response times, SSL certificates, and receive instant notifications when your sites go down or come back online.

## Features

- **Website Monitoring** — Automated HTTP health checks with response time tracking
- **SSL Certificate Monitoring** — Automatic SSL certificate validation and expiry alerts
- **Real-time Dashboard** — Live status updates via Laravel Reverb and Echo
- **Site Management** — Full CRUD for adding, editing, and removing monitored sites
- **Uptime Calculation** — Rolling uptime percentage based on the last 100 checks (Redis-backed)
- **Email Notifications** — Automatic alerts when a site goes down or recovers
- **Public Status Page** — Check any monitored site's status without an account at `/status`
- **Admin Panel** — Filament-powered admin dashboard for managing users and sites
- **User Authentication** — Secure registration and login with role-based access (admin/user)
- **Broadcasting** — Real-time user registration announcements via Reverb

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Livewire 4, Alpine.js, Tailwind CSS 4, Vite
- **Admin Panel**: Filament 5
- **Real-time**: Laravel Reverb, Laravel Echo, Pusher.js
- **Queue**: Redis (predis)
- **Database**: PostgreSQL / MySQL / SQLite
- **Testing**: Pest PHP (100% type coverage)
- **Code Quality**: PHPStan (Larastan), Rector, Pint

## Screenshots

> Dashboard showing site overview with uptime percentages and status indicators.

> Admin panel for managing users and sites through Filament.

> Public status page for checking site health without authentication.

## Installation

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 20+
- Database (PostgreSQL, MySQL, or SQLite)
- Redis (required for queue and uptime tracking)
- Reverb (WebSocket server for real-time updates)

### Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/heartbeat-lab.git
   cd heartbeat-lab
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials and Redis connection
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Install frontend dependencies**
   ```bash
   npm install
   npm run build
   ```

7. **Publish Filament assets**
   ```bash
   php artisan filament:upgrade
   ```

8. **Create an admin user**
   ```bash
   php artisan admin:create
   ```

9. **(Optional) Seed demo data**
   ```bash
   php artisan db:seed
   ```

### Running the Application

```bash
# Start all services (server + queue + Vite + Reverb)
composer run dev

# Or run services individually:
php artisan serve                          # HTTP server
php artisan queue:listen --tries=1         # Queue worker
npm run dev                                # Vite dev server
php artisan reverb:start                   # WebSocket server
```

The application will be available at `http://localhost:8000`.

## Available Commands

### Artisan Commands

| Command | Description |
|---------|-------------|
| `php artisan sites:check` | Dispatch a check job for all monitored sites |
| `php artisan sites:schedule-checks` | Queue periodic checks for all sites (used by scheduler) |
| `php artisan admin:create` | Create a new admin user interactively |

### Scheduler

Add the following to your server's crontab to enable automated monitoring:

```cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Then register the check command in `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('sites:schedule-checks')->everyFiveMinutes();
```

### Code Quality Commands

```bash
# Linting (Pint + Rector)
composer run lint

# Type checking (PHPStan)
composer run test:types

# Type coverage (must be 100%)
composer run test:type-coverage

# Unit tests with coverage
composer run test:unit

# All tests (lint, refactor, types, coverage, unit)
composer run test

# Run specific test file
./vendor/bin/pest tests/Unit/Jobs/CheckSiteJobTest.php
```

## Project Structure

```
heartbeat-lab/
├── app/
│   ├── Actions/              # Action classes for business logic
│   │   ├── Auth/             # Login, registration, logout actions
│   │   └── Sites/            # Create, update, delete site actions
│   ├── Console/Commands/     # Artisan commands
│   ├── Events/               # Event classes (broadcast via Reverb)
│   ├── Exceptions/           # Custom exceptions
│   ├── Filament/Admin/       # Admin panel resources and pages
│   ├── Http/Middleware/      # HTTP middleware
│   ├── Jobs/                 # Queue jobs (site checks, emails)
│   ├── Listeners/            # Event listeners
│   ├── Livewire/             # Livewire components
│   │   ├── Auth/             # Login, register, logout components
│   │   └── Sites/            # Create, update, delete site components
│   ├── Models/               # Eloquent models (User, Site)
│   ├── Notifications/        # Mail notifications (up/down alerts)
│   └── Providers/            # Service providers
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── css/                  # Tailwind CSS entrypoint
│   ├── js/                   # JavaScript (Echo, Axios)
│   └── views/                # Blade templates
│       ├── layouts/          # App and auth layouts
│       └── livewire/         # Livewire component views
├── routes/
│   ├── channels.php          # Broadcasting channel auth
│   ├── console.php           # Scheduled tasks
│   └── web.php               # Web routes
└── tests/
    ├── Feature/              # Feature/integration tests
    └── Unit/                 # Unit tests
```

## Real-time Broadcasting

Heartbeat Lab uses Laravel Reverb for WebSocket broadcasting:

- **User Registered** — Broadcast on the `public-announcements` channel when a new user signs up
- **Site Status Changed** — Broadcast on a private user channel when a monitored site changes status
- **Dashboard Updates** — Livewire components refresh automatically on status change events

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Run tests to ensure everything passes (`composer run test`)
4. Commit your changes (`git commit -m 'Add amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

### Code Standards

- Use **Pint** for code formatting
- Maintain **100% type coverage** in tests
- Pass **PHPStan** analysis at max level
- Follow **Rector** suggestions for modernization

## License

This project is licensed under the [MIT License](LICENSE).
