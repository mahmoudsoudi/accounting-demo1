<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Accounting\AccountController;
use App\Http\Controllers\Accounting\JournalEntryController;
use App\Http\Controllers\Accounting\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::resource('accounts', AccountController::class);

        Route::resource('journal-entries', JournalEntryController::class);
        Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])
            ->name('journal-entries.post');
        Route::post('journal-entries/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])
            ->name('journal-entries.reverse');

        Route::get('reports/general-ledger', [ReportController::class, 'generalLedger'])
            ->name('reports.general-ledger');
        Route::get('reports/trial-balance', [ReportController::class, 'trialBalance'])
            ->name('reports.trial-balance');
        Route::get('reports/profit-loss', [ReportController::class, 'profitLoss'])
            ->name('reports.profit-loss');
        Route::get('reports/balance-sheet', [ReportController::class, 'balanceSheet'])
            ->name('reports.balance-sheet');
    });
});
