# Heartbeat Lab

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel Version">
    <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php">
    <img src="https://img.shields.io/badge/Type%20Coverage-100%25-success?style=for-the-badge">
</p>

Real-time website monitoring built with Laravel, Livewire, and Filament. Track website uptime, response times, and health with automated checks and instant notifications.

## Features

- **Real-time Monitoring** - Track website status with automatic health checks
- **Dashboard** - Beautiful overview of all your monitored sites with uptime stats
- **Site Management** - Full CRUD operations for adding, editing, and removing sites
- **Scheduled Checks** - Automated monitoring via Laravel scheduler
- **Admin Panel** - Filament-powered admin dashboard for user and site management
- **User Authentication** - Secure registration and login with role-based access

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Livewire 4, Tailwind CSS 4
- **Admin Panel**: Filament 5
- **Real-time**: Laravel Reverb
- **Testing**: Pest PHP (100% type coverage)
- **Code Quality**: PHPStan, Rector, Pint

## Screenshots

> Dashboard showing site overview with uptime percentages and status indicators.

> Admin panel for managing users and sites through Filament.

## Installation

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 20+
- Database (MySQL, PostgreSQL, or SQLite)

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
   # Edit .env with your database credentials
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

7. **Create an admin user**
   ```bash
   php artisan create:admin
   ```

### Running the Application

```bash
# Development server with all services
composer run dev

# Or run services separately:
php artisan serve
npm run dev
php artisan queue:listen --tries=1
php artisan reverb:start
```

The application will be available at `http://localhost:8000`.

## Available Commands

```bash
# Linting (Pint + Rector)
composer run lint

# Type checking (PHPStan)
composer run test:types

# Type coverage (must be 100%)
composer run test:type-coverage

# Unit tests
composer run test:unit

# All tests (lint, refactor, types, coverage, unit)
composer run test

# Run specific test file
./vendor/bin/pest tests/Unit/Models/SiteTest.php
```

## Project Structure

```
heartbeat-lab/
├── app/
│   ├── Actions/           # Action classes for business logic
│   ├── Console/           # Artisan commands
│   ├── Events/            # Event classes
│   ├── Exceptions/        # Custom exceptions
│   ├── Filament/          # Admin panel resources
│   ├── Http/              # Middleware and controllers
│   ├── Jobs/              # Queue jobs
│   ├── Listeners/         # Event listeners
│   ├── Livewire/          # Livewire components
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── tests/
│   ├── Feature/           # Feature/browser tests
│   └── Unit/              # Unit tests
└── resources/
    └── views/             # Blade templates
```

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