<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amortization_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->decimal('principal', 15, 2);
            $table->decimal('interest_rate', 8, 4);
            $table->unsignedSmallInteger('term_months');
            $table->date('start_date');
            $table->string('payment_frequency')->default('monthly');
            $table->decimal('monthly_payment', 15, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('amortization_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amortization_schedule_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('payment_number');
            $table->date('payment_date');
            $table->decimal('payment_amount', 15, 2);
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('scheduled');
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('sale_price', 15, 2);
            $table->decimal('commission_rate', 8, 4);
            $table->decimal('brokerage_amount', 15, 2);
            $table->decimal('agent_amount', 15, 2);
            $table->string('status')->default('pending');
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('amortization_payments');
        Schema::dropIfExists('amortization_schedules');
    }
};
