<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Commission;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use App\Services\Demo\BrokerageYearSimulator;
use Database\Seeders\ChartOfAccountsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\TransactionTemplateSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDemoYearCommand extends Command
{
    protected $signature = 'demo:seed-year
                            {--year= : Calendar year to simulate (default: previous year)}
                            {--force : Remove existing demo data and regenerate}';

    protected $description = 'Seed a full year of demo brokerage transactions for dashboards and reports';

    public function handle(BrokerageYearSimulator $simulator): int
    {
        if ($this->option('force')) {
            $this->purgeDemoData();
        } elseif (Agent::where('license_number', 'like', 'DEMO-%')->exists()) {
            $this->warn('Demo data already exists. Use --force to regenerate.');

            return self::SUCCESS;
        }

        $this->ensureAccountingDefaults();

        $user = User::where('email', 'admin')->first();

        if (! $user) {
            $this->error('Admin user not found. Run php artisan migrate --seed first.');

            return self::FAILURE;
        }

        $year = (int) ($this->option('year') ?: now()->subYear()->year);

        $this->info("Generating demo brokerage data for {$year}...");

        $stats = $simulator->run($user, $year);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Year', $stats['year']],
                ['Agents', $stats['agents']],
                ['Clients', $stats['clients']],
                ['Properties', $stats['properties']],
                ['Closed deals', $stats['deals_closed']],
                ['Invoices', $stats['invoices']],
                ['Payments', $stats['payments']],
                ['Expenses', $stats['expenses']],
                ['Journal entries', $stats['journal_entries']],
            ],
        );

        $this->info('Done. Log in as admin/admin and check Invoices, Payments, Expenses, and Reports.');

        return self::SUCCESS;
    }

    protected function ensureAccountingDefaults(): void
    {
        Artisan::call('db:seed', [
            '--class' => RoleSeeder::class,
            '--force' => true,
        ]);
        Artisan::call('db:seed', [
            '--class' => AdminUserSeeder::class,
            '--force' => true,
        ]);
        Artisan::call('db:seed', [
            '--class' => ChartOfAccountsSeeder::class,
            '--force' => true,
        ]);
        Artisan::call('db:seed', [
            '--class' => TransactionTemplateSeeder::class,
            '--force' => true,
        ]);
    }

    protected function purgeDemoData(): void
    {
        $this->warn('Removing existing demo data...');

        $demoClientIds = Client::where('email', 'like', 'demo-%')->pluck('id');
        $demoAgentIds = Agent::where('license_number', 'like', 'DEMO-%')->pluck('id');
        $demoPropertyIds = Property::whereIn('client_id', $demoClientIds)->pluck('id');

        Payment::where('payment_number', 'like', 'DEMO-PAY-%')->delete();
        Invoice::where('invoice_number', 'like', 'DEMO-INV-%')->delete();
        Expense::where('expense_number', 'like', 'DEMO-EXP-%')->delete();
        JournalEntry::where('reference_number', 'like', 'DEMO-%')->delete();

        Commission::whereIn('agent_id', $demoAgentIds)->delete();
        Listing::whereIn('property_id', $demoPropertyIds)->delete();
        Property::whereIn('id', $demoPropertyIds)->delete();
        Client::whereIn('id', $demoClientIds)->delete();
        Agent::whereIn('id', $demoAgentIds)->delete();
    }
}
