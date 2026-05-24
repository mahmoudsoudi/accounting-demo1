# Accounting Laravel

## What is this for?

**Accounting Laravel** is a full accounting dashboard for **Canadian real estate brokerages**. It gives brokerage staff a single place to record transactions, manage clients and properties, and produce financial reports using proper double-entry bookkeeping.

The goal is to provide a practical, auditable accounting system—not a mockup—tailored to brokerage workflows such as:

- Commission income and agent payouts
- Invoices, expenses, and payments (cash, bank, Stripe, credit card)
- Trust/client funds, rental income, and property management
- Mortgage amortization, taxes, insurance, and maintenance
- Financial statements built from posted journal entries

The app supports **English and French**, with role-based access for **admin**, **manager**, and **accountant** users.

## Tech stack

- Laravel 13
- PHP 8.3+
- MySQL 8+ (recommended) or SQLite for local development
- Blade templates with Bootstrap
- Vite for frontend assets

## Installation

### Requirements

- PHP 8.3+
- Composer
- Node.js and npm (for building assets)
- MySQL 8+ (production / recommended local setup)

### Quick setup

From the project root:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate --seed
php artisan serve
```

Or use the Composer shortcut, which runs the same steps:

```bash
composer setup
```

### MySQL database setup

Create the database and user, then configure `.env`:

```bash
mysql -uroot -e "
  CREATE DATABASE IF NOT EXISTS accounting_laravel;
  CREATE USER IF NOT EXISTS 'accounting_laravel'@'localhost';
  GRANT ALL PRIVILEGES ON accounting_laravel.* TO 'accounting_laravel'@'localhost';
  FLUSH PRIVILEGES;
"
```

In `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=accounting_laravel
DB_USERNAME=accounting_laravel
DB_PASSWORD=
```

Then migrate and seed:

```bash
php artisan migrate:fresh --seed
```

### Default login

After seeding, open [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login):

| Field    | Value   |
|----------|---------|
| Email    | `admin` |
| Password | `admin` |

## How to use

### 1. Log in and explore the dashboard

Sign in with the default admin account. The dashboard shows key financial metrics and links to the main modules.

### 2. Set up your chart of accounts

Go to **Accounts** to review or edit the seeded chart of accounts (40 accounts designed for Canadian brokerages). Accounts are organized by type: assets, liabilities, equity, revenue, cost of sales, and operating expenses.

### 3. Record transactions

Use any of these workflows:

- **Journal entries** — Create manual entries with multiple debit/credit lines. Post, approve, or reverse entries as needed. All changes are audited.
- **Transaction templates** — Apply one of 15 pre-built templates for common flows (invoice, expense, payment, commission, mortgage, rental, etc.).
- **Invoices & expenses** — Create operational documents; managers approve expenses before they affect reporting.
- **Payments** — Record payments with different methods (cash, bank transfer, Stripe, credit card).

### 4. Manage brokerage data

Add and maintain:

- **Clients** — Buyers, sellers, and other parties
- **Agents** — Sales agents and commission relationships
- **Properties** — Listings and managed properties

### 5. Run reports

Under **Reports**, generate statements from posted and approved journal entries:

- Balance Sheet, Income Statement (P&L), Cash Flow
- Trial Balance, General Ledger, Journal Register
- AR aging, expense summary, revenue by client/property/agent

### 6. Use calculators

The **Calculators** section includes:

- Mortgage calculator
- Rental income calculator
- Commission calculator

### 7. Switch language

Use the locale switcher in the UI to toggle between **English** and **French**.

### 8. Work with roles

| Role       | Typical use |
|------------|-------------|
| **Admin**  | Full access; only role that can manage users |
| **Manager**| Approve expenses and journal entries; reverse transactions; view reports |
| **Accountant** | Create invoices, expenses, journal entries, and payments; cannot approve own transactions |

See [docs/roles-and-permissions.md](docs/roles-and-permissions.md) for the full permission matrix.

### Development server

For local development with hot reload and logs:

```bash
composer dev
```

This starts the Laravel server, queue worker, log viewer, and Vite dev server together.

## Running tests

```bash
php artisan test
```

Or:

```bash
composer test
```

## Documentation

Additional design and implementation docs are in the `docs/` folder:

- [playbook.md](docs/playbook.md) — Setup, architecture, and build order
- [accounting-design.md](docs/accounting-design.md) — Journal entries, COA, templates, and financial statements
- [roles-and-permissions.md](docs/roles-and-permissions.md) — Role definitions and authorization rules
- [test-plan.md](docs/test-plan.md) — Test cases and acceptance criteria

## License

MIT
