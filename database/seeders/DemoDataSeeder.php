<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\User;
use App\Services\Demo\BrokerageYearSimulator;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Agent::where('license_number', 'like', 'DEMO-%')->exists()) {
            $this->command?->warn('Demo data already exists. Skipping. Run `php artisan demo:seed-year --force` to regenerate.');

            return;
        }

        $user = User::where('email', 'admin')->first();

        if (! $user) {
            $this->command?->error('Admin user not found. Run RoleSeeder and AdminUserSeeder first.');

            return;
        }

        $year = (int) (env('DEMO_DATA_YEAR') ?: now()->subYear()->year);

        $this->command?->info("Seeding demo brokerage data for {$year}...");

        $stats = app(BrokerageYearSimulator::class)->run($user, $year);

        $this->command?->info(sprintf(
            'Demo data created: %d agents, %d clients, %d properties, %d closed deals, %d invoices, %d payments, %d expenses, %d journal entries.',
            $stats['agents'],
            $stats['clients'],
            $stats['properties'],
            $stats['deals_closed'],
            $stats['invoices'],
            $stats['payments'],
            $stats['expenses'],
            $stats['journal_entries'],
        ));
    }
}
