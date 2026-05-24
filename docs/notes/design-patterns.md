# Design Patterns in iBrokage

> Analyzed from source code — last updated 2026-05-22.
> Factory is **not** a primary pattern here. What may feel factory-like is Laravel's IoC container doing object construction — that belongs to the framework, not the app.

---

## 1. Template Method *(most dominant)*

**Where:** `app/Services/Saving/BaseSavingService.php`

`BaseSavingService::save()` defines a fixed algorithm skeleton. Subclasses override only the hooks they need.

```php
// BaseSavingService::save()
$params = $this->prepareArray($params);    // hook — override to transform input
$this->validate($params);                   // hook — override to add validation
$model  = $this->saveData($params);         // core — handles create vs update
$this->saveRelatedData($model, $params);    // hook — override for relations
$this->afterSave($model, $params);          // hook — override for media, mail, etc.
```

**Concrete subclasses** (50+): `AgentSavingService`, `UserSavingService`, `BranchSavingService`, `ClientSavingService`, `AnnouncementSavingService`, etc. Each lives in `app/Services/Saving/`.

The structure of the save operation never changes — only the steps. The base class also provides `saveAndCommit()` (wraps in a DB transaction), `saveMany()`, `storeManyFast()` (bulk insert), `saveManyToMany()` (diff-based pivot sync), and `delete()` / `deleteMany()`.

---

## 2. Strategy

### 2a. Filter Strategy

**Where:** `app/Filters/BaseFilter.php` + all concrete `*Filter` classes.

`BaseFilter::apply(array $filters)` iterates the incoming filter map and dispatches each key to a method on the concrete subclass:

```php
foreach ($filters as $filter => $value) {
    if (method_exists($this, $filter)) {
        $this->$filter($value);   // e.g., $this->status('active')
    }
}
```

Each `AgentFilter`, `BranchFilter`, `ClientFilter`, `ContactFilter`, etc. is a different strategy for modifying the same Eloquent query builder. Swap the filter class → get different search behaviour without touching the caller.

### 2b. Theme Processor Strategy

**Where:** `app/Services/ThemeImport/Processors/`

`ThemeProcessorContract` defines two methods:

```php
interface ThemeProcessorContract
{
    public function supports(ThemeImportType $type): bool;
    public function process(string $sandboxPath, string $zipPath, AgentTheme $theme, array &$trace): array;
}
```

Three concrete processors — `HtmlThemeProcessor`, `JsonThemeProcessor`, `LaravelThemeProcessor` — each declare what import type they handle via `supports()`. The service iterates them and picks the matching one at runtime.

---

## 3. Observer (two variants)

### 3a. Eloquent Model Observers

**Where:** `app/Observers/` — registered in `AppServiceProvider`.

| Observer | Model | Reacts to |
|---|---|---|
| `AgentObserver` | `Agent` | `created` → auto-creates default contact & client lists for the agent |
| `TransactionObserver` | `Transaction` | lifecycle hooks on deal changes |
| `UserObserver` | `User` | user lifecycle hooks |
| `ComplaintObserver` | `Complaint` | complaint lifecycle hooks |
| `TicketObserver` | `Ticket` | ticket lifecycle hooks |

```php
// AppServiceProvider
Agent::observe(AgentObserver::class);
Transaction::observe(TransactionObserver::class);
```

### 3b. Event / Listener (Pub-Sub)

**Where:** `app/Events/` + `app/Listeners/` — wired in `AppServiceProvider::register()`.

```php
Event::listen(ThemePublished::class,       [InvalidateThemeRenderCache::class,     'handleThemePublished']);
Event::listen(ThemePublished::class,       [CompileThemeAfterLifecycleEvent::class, 'handleThemePublished']);
Event::listen(SubscriptionActivated::class, [SendSubscriptionActivatedEmail::class, 'handle']);
Event::listen(SubscriptionCancelled::class, [SendSubscriptionCancelledEmail::class, 'handle']);
// ... + 6 more subscription events
```

One event can have multiple listeners (e.g., `ThemePublished` triggers both cache invalidation and recompile). This is the publish-subscribe variant of Observer.

