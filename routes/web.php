<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionTemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('accounts', AccountController::class)->except(['show', 'destroy']);
    Route::resource('journal-entries', JournalEntryController::class);
    Route::post('journal-entries/{journal_entry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');
    Route::post('journal-entries/{journal_entry}/approve', [JournalEntryController::class, 'approve'])->name('journal-entries.approve');
    Route::post('journal-entries/{journal_entry}/reverse', [JournalEntryController::class, 'reverse'])->name('journal-entries.reverse');

    Route::get('templates', [TransactionTemplateController::class, 'index'])->name('templates.index');
    Route::get('templates/{template}', [TransactionTemplateController::class, 'show'])->name('templates.show');
    Route::get('templates/{template}/apply', [TransactionTemplateController::class, 'applyForm'])->name('templates.apply');
    Route::post('templates/{template}/apply', [TransactionTemplateController::class, 'apply']);

    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');

    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('/income-statement', [ReportController::class, 'incomeStatement'])->name('income-statement');
        Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('cash-flow');
        Route::get('/general-ledger', [ReportController::class, 'generalLedger'])->name('general-ledger');
        Route::get('/journal-register', [ReportController::class, 'journalRegister'])->name('journal-register');
        Route::get('/ar-aging', [ReportController::class, 'arAging'])->name('ar-aging');
        Route::get('/expense-summary', [ReportController::class, 'expenseSummary'])->name('expense-summary');
        Route::get('/revenue-by-dimension', [ReportController::class, 'revenueByDimension'])->name('revenue-by-dimension');
    });

    Route::prefix('calculators')->name('calculators.')->group(function () {
        Route::get('/', [CalculatorController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/mortgage', [CalculatorController::class, 'mortgage'])->name('mortgage');
        Route::match(['get', 'post'], '/rental', [CalculatorController::class, 'rental'])->name('rental');
        Route::match(['get', 'post'], '/commission', [CalculatorController::class, 'commission'])->name('commission');
    });

    Route::resource('clients', ClientController::class)->only(['index', 'create', 'store']);
    Route::resource('agents', AgentController::class)->only(['index', 'create', 'store']);
    Route::resource('properties', PropertyController::class)->only(['index', 'create', 'store']);

    Route::resource('users', UserController::class)->except(['show', 'destroy']);
});
