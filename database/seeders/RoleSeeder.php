<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => RoleName::Admin->value,
                'description' => 'Full system access. Only role allowed to manage users.',
            ],
            [
                'name' => 'Manager',
                'slug' => RoleName::Manager->value,
                'description' => 'Can approve expenses and journal entries, reverse transactions, and view reports.',
            ],
            [
                'name' => 'Accountant',
                'slug' => RoleName::Accountant->value,
                'description' => 'Can create invoices, expenses, journal entries, payments, and templates.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
