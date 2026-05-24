# Accounting & Financial Routes and Controllers

All financial/accounting functionality lives across three route contexts. Permission checks are handled per-controller via `userCanAccessPermission()`.

---

## 1. Chart of Accounts (Double-Entry Ledger)

**Route prefix:** `user.accounting.*`  
**Controller:** `App\Http\Controllers\User\ChartOfAccountController`  
**File:** `app/Http/Controllers/User/ChartOfAccountController.php`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/accounting/settings` | `settings()` | View account mappings (AR, AP, HST, commissions, trust accounts) |
| POST | `/accounting/store-settings` | `storeSettings()` | Persist account-to-system-setting mappings |
| GET | `/accounting/accounts` | `index()` | List all chart-of-accounts entries |
| GET | `/accounting/accounts/create` | `create()` | Form to add a new account |
| POST | `/accounting/accounts` | `store()` | Create a new chart-of-account |
| GET | `/accounting/accounts/{account}` | `show()` | View account detail + balance |
| GET | `/accounting/accounts/{account}/edit` | `edit()` | Edit account form |
| PUT | `/accounting/accounts/{account}` | `update()` | Update account |
| DELETE | `/accounting/accounts/{account}` | `destroy()` | Delete account |
| GET | `/accounting/print-account/{account}` | `printAccount()` | PDF printout of account ledger |
| GET | `/accounting/accounts-import` | `import()` | Import accounts form |
| GET | `/accounting/accounts-import-preview` | `importPreview()` | Preview import data |
| POST | `/accounting/accounts-import-save` | `importSave()` | Save imported accounts |
| GET | `/accounting/detail-types-search` | `searchDetailTypes()` | AJAX: search account detail types |
| GET | `/accounting/parent-accounts-search` | `searchParentAccounts()` | AJAX: search parent accounts |

**Models:** `App\Models\Accounting\ChartOfAccount`, `AccountCategory`, `AccountDetailType`, `VoucherAccount`

---

## 2. Vouchers (Journal Entries)

**Route prefix:** `user.accounting.*`  
**Controller:** `App\Http\Controllers\User\VoucherController`  
**File:** `app/Http/Controllers/User/VoucherController.php`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/accounting/vouchers` | `index()` | List vouchers (currently returns 404 — uses DataTable) |
| GET | `/accounting/vouchers/create` | `create()` | Manual journal entry form |
| POST | `/accounting/vouchers` | `store()` | Create double-entry journal voucher |
| GET | `/accounting/vouchers/{voucher}` | `show()` | View voucher detail |
| GET | `/accounting/vouchers/{voucher}/edit` | `edit()` | Edit voucher |
| PUT | `/accounting/vouchers/{voucher}` | `update()` | Update voucher |
| DELETE | `/accounting/vouchers/{voucher}` | `destroy()` | Delete voucher |
| GET | `/accounting/print-voucher/{voucher}` | `printVoucher()` | PDF print voucher |
| GET | `/accounting/accounts-search` | `searchAccounts()` | AJAX: search accounts for voucher lines |
| DELETE | `/accounting/delete-voucher-attachments/{voucher}/{id}` | `deleteAttachment()` | Remove voucher attachment |

**Models:** `App\Models\Accounting\Voucher`, `VoucherAccount`

---

## 3. Accounting Reports

