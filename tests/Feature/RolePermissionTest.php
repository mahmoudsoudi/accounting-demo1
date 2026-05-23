<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccountingDefaults();
    }

    public function test_roles_are_created(): void
    {
        $this->assertDatabaseCount('roles', 3);
        $this->assertNotNull(Role::where('slug', RoleName::Admin->value)->first());
        $this->assertNotNull(Role::where('slug', RoleName::Manager->value)->first());
        $this->assertNotNull(Role::where('slug', RoleName::Accountant->value)->first());
    }

    public function test_only_admin_can_create_users(): void
    {
        $manager = $this->createUser(RoleName::Manager);
        $accountant = $this->createUser(RoleName::Accountant);
        $admin = User::where('email', 'admin')->first();

        $this->actingAs($manager)->get(route('users.create'))->assertForbidden();
        $this->actingAs($accountant)->get(route('users.create'))->assertForbidden();
        $this->actingAs($admin)->get(route('users.create'))->assertOk();
    }

    public function test_manager_can_approve_expenses(): void
    {
        $manager = $this->createUser(RoleName::Manager);
        $this->assertTrue($manager->canApproveTransactions());
    }

    public function test_accountant_cannot_approve_own_expense(): void
    {
        $accountant = $this->createUser(RoleName::Accountant);
        $expense = \App\Models\Expense::factory()->create(['created_by' => $accountant->id]);

        $this->assertFalse($accountant->can('approve-expense', $expense));
    }

    protected function createUser(RoleName $role): User
    {
        return User::create([
            'name' => ucfirst($role->value).' User',
            'email' => $role->value.'@test.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where('slug', $role->value)->first()->id,
            'is_active' => true,
        ]);
    }
}
