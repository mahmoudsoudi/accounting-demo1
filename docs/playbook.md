# AI Agent Implementation Playbook

## Build Order

1. Laravel project structure and environment configuration
2. Database configuration (MySQL: `accounting_laravel`)
3. Eloquent models and enums
4. Migrations for all accounting and real estate entities
5. Seeders: roles, admin user, Chart of Accounts, transaction templates
6. Blade layouts and module views
7. Controllers and form handling
8. Routes (`routes/web.php`)
9. Middleware, Gates, and authorization rules
10. English/French JSON translations
11. PHPUnit tests
12. Documentation (`docs/`)

## Architecture Decisions

- **Double-entry accounting** via `journal_entries` and `journal_lines`
- **Service layer** for journal posting, template application, financial statements, and calculators
- **Transaction templates** store debit/credit patterns by account code with variable placeholders
- **Role-based access** using Gates (`manage-users`, `approve-transactions`, etc.)
- **Audit trail** via `audit_logs` polymorphic table on journal lifecycle events
- **Blade + Bootstrap 5** for UI; jQuery UI datepickers on date fields
- **JSON translations** in `lang/en.json` and `lang/fr.json`

## Database Setup

```bash
# Create database and user (MySQL)
mysql -uroot -e "
  CREATE DATABASE IF NOT EXISTS accounting_laravel;
  CREATE USER IF NOT EXISTS 'accounting_laravel'@'localhost';
  GRANT ALL PRIVILEGES ON accounting_laravel.* TO 'accounting_laravel'@'localhost';
  FLUSH PRIVILEGES;
"

# Configure .env
DB_CONNECTION=mysql
DB_DATABASE=accounting_laravel
DB_USERNAME=accounting_laravel
DB_PASSWORD=

# Migrate and seed
php artisan migrate:fresh --seed
```

## Default Login

- Email: `admin`
- Password: `admin`

## Running Locally

```bash
cd /Applications/MAMP/htdocs/accounting-laravel
php artisan serve
# Visit http://127.0.0.1:8000/login
```

With MAMP, point the virtual host document root to the `public/` directory.

## Testing Instructions

```bash
php artisan test
```

Tests use SQLite in-memory (`phpunit.xml`). Feature tests call `seedAccountingDefaults()` for roles, COA, and templates.

## Key Paths

| Area | Location |
|------|----------|
| Models | `app/Models/` |
| Services | `app/Services/Accounting/`, `app/Services/Calculators/` |
| Seeders | `database/seeders/` |
| Views | `resources/views/` |
| Gates | `app/Providers/AuthServiceProvider.php` |
| Translations | `lang/en.json`, `lang/fr.json` |