**Route prefix:** `user.accounting.reports.*`  
**Controller:** `App\Http\Controllers\User\AccountingReportController`  
**File:** `app/Http/Controllers/User/AccountingReportController.php`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/accounting/reports` | `index()` | Reports dashboard |
| POST | `/accounting/reports/get-report-period-dates` | `getReportPeriodDates()` | AJAX: resolve named period dates |
| ANY | `/accounting/reports/balance-sheet` | `balanceSheet()` | Balance Sheet (Assets / Liabilities / Equity) |
| ANY | `/accounting/reports/profit-and-loss` | `profitAndLoss()` | Profit & Loss Statement |
| ANY | `/accounting/reports/trial-balance` | `trialBalance()` | Trial Balance |
| ANY | `/accounting/reports/general-ledger` | `generalLedger()` | General Ledger |
| ANY | `/accounting/reports/ap-aging-summary` | `accountsPayableAgingSummary()` | AP Aging Summary |
| ANY | `/accounting/reports/ap-aging-detail` | `accountsPayableAgingDetail()` | AP Aging Detail |
| ANY | `/accounting/reports/ar-aging-summary` | `accountsReceivableAgingSummary()` | AR Aging Summary |
| ANY | `/accounting/reports/ar-aging-detail` | `accountsReceivableAgingDetail()` | AR Aging Detail |
| ANY | `/accounting/reports/t4a-contractor-info` | `t4AcontractorInfo()` | T4A Contractor Info (CRA payroll slip summary) |
| ANY | `/accounting/reports/t4a-contractor-detail` | `t4AcontractorDetail()` | T4A Contractor Detail |
| ANY | `/accounting/reports/journal` | `journal()` | Journal Report (all voucher lines) |

**Key Service:** `App\Services\AccountBalanceService` — aggregates `VoucherAccount` debits/credits per `ChartOfAccount` within a date range.

---

## 4. Invoices (Broker → Agent Billing)

### User (Broker) Context

**Controller:** `App\Http\Controllers\User\InvoiceController`  
**File:** `app/Http/Controllers/User/InvoiceController.php`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/billing/invoices` | `index()` | List all brokerage invoices |
| GET | `/billing/invoices/create` | `create()` | New invoice form (multi-agent) |
| GET | `/billing/invoices/agent-create` | `agentCreate()` | New invoice form for specific agent |
| POST | `/billing/invoices` | `store()` | Create invoice + Stripe invoice |
| GET | `/billing/invoices/{invoice}` | `show()` | Invoice detail |
| GET | `/billing/invoices/{invoice}/preview` | `preview()` | Invoice preview (Blade) |
| POST | `/billing/invoices/{invoice}/resend` | `resend()` | Re-send invoice email |
| POST | `/billing/invoices/{invoice}/send` | `send()` | Send invoice to agent |
| DELETE | `/billing/invoices/{invoice}` | `destroy()` | Delete invoice |
| GET | `/invoices/export` | `export()` | Excel export |
| GET | `/invoices/{invoice}/download` | `download()` | PDF download |
| GET | `/invoices/{invoice}/print` | `print()` | Print view |
| POST | `/{invoice}/cancel-subscription` | `cancelSubscription()` | Cancel recurring Stripe subscription |
| GET | `/api/agents` | `getAgents()` | AJAX: list agents for invoice creation |
| GET | `/api/invoice-items` | `getInvoiceItems()` | AJAX: list billing items |

**Key Service:** `App\Services\StripeInvoiceService`

### Agent View of Invoices

**Controller:** `App\Http\Controllers\Agent\InvoiceController`  
**File:** `app/Http/Controllers/Agent/InvoiceController.php`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/agent/payments` | `index()` | Agent's invoice/payment list |
| GET | `/agent/invoices/{invoice}` | `show()` | Invoice detail |
| GET | `/agent/invoices/{invoice}/download` | `download()` | PDF download |
| GET | `/agent/invoices/export` | `export()` | Excel export |
| GET | `/agent/invoices/{invoice}/print` | `print()` | Print view |
| POST | `/agent/invoices/{invoice}/archive` | `archive()` | Archive invoice |
| POST | `/agent/invoices/{invoice}/unarchive` | `unarchive()` | Unarchive invoice |
| GET | `/agent/payments/archived` | `archived()` | Archived payments list |
| GET | `paymentSuccess()` | `paymentSuccess()` | Stripe payment success callback |
| GET | `paymentCancel()` | `paymentCancel()` | Stripe payment cancel callback |

### Invoice Payment (Public Pay Page)

**Controller:** `App\Http\Controllers\User\InvoicePaymentController`  
**File:** `app/Http/Controllers/User/InvoicePaymentController.php`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/agent/pay/{invoice}` | `show()` | Public payment page for agent |
| POST | `/agent/pay/process` | `process()` | Process Stripe payment intent |
| GET | `/agent/pay/success/{invoice}` | `success()` | Payment success page |