---

## 4. Service Layer

**Where:** `app/Services/`

Business logic is fully extracted from controllers into purpose-built service classes. Controllers validate input, call a service, and return a view or JSON.

Key financial/accounting services:

| Service | Responsibility |
|---|---|
| `StripeInvoiceService` | Create, send, cancel Stripe invoices & subscriptions |
| `BillingService` | Stripe Connect onboarding, bank account management |
| `AccountBalanceService` | Aggregate debit/credit from `VoucherAccount` for reporting |
| `StripeService` | Low-level Stripe API calls |

Other domain services: `AgentWebsiteService`, `ImageComposerService`, `PdfParserService`, `ThemeBuilderService`, `WebsiteReportingService`, etc.

---

## 5. Action / Command Object

**Where:** `app/Actions/`

Single-responsibility classes that encapsulate one operation. Each has a `handler()` or equivalent entry point.

```
app/Actions/
├── Helper/
│   └── StoreOrUpdateMedia.php      # compares file hash before replacing media
├── StoreBranchDocumentHandler.php
├── StoreGlobalDocumentHandler.php
├── StoreHealthSafetyDocumentHandler.php
├── TwoFactorAuthentication.php
├── Campaign/
└── ECard/
```

Example — `StoreOrUpdateMedia` takes a model, collection name, and request; compares MD5 hashes to decide whether to replace the file. The controller instantiates it and calls `->handler()`.

---

## 6. Strategy via Dependency Inversion (IoC Binding)

**Where:** `AppServiceProvider::register()`

```php
$this->app->bind(
    \App\Contracts\ThemeEngine\ThemeAssetStorage::class,
    \App\Services\ThemeEngine\LocalThemeAssetStorage::class
);

$this->app->singleton(Client::class, function (Application $app) {
    // Twilio client — one instance per container lifecycle
});
```

Controllers and services depend on the `ThemeAssetStorage` **interface**, not the concrete class. Swapping implementations (local disk → S3) requires only a single line change in the provider. This is Dependency Inversion realized through the container, enabling Strategy-level substitutability.

---

## 7. Mixin via PHP Traits

**Where:** `app/Traits/`

| Trait | Composed into | Does |
|---|---|---|
| `TransactionJournalEntries` | `TransactionController` | Auto-posts double-entry vouchers on deal close; loads 17 account IDs from `setting()` |
| `TransactionCalculations` | `TransactionController`, `TransactionCommissionController` | Computes agent fees: enrollment, transaction, deal, admin, royalty, garnishment, withholding |
| `DatatableHelper` | DataTable classes | Shared DataTable utility methods |
| `HasResizableImage` | Models | Image resize helpers |
| `Searchable` | Models | Adds `search()` scope for filter-based querying |
| `NotificationHelper` | Controllers | Shared notification dispatch logic |

Traits provide horizontal code reuse without inheritance. The `TransactionJournalEntries` + `TransactionCalculations` combination on `TransactionController` is the most architecturally significant use.

---

## 8. Repository-lite (inside Saving Services)

`BaseSavingService` doubles as a lightweight, model-scoped repository. It provides:

- `saveData()` — upsert (find by `id` or create new)
- `delete($id)` — soft-delete by ID
- `deleteMany($filter)` — delete by filter criteria
- `saveManyToMany($arr)` — diff-based pivot sync (adds missing, removes stale)
- `storeManyFast()` — bulk insert via `Model::insert()`

It is not a full Repository pattern (no query interface, no collection abstraction), but it centralizes all persistence for a model in one place.

---

## Pattern Interaction Map

```
HTTP Request
    │
    ▼
Controller (thin)
    │  calls
    ▼
SavingService (Template Method)         ← prepareArray / validate / saveRelatedData hooks
    │  delegates to
    ├─► Action Objects  (StoreOrUpdateMedia, etc.)
    └─► Model  ──────────────────────────────────► Observer (Eloquent lifecycle)
                                                         │
                                                         └─► Event fired ──► Listener(s) (pub-sub)

Query / List endpoints:
Controller → Filter (Strategy) → Eloquent Builder → DataTable
```
