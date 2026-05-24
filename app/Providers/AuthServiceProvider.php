<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('manage-users', fn (User $user) => $user->canManageUsers());
        Gate::define('approve-transactions', fn (User $user) => $user->canApproveTransactions());
        Gate::define('reverse-transactions', fn (User $user) => $user->canReverseTransactions());
        Gate::define('view-reports', fn (User $user) => $user->canViewReports());
        Gate::define('create-accounting', fn (User $user) => $user->isAdmin() || $user->isAccountant());

        Gate::define('approve-journal-entry', function (User $user, JournalEntry $entry) {
            if (! $user->canApproveTransactions()) {
                return false;
            }

            return $user->isAdmin() || $entry->created_by !== $user->id;
        });

        Gate::define('approve-expense', function (User $user, Expense $expense) {
            if (! $user->canApproveTransactions()) {
                return false;
            }

            return $user->isAdmin() || $expense->created_by !== $user->id;
        });
    }
}
