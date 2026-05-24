<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('trigger_action_type');
            $table->json('variables')->nullable();
            $table->json('links_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('transaction_template_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('transaction_templates')->cascadeOnDelete();
            $table->string('account_code', 20);
            $table->string('side');
            $table->string('amount_variable');
            $table->string('description_template')->nullable();
            $table->unsignedSmallInteger('line_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_template_lines');
        Schema::dropIfExists('transaction_templates');
    }
};
