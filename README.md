# Accounting Laravel

Canadian real estate brokerage accounting dashboard built with Laravel 13, Blade, Bootstrap, and MySQL.

## Requirements

- PHP 8.2+
- MySQL 8+
- Composer

## Setup

```bash
# Create database
mysql -uroot -e "
  CREATE DATABASE IF NOT EXISTS accounting_laravel;
  CREATE USER IF NOT EXISTS 'accounting_laravel'@'localhost';
  GRANT ALL PRIVILEGES ON accounting_laravel.* TO 'accounting_laravel'@'localhost';
  FLUSH PRIVILEGES;
"

# Install dependencies (if not already installed)
php composer.phar install

# Configure environment — copy .env.example or use existing .env
# DB_DATABASE=accounting_laravel
# DB_USERNAME=accounting_laravel
# DB_PASSWORD=

# Migrate and seed
php artisan migrate:fresh --seed

# Run development server
php artisan serve
```

Visit `http://127.0.0.1:8000/login`

**Default credentials:** `admin` / `admin`

## Features

- Accounting dashboard with financial metrics
- Chart of Accounts (40 seeded accounts for Canadian brokerages)
- Manual journal entries with approval, reversal, and audit trail
- 15 transaction templates (invoice, expense, payment, commission, mortgage, etc.)
- Invoices, expenses, and payments
- Financial reports (Balance Sheet, P&L, Trial Balance, General Ledger, etc.)
- Mortgage, rental, and commission calculators
- Clients, agents, and properties
- Role-based access (admin, manager, accountant)
- English/French translations

## Tests

```bash
php artisan test
```

## Documentation

See the `docs/` folder:

- [playbook.md](docs/playbook.md)
- [accounting-design.md](docs/accounting-design.md)
- [roles-and-permissions.md](docs/roles-and-permissions.md)
- [test-plan.md](docs/test-plan.md)