---

## 5. Billing Categories

**Controller:** `App\Http\Controllers\User\BillingCategoryController`  
**File:** `app/Http/Controllers/User/BillingCategoryController.php`  
**Route:** `Route::resource('billing-categories', BillingCategoryController::class)` — full CRUD.

Used to categorize line items on invoices (e.g., "Transaction Fee", "Admin Fee").

---

## 6. Billing Onboarding (Stripe Connect)

**Controller:** `App\Http\Controllers\User\BillingOnboardingController`  
**File:** `app/Http/Controllers/User/BillingOnboardingController.php`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/billing/onboarding` | `index()` | Stripe Connect onboarding start |
| GET | `/billing/onboarding/start` | `start()` | Redirect to Stripe Connect OAuth |
| GET | `/billing/onboarding/complete` | `complete()` | Stripe Connect return URL |
| GET | `/billing/onboarding/refresh` | `refresh()` | Re-initiate if expired |
| GET | `/billing/dashboard` | `dashboard()` | Billing overview dashboard |
| GET | `/billing/status` | `status()` | AJAX: check Stripe onboarding status |
| GET | `/billing/subscriptions` | `activeSubscriptions()` | List active Stripe subscriptions |
| GET | `/billing/setup` | `setup()` | Setup wizard entry |
| GET | `/billing/bank/replace` | `replaceBankAccount()` | Replace linked bank account |
| POST | `/billing/bank/remove` | `removeBankAccount()` | Detach bank account |
| GET | `/billing/bank/complete` | `bankUpdateComplete()` | Bank update success |
| GET | `/billing/bank/refresh` | `bankUpdateRefresh()` | Bank update refresh |
| POST | `/billing/deactivate` | `deactivate()` | Deactivate billing |

**Key Service:** `App\Services\BillingService`

---

## 7. Billing Controller (Invoice Dashboard)

**Controller:** `App\Http\Controllers\User\BillingController`  
**File:** `app/Http/Controllers/User/BillingController.php`  
**Method:** `index()` — renders broker-side invoice dashboard with stats (total, paid, unpaid, amounts).

> Not currently registered as a named route in `web.php`; used internally or referenced directly.

---

## 8. Agent Billing (Admin view)

**Controller:** `App\Http\Controllers\User\AgentBillingController`  
**File:** `app/Http/Controllers/User/AgentBillingController.php`  
**Method:** `index(PaymentsDataTable $dataTable, $agentId)` — shows payment history for a given agent.

**Route:** `GET /agents/{agent}/billing` → `user.agents.billing`

---

## 9. Agent Credit Cards (Payment Methods)

**Controller:** `App\Http\Controllers\Agent\CreditCardController`  
**File:** `app/Http/Controllers/Agent/CreditCardController.php`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/agent/credit-cards` | `index()` | List saved payment methods |
| GET | `/agent/credit-cards/create` | `create()` | Add card form |
| POST | `/agent/credit-cards` | `store()` | Attach Stripe payment method |
| GET | `/agent/credit-cards/{credit_card}/edit` | `edit()` | Edit card |
| PUT | `/agent/credit-cards/{credit_card}/update` | `update()` | Update card |
| DELETE | `/agent/credit-cards/{credit_card}` | `destroy()` | Remove card |
| GET | `/agent/credit-cards/setup-intent` | `setupIntent()` | Stripe SetupIntent AJAX |
| GET | `/agent/credit-cards/api/valid-cards` | `getValidCards()` | AJAX: valid cards for payment |

---

## 10. Stripe Webhook

