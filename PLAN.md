# Enhanced AI Agent Prompt: Accounting Dashboard for Canadian Real Estate Brokerage

You are a senior software architect, senior Laravel engineer, and senior accounting manager. Build a complete accounting dashboard for a Canadian real estate brokerage company.

## Project Context

Create a Laravel application named `accounting-laravel`.

Tech stack:

- Laravel latest stable version
- Blade templates
- MySQL
- Bootstrap
- jQuery UI
- English/French translations, with English as the default language

Database:

- Database name: `accounting_laravel`
- Database user: `accounting_laravel`
- Database password: none

Default admin user:

- Username/email: `admin`
- Password: `admin`

## Core Accounting Scope

Design and implement a full accounting module for a Canadian real estate brokerage. The system must include:

- Accounting dashboard with key financial metrics
- Chart of Accounts suitable for real estate brokerages
- Manual journal entries with multiple debit/credit line items
- Journal entry approval, reversal, and audit trail
- Pre-recorded transaction templates for common brokerage workflows
- Major financial statements
- Common accounting sub-statements
- Expense management
- Invoice management
- Payment recording with multiple payment methods
- Installment payments
- Amortization schedules
- Mortgage calculator
- Rental calculator
- Commission calculator
- Taxes, insurance, maintenance, and real estate management accounting flows

## Security and Roles

Implement role-based access control with exactly these core roles:

1. `admin`
   - Only role allowed to create, edit, or deactivate users
   - Full system access
   - There must be at least one admin
   - Only one primary admin account should be seeded by default

2. `manager`
   - Can approve expenses
   - Can approve journal entries
   - Can reverse approved transactions
   - Can view reports

3. `accountant`
   - Can create invoices, expenses, journal entries, payments, and templates
   - Cannot approve own transactions unless explicitly granted later
   - Cannot manage users

Use middleware and policies/gates where appropriate.

## Accounting Features

### Chart of Accounts

Create a real estate brokerage Chart of Accounts that covers common Canadian brokerage operations, including:

- Assets
- Liabilities
- Equity
- Revenue
- Cost of sales
- Operating expenses
- Trust/client funds where applicable
- Commission receivables
- Agent commission payable
- Brokerage commission income
- Listing expenses
- Marketing expenses
- Rent and property management income
- Insurance
- Maintenance
- Taxes
- Mortgage-related accounts
- Bank, Stripe, cash, and clearing accounts

Each account should support:

- Account code
- Account name
- Account type
- Parent account
- Normal balance
- Active/inactive status
- Description

### Journal Entries

Manual journal entries must support:

- Multiple journal line items
- Debit and credit validation
- Balanced entry requirement
- Draft, posted, approved, reversed statuses
- Reference number
- Transaction date
- Notes
- Attachments-ready structure
- Audit fields
- Reversal entries that preserve original transaction history

### Transaction Templates

Create reusable pre-recorded transaction templates for common workflows, including:

- Invoice creation
- Invoice payment by cash
- Invoice payment by bank transfer
- Invoice payment through Stripe
- Expense paid by cash
- Expense paid by bank
- Expense paid by credit card
- Agent commission payout
- Brokerage commission income
- Rental income
- Property maintenance expense
- Insurance payment
- Tax payment
- Mortgage payment split between principal and interest
- Amortization entry

Each template should define:

- Template name
- Description
- Trigger/action type
- Required accounts
- Debit/credit journal lines
- Optional variables such as amount, tax, commission rate, payment method, property, client, or agent
- Whether the template can be linked to invoices, expenses, payments, or amortization schedules

## Financial Statements

Implement these major statements:

- Balance Sheet
- Income Statement / Profit and Loss
- Cash Flow Statement
- Trial Balance
- General Ledger

Implement common supporting reports/sub-statements:

- Accounts Receivable aging
- Accounts Payable aging
- Commission payable report
- Commission receivable report
- Tax summary
- Expense summary
- Revenue by property/client/agent
- Bank/payment method summary
- Journal entry register

## Real Estate Brokerage Features

Include data structures and workflows for:

- Clients
- Agents
- Properties
- Listings or managed properties
- Commissions
- Rental income
- Property maintenance
- Insurance
- Taxes
- Mortgage calculations
- Amortization schedules
- Installment payments

Calculators required:

- Mortgage calculator
- Rental income calculator
- Commission calculator

## Implementation Order

The AI coding agent must build the system in this order:

1. Create Laravel project structure and environment configuration
2. Create database configuration
3. Create models
4. Create migrations
5. Create seeders for roles, admin user, Chart of Accounts, and transaction templates
6. Create Blade templates and layouts
7. Create controllers
8. Create routes
9. Create middleware, policies, and authorization logic
10. Add translations for English and French
11. Add tests
12. Create documentation in a `docs` folder

## Required Documentation

Create a `docs` folder containing:

- `docs/playbook.md`
  - Step-by-step AI agent implementation playbook
  - Build order
  - Architecture decisions
  - Database setup instructions
  - Testing instructions

- `docs/accounting-design.md`
  - Accounting model overview
  - Journal entry lifecycle
  - Chart of Accounts design
  - Transaction template design
  - Financial statement logic

- `docs/roles-and-permissions.md`
  - Role definitions
  - Permission matrix
  - Authorization rules

- `docs/test-plan.md`
  - Unit test cases
  - Feature test cases
  - Acceptance criteria

## Testing and Acceptance Criteria

Add unit and feature tests covering:

- Admin user seeding
- Role creation and permissions
- Only admin can create users
- Manager can approve expenses
- Manager can reverse transactions
- Accountant cannot approve restricted transactions
- Journal entries must balance before posting
- Journal entries can contain multiple line items
- Reversed journal entries preserve audit history
- Chart of Accounts seed data exists
- Transaction templates create correct journal lines
- Invoice payment templates work for cash, bank, and Stripe
- Expense templates work for cash, bank, and credit card
- Financial statements calculate from posted journal entries only
- English is the default locale
- French translations are available

The implementation is accepted only when:

- Laravel app runs locally
- Database migrations run successfully
- Seeders create admin user, roles, COA, and templates
- Authentication and role permissions work
- Dashboard, COA, journal entries, invoices, expenses, payments, reports, and calculators are accessible through Blade UI
- Tests pass
- Documentation exists in the `docs` folder

## Quality Requirements

Use clean Laravel conventions:

- Eloquent models and relationships
- Form requests for validation
- Policies or gates for authorization
- Service classes for accounting calculations where useful
- Seeders for default accounting data
- Blade components/partials for reusable UI
- Bootstrap for responsive layout
- jQuery UI only where it improves interaction, such as date pickers, dialogs, or autocomplete
- Avoid hard-coded financial logic in Blade views
- Keep accounting calculations testable

Build the project as a practical accounting system, not a mockup. Prioritize correctness, auditability, and clear workflows.
