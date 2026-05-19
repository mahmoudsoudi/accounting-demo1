# Quick Start Guide - Minimal Accounting App

## 🚀 Fast Setup (5 Minutes)

### Step 1: Verify Environment
```bash
php -v          # Should be 8.3+
composer -v     # Should be 2.x
npm -v          # Should be 8.x+
sqlite3 -version # Optional, for SQLite
```

### Step 2: Install Dependencies
```bash
composer install
npm install
```

### Step 3: Setup Database (SQLite - Recommended)

**Option A: SQLite (No Setup Required)**
```bash
# Create empty database file
touch database/database.sqlite

# Run migrations to SQLite
php artisan migrate:fresh --database=sqlite

# Seed the chart of accounts
php artisan db:seed --class=AccountSeeder --database=sqlite
```

**Option B: MySQL/MariaDB**
```bash
# Edit .env file and set:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=accounting_app
# DB_USERNAME=root
# DB_PASSWORD=

# Then run:
php artisan migrate:fresh --database=mysql
php artisan db:seed --class=AccountSeeder --database=mysql
```

### Step 4: Generate App Key
```bash
php artisan key:generate
```

### Step 5: Build Assets
```bash
npm run build
```

### Step 6: Start Dev Server
```bash
php artisan serve
```

### Step 7: Access the App
Open browser to: **http://localhost:8000/dashboard**

---

## 📌 First Steps in the App

### 1. View Dashboard
- Click **Dashboard** in navigation
- See summary stats and quick links

### 2. View Chart of Accounts
- Click **Accounts** → See seeded accounts
- Notice accounts organized by type and code
- Try clicking **View** on an account

### 3. Create a Journal Entry
- Click **Journal Entries** → **+ New Entry**
- Fill in date and optional description
- Add lines:
  - Line 1: Account Bank, Debit 5000
  - Line 2: Account Service Revenue, Credit 5000
- Click **Save and Post**

### 4. View Reports
- **Trial Balance**: Should show balanced
- **General Ledger**: Filter to Bank account
- **Profit & Loss**: Should show revenue of 5000
- **Balance Sheet**: Should show balanced

### 5. Reverse an Entry
- Go to **Journal Entries**
- Click **View** on a posted entry
- Click **Reverse** button
- Entry should be marked as reversed

---

## 🔍 Testing Validation

Try creating an invalid entry:

### Test 1: Unbalanced Entry
```
- Dr Bank 1000
- Cr Service Revenue 500
→ Error: "Total debit must equal total credit"
```

### Test 2: Single Line
```
- Just add one line
→ Error: "A journal entry must have at least 2 lines"
```

### Test 3: Both Debit and Credit
```
- Account: Bank
- Debit: 100
- Credit: 50
→ Error: "A line cannot have both debit and credit"
```

---

## 📊 Understanding the Reports

### Trial Balance
- **Purpose**: Verify that total debits = total credits
- **When to use**: After posting entries
- **What to look for**: Green checkmark = balanced ✓

### General Ledger
- **Purpose**: See all transactions for one account
- **How to use**: Select an account from dropdown
- **Shows**: Running balance after each transaction

### Profit & Loss
- **Purpose**: Show revenue, expenses, and net profit
- **Formula**: Net Profit = Total Revenue - Total Expenses
- **Period**: Select date range (optional)

### Balance Sheet
- **Purpose**: Verify Assets = Liabilities + Equity
- **How to read**: 
  - Left side: Assets
  - Right side: Liabilities + Equity
- **Check**: Green box at bottom = balanced ✓

---

## 💡 Example: Complete Workflow

```
Step 1: Owner invests $10,000
  Dr Bank 10,000 → Cr Owner Capital 10,000 (Post)

Step 2: Earn service revenue $5,000
  Dr Bank 5,000 → Cr Service Revenue 5,000 (Post)

Step 3: Pay rent $2,000
  Dr Rent Expense 2,000 → Cr Bank 2,000 (Post)

Step 4: Check reports:
  ✓ Trial Balance is balanced
  ✓ Bank ledger shows: 10,000 + 5,000 - 2,000 = 13,000
  ✓ P&L shows: Revenue 5,000 - Expense 2,000 = Net Profit 3,000
  ✓ Balance Sheet shows: Assets 13,000 = Equity (10,000) + Earnings (3,000)
```

---

## ⚙️ Useful Commands

```bash
# Development
php artisan serve                              # Start dev server
npm run dev                                    # Watch for CSS/JS changes

# Database
php artisan migrate:fresh --database=sqlite   # Reset database
php artisan db:seed --class=AccountSeeder     # Re-seed accounts
php artisan tinker                            # Interactive shell

# Cache/Config
php artisan config:clear                      # Clear config cache
php artisan route:list                        # Show all routes
php artisan migrate:status                    # Check migration status

# Production
npm run build                                 # Build for production
php artisan config:cache                      # Cache config
php artisan route:cache                       # Cache routes
```

---

## 🐛 Troubleshooting

### Issue: "No such table: accounts"
**Solution**: Run migrations
```bash
php artisan migrate:fresh --database=sqlite
php artisan db:seed --class=AccountSeeder --database=sqlite
```

### Issue: "Class not found"
**Solution**: Regenerate autoloader
```bash
composer dump-autoload
```

### Issue: Assets not loading (CSS/JS missing)
**Solution**: Rebuild assets
```bash
npm install
npm run build
```

### Issue: Connection refused to MySQL
**Solution**: Either:
1. Check MySQL is running, OR
2. Switch to SQLite:
   - Edit .env: `DB_CONNECTION=sqlite`
   - Run: `php artisan migrate:fresh --database=sqlite`

---

## 📁 Key Files to Know

```
routes/web.php                 → All application routes
app/Models/                    → Data models (Account, JournalEntry, etc)
app/Http/Controllers/Accounting/  → Business logic controllers
app/Services/Accounting/       → Core accounting logic
app/Http/Requests/Accounting/  → Form validation rules
resources/views/               → All HTML templates (Blade)
database/migrations/           → Database schema
database/seeders/              → Initial data
```

---

## 🎯 Next Steps

1. ✅ Create a few journal entries to understand flow
2. ✅ Try creating invalid entries to see validation
3. ✅ Run all four reports
4. ✅ Test the reversal feature
5. ✅ Check the code to understand architecture

---

## 📚 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Blade Template Engine](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Bootstrap 5.3 Docs](https://getbootstrap.com/docs/5.3)
- [Accounting Principles](https://en.wikipedia.org/wiki/Double-entry_bookkeeping)

---

**Ready to go!** 🎉