**Controller:** `App\Http\Controllers\Agent\StripeWebhookController`  
**File:** `app/Http/Controllers/Agent/StripeWebhookController.php`  
**Route:** `POST /agent/stripe/webhook` → `handleWebhook()`  
Handles Stripe event callbacks (payment_intent.succeeded, invoice.paid, subscription events, etc.).

---

## 11. Payments (Agent)

**Controller:** `App\Http\Controllers\Agent\PaymentController`  
**File:** `app/Http/Controllers/Agent/PaymentController.php`  
**Route:** `GET /agent/payments` → `index()` — shows agent's payment history via DataTable.

---

## 12. Transaction Commission & Fees

**Controller:** `App\Http\Controllers\User\TransactionCommissionController`  
**File:** `app/Http/Controllers/User/TransactionCommissionController.php`  
**Route prefix:** `user.transactions.tabs.transaction-commissions.*`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `transactions/{transaction}/transaction-commissions` | `index()` | List commissions for transaction |
| GET | `transactions/{transaction}/transaction-commissions/{id}` | `show()` | Commission detail |
| GET | `transactions/{transaction}/transaction-commissions/create` | `create()` | Add commission form |
| GET | `transactions/{transaction}/transaction-commissions/create-expenses` | `createExpenses()` | Add expense commission form |
| POST | `transactions/{transaction}/transaction-commissions` | `store()` | Create commission entry |
| GET | `transactions/{transaction}/transaction-commissions/{id}/edit` | `edit()` | Edit commission |
| GET | `transactions/{transaction}/transaction-commissions/{id}/edit-expenses` | `editExpenses()` | Edit expense commission |
| PUT | `transactions/{transaction}/transaction-commissions/{id}` | `update()` | Update commission |
| DELETE | `transactions/{transaction}/transaction-commissions/{id}` | `destroy()` | Delete commission |

**Trait:** `App\Traits\TransactionCalculations` — computes `enrollment_fee`, `transaction_fee`, `deal_fee`, `accounting_fee`, `royalty_cap_fee`, `admin_fee`, `lease_deal_fee`, withholding, garnishment.

---

## 13. Transaction Brokerage Splits

**Controller:** `App\Http\Controllers\User\TransactionBrokerageController`  
**File:** `app/Http/Controllers/User/TransactionBrokerageController.php`  
**Route prefix:** `user.transactions.tabs.transaction-brokerages.*`  
Full CRUD resource for recording co-op brokerage information and commission splits on a transaction.

---

## 14. Transaction Agent (Agent on a Transaction)

**Controller:** `App\Http\Controllers\User\TransactionAgentController`  
**File:** `app/Http/Controllers/User/TransactionAgentController.php`  
**Route prefix:** `user.transactions.tabs.transaction-agents.*`  
Full CRUD resource linking agents to transactions; triggers `TransactionCalculations` on store/update.

---

## 15. Transaction Journal & Deal Close

All in `App\Http\Controllers\User\TransactionController` (6,500-line file):

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `transactions/{transaction}/journal` | `journal()` | View auto-generated journal entries for transaction |
| GET | `transactions/{transaction}/general-ledger` | `generalLedger()` | Transaction-scoped ledger view |
| POST | `transactions/{transaction}/delete-journal-entries` | `deleteJournalEntries()` | Remove posted journal vouchers |
| POST | `transactions/{transaction}/post-journal-entries` | `postJournalEntries()` | Post deal journal entries to ledger |
| POST | `transactions/{transaction}/close-deal` | `closeDeal()` | Close deal → triggers `TransactionJournalEntries::triggerDealCloses()` |
| GET | `transactions/shortfall_excess_balance` | `shortfallExcessBalance()` | Report: trust account shortfall/excess |

**Trait:** `App\Traits\TransactionJournalEntries` — on deal close, posts double-entry vouchers for:
- Accounts Receivable
- Gross Commission Income
- HST Paid / Collected
- Agent Commissions
- Accounts Payable (other brokerages)
- Trust Account / Commission Trust / Contra Trust
- General Bank
- Royalty Income / Expense

