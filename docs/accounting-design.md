# Accounting Design

## Model Overview

The system implements accrual-basis double-entry accounting for a Canadian real estate brokerage.

Core entities:

- **Account** — Chart of Accounts with hierarchical parent/child structure
- **JournalEntry / JournalLine** — Manual and template-generated transactions
- **TransactionTemplate** — Reusable posting patterns for invoices, expenses, payments, commissions, mortgage, etc.
- **Invoice, Expense, Payment** — Operational documents linked to journal entries
- **Client, Agent, Property, Listing, Commission** — Real estate domain objects
- **AmortizationSchedule / AmortizationPayment** — Mortgage amortization tracking
- **AuditLog** — Immutable audit trail for journal lifecycle

## Journal Entry Lifecycle

```
Draft → Posted → Approved → Reversed (original preserved)
                  ↓
            Reversal Entry (new approved entry, linked via reversal_of_id)
```

Rules:

- Draft entries are editable; posted/approved entries are not
- Posting requires balanced debits/credits and at least two lines
- Approval can auto-post draft entries
- Reversal creates a mirror entry and marks the original as `reversed`
- All status changes write to `audit_logs`

## Chart of Accounts Design

Canadian brokerage COA seeded with codes 1000–6100:

| Range | Type | Examples |
|-------|------|----------|
| 1000–1999 | Assets | Cash, Bank, Trust Funds, A/R, Commission Receivable |
| 2000–2999 | Liabilities | A/P, Agent Commission Payable, GST/HST, Mortgage Payable |
| 3000–3999 | Equity | Retained Earnings, Owner Drawings |
| 4000–4999 | Revenue | Brokerage Commission, Rental, Property Management |
| 5000–5999 | Cost of Sales | Agent Commission Expense, Referral Fees |
| 6000–6999 | Operating Expenses | Listing, Marketing, Insurance, Maintenance, Taxes |

Each account stores: code, name, type, parent, normal balance (debit/credit), active flag, description.

## Transaction Template Design

Templates define:

- `slug` — unique identifier (e.g. `invoice_payment_stripe`)
- `trigger_action_type` — workflow category
- `variables` — JSON list of supported placeholders (`amount`, `tax`, `agent`, etc.)
- `links_to` — whether applicable to invoices, expenses, payments, or amortization
- **Lines** — account code, debit/credit side, amount variable, description template

Application flow (`TransactionTemplateService`):

1. Resolve account codes to IDs
2. Map variables to amounts (including derived `agent_amount`)
3. Build journal lines
4. Create and post journal entry via `JournalEntryService`

## Financial Statement Logic

`FinancialStatementService` aggregates **posted and approved** journal lines only.

| Statement | Logic |
|-----------|-------|
| Trial Balance | Per-account debit/credit columns |
| Balance Sheet | Assets, Liabilities, Equity by account type |
| Income Statement | Revenue − Cost of Sales − Expenses for date range |
| Cash Flow | Net change in cash/bank/Stripe clearing accounts |
| General Ledger | Running balance per account with date filter |
| Journal Register | All posted/approved/reversed entries |

Supporting reports: AR aging (open invoices), expense summary by category, revenue by client/property/agent.
