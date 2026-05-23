<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', RoleName::Admin->value)->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );
    }
}