Account IDs are resolved from the `setting()` helper (configured in Chart of Accounts settings).

---

## 16. Transaction Reports (TR Reports)

These are financial summary reports in `TransactionController`:

| Route | Method | Description |
|-------|--------|-------------|
| `transactions/reports/tr420` | `tr420()` | TR-420 (Disbursement Trust) |
| `transactions/reports/tr421` | `tr421()` | TR-421 |
| `transactions/reports/tr422` | `tr422()` | TR-422 |
| `transactions/reports/tr440` | `tr440()` | TR-440 |
| `transactions/reports/tr470` | `tr470()` | TR-470 |
| `transactions/reports/branch/{branch}` | `branchTransactions()` | Branch-level transaction report |
| `transactions/reports/branch-reports` | `branchReports()` | All-branch report |

---

## 17. Commissions & Office Fees (Agent Setup)

Commission rates are configured per-agent by brokers.

**Routes in `web.php` (User context):**
- `GET create/agent/commissions-and-office-fees/{agent?}` → `AgentSettingController::createAgentCommissionAndOfficeFees()` — `user.agents.commissions-and-office-fees`
- `POST create/agent/commissions-and-office-fees/{agent?}` → `AgentSettingController::submitAgentCommissionAndOfficeFees()` — `user.agents.commissions-and-office-fees.submit`
- `POST store/agent/{agent}/commission-document` → `AgentSettingController::storeCommissionDocument()` — `user.agents.store.agent.commission-document`
- `POST complete/commission-document/{commission_document}` → `UserSettingController::completeCommissionDocument()` — `user.commission.document.complete`
- `GET download/commission-document/{commission_document}` → `UserSettingController::downloadCommissionDocument()` — `user.download.commission.document`
- `DELETE destroy/commission-document/{commission_document}` → `UserSettingController::destroyCommissionDocument()` — `user.commission.document.destroy`

**Agent self-service routes in `agent.php`:**
- `GET update/my-commissions-and-office-fees` → `UserSettingController::createAgentCommissionAndOfficeFees()` — `agent.commissions-and-office-fees`
- `POST update/my-commissions-and-office-fees` → `UserSettingController::submitAgentCommissionAndOfficeFees()` — `agent.commissions-and-office-fees.submit`

**Model:** `App\Models\CommissionAndOfficeFee` (from migration `commission_and_office_fees` table)

---

## 18. Commission Plans (Admin-Level)

**Controller:** `App\Http\Controllers\Admin\CommissionPlanController`  
**File:** `app/Http/Controllers/Admin/CommissionPlanController.php`  
**Route prefix:** `admin.compensation.commission-plans.*`

Full CRUD — defines global/template commission plan structures that can be applied per account.

---

## 19. Compensation Fees (Admin-Level)

**Controller:** `App\Http\Controllers\Admin\CompensationController`  
**File:** `app/Http/Controllers/Admin/CompensationController.php`  
**Routes:**
- `GET admin/compensation` → `index()` — `admin.compensation.index`
- `PUT admin/compensation/fees` → `updateFees()` — `admin.compensation.fees.update`

---

## 20. Expense Tracker (Agent)

**Controller:** `App\Http\Controllers\Agent\ExpenseController`  
**File:** `app/Http/Controllers/Agent/ExpenseController.php`  
**Route prefix:** `agent.expenses.*`

