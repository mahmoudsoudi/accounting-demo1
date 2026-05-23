<?php

namespace Tests;

use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ChartOfAccountsSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TransactionTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function seedAccountingDefaults(): void
    {
        $this->seed([
            RoleSeeder::class,
            AdminUserSeeder::class,
            ChartOfAccountsSeeder::class,
            TransactionTemplateSeeder::class,
        ]);
    }
}
