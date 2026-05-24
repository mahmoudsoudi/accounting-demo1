# Test Plan

## Unit Tests

| Test | File | Criteria |
|------|------|----------|
| Mortgage calculator | `tests/Unit/CalculatorTest.php` | Returns payment schedule with correct count |
| Rental calculator | `tests/Unit/CalculatorTest.php` | Computes NOI from rent and expenses |
| Commission calculator | `tests/Unit/CalculatorTest.php` | Splits brokerage/agent amounts correctly |

## Feature Tests

| Test | File | Criteria |
|------|------|----------|
| Admin user seeding | `AdminUserSeederTest` | `admin` user exists with admin role |
| Role creation | `RolePermissionTest` | Three roles seeded |
| Only admin creates users | `RolePermissionTest` | Manager/accountant get 403 on user create |
| Manager approves expenses | `RolePermissionTest` | Manager has approve permission |
| Accountant cannot approve own expense | `RolePermissionTest` | Gate denies self-approval |
| Journal must balance | `JournalEntryTest` | Unbalanced post throws exception |
| Multiple line items | `JournalEntryTest` | 3-line entry balances |
| Reversal preserves history | `JournalEntryTest` | Original reversed, audit log exists |
| Manager can reverse | `JournalEntryTest` | Manager reverses approved entry |
| COA seed exists | `TransactionTemplateTest` | 30+ accounts including 4010, 1110 |
| Templates create lines | `TransactionTemplateTest` | 15+ templates with line items |
| Invoice payment templates | `TransactionTemplateTest` | Cash, bank, Stripe templates exist |
| Expense payment templates | `TransactionTemplateTest` | Cash, bank, credit card templates exist |
| Template application | `TransactionTemplateTest` | Applied entry is balanced |
| Statements use posted only | `FinancialStatementTest` | Draft excluded from revenue |
| English default locale | `LocaleTest` | `config('app.locale')` is `en` |
| French translations | `LocaleTest` | `lang/fr.json` contains translations |
| Locale switch | `LocaleTest` | Session locale updates to `fr` |

## Acceptance Criteria

- [x] Laravel app runs locally
- [x] Migrations run successfully
- [x] Seeders create admin, roles, COA, templates
- [x] Authentication and RBAC work
- [x] Dashboard, COA, journals, invoices, expenses, payments, reports, calculators accessible via Blade
- [x] Tests pass (`php artisan test`)
- [x] Documentation in `docs/`

## Running Tests

```bash
php artisan test
php artisan test --filter=JournalEntryTest
```