| Method | Route | Controller Method | Description |
|--------|-------|-------------------|-------------|
| GET | `/agent/expenses` | `index()` | Expense list with YTD totals + HST |
| GET | `/agent/expenses/create` | `create()` | New expense form |
| POST | `/agent/expenses` | `store()` | Create expense |
| GET | `/agent/expenses/{expense}` | `show()` | Expense detail |
| GET | `/agent/expenses/{expense}/edit` | `edit()` | Edit expense |
| PUT | `/agent/expenses/{expense}` | `update()` | Update expense |
| DELETE | `/agent/expenses/{expense}` | `destroy()` | Delete expense |
| GET | `/agent/expenses/{expense}/receipt` | `receipt()` | View receipt media |
| GET | `/agent/expenses/subcategories/{category}` | `getSubcategories()` | AJAX: get subcategories |
| GET | `/agent/expenses/{expense}/notes` | `notes()` | View expense notes |
| GET | `/agent/expenses/{expense}/note/create` | `createNote()` | Add note |
| POST | `/agent/expenses/{expense}/note` | `storeNote()` | Save note |
| GET | `/agent/expenses/{expense}/note/{note}/edit` | `editNote()` | Edit note |
| PUT | `/agent/expenses/{expense}/note/{note}` | `updateNote()` | Update note |
| DELETE | `/agent/expenses/{expense}/note/{note}` | `destroyNote()` | Delete note |
| POST | `/agent/expenses/{expense}/archive` | `archive()` | Archive expense |
| POST | `/agent/expenses/{expense}/unarchive` | `unarchive()` | Unarchive expense |
| GET | `/agent/expenses/archived` | `archived()` | Archived expenses list |

---

## 21. Expense Categories (Admin)

**Controller:** `App\Http\Controllers\Admin\ExpenseCategoryController`  
**File:** `app/Http/Controllers/Admin/ExpenseCategoryController.php`  
**Route prefix:** `admin.expense-categories.*`

Full CRUD + `GET expense-categories/{expense_category}/subcategories` → `subcategories()` — returns sub-category list.

---

## 22. Promo Codes

**Controller:** `App\Http\Controllers\User\PromoCodeController`  
**File:** `app/Http/Controllers/User/PromoCodeController.php`  
**Route:** `Route::resource('promo-codes', PromoCodeController::class)->except(['show'])` → `user.promo-codes.*`

---

## Key Supporting Services & Traits

| File | Role |
|------|------|
| `app/Services/AccountBalanceService.php` | Aggregates ledger balances from `VoucherAccount` rows for reporting |
| `app/Services/StripeInvoiceService.php` | Creates/updates/cancels Stripe invoices and subscriptions |
| `app/Services/BillingService.php` | Stripe Connect onboarding and account management |
| `app/Traits/TransactionJournalEntries.php` | Auto-posts double-entry vouchers on deal close; loads 17 account IDs from settings |
| `app/Traits/TransactionCalculations.php` | Computes agent fee breakdowns (enrollment, transaction, deal, royalty, garnishment, withholding) |

---

## Database Models Summary

| Model | Table | Purpose |
|-------|-------|---------|
| `Accounting\ChartOfAccount` | `chart_of_accounts` | Double-entry account tree |
| `Accounting\AccountCategory` | `account_categories` | Asset / Liability / Equity / Income / Expense |
| `Accounting\AccountDetailType` | `account_detail_types` | Sub-classification within categories |
| `Accounting\Voucher` | `vouchers` | Journal entry header |
| `Accounting\VoucherAccount` | `voucher_accounts` | Journal entry lines (debit/credit) |
| `Invoice` | `invoices` | Agent billing invoices (Stripe-backed) |
| `InvoiceItem` | `invoice_items` | Line items on an invoice |
| `Payment` | `payments` | Stripe payment records |
| `BillingCategory` | `billing_categories` | Invoice line-item categories |
| `CreditCard` | `credit_cards` | Saved Stripe payment methods |
| `Transaction\Transaction` | `transactions` | Real-estate deals |
| `Transaction\TransactionCommission` | `transaction_commissions` | Commission/fee entries per agent on deal |
| `Transaction\TransactionBrokerage` | `transaction_brokerages` | Co-op brokerage entries |
| `Transaction\TransactionAgent` | `transaction_agents` | Agents linked to a transaction |
| `Transaction\TransactionDeposit` | `transaction_deposits` | Trust deposits on deal |
| `Expense` | `expenses` | Agent expense records |
| `ExpenseCategory` | `expense_categories` | Hierarchical expense categories |
