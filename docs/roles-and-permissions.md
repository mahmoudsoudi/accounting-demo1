# Roles and Permissions

## Role Definitions

### Admin (`admin`)

- Full system access
- **Only role** that can create, edit, or deactivate users
- Can approve and reverse any transaction
- At least one active admin must always exist

### Manager (`manager`)

- Approve expenses and journal entries (except own, unless admin)
- Reverse approved transactions
- View all reports
- Cannot manage users

### Accountant (`accountant`)

- Create invoices, expenses, journal entries, payments
- Apply transaction templates
- View reports
- **Cannot** approve own transactions
- **Cannot** manage users

## Permission Matrix

| Action | Admin | Manager | Accountant |
|--------|:-----:|:-------:|:----------:|
| Manage users | ✓ | ✗ | ✗ |
| Create journal entries | ✓ | ✓ | ✓ |
| Post journal entries | ✓ | ✓ | ✓ |
| Approve own journal entry | ✓ | ✗ | ✗ |
| Approve others' journal entry | ✓ | ✓ | ✗ |
| Reverse approved entry | ✓ | ✓ | ✗ |
| Approve own expense | ✓ | ✗ | ✗ |
| Approve others' expense | ✓ | ✓ | ✗ |
| View reports | ✓ | ✓ | ✓ |
| Manage Chart of Accounts | ✓ | ✓ | ✓ |

## Authorization Rules

Implemented via Laravel Gates in `AuthServiceProvider`:

- `manage-users` — admin only
- `approve-transactions` — admin or manager
- `reverse-transactions` — admin or manager
- `view-reports` — all authenticated roles
- `create-accounting` — admin or accountant
- `approve-journal-entry` — manager/admin, not own entry (except admin)
- `approve-expense` — manager/admin, not own expense (except admin)

Middleware:

- `EnsureUserIsActive` — logs out deactivated users
- `SetLocale` — applies session locale (en/fr)

## Seeded Users

Only one primary admin is seeded by default:

- Email: `admin`
- Password: `admin`

Additional users are created by admin through `/users`.
