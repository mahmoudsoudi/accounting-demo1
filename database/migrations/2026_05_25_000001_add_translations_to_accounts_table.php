<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->json('name_translations')->nullable()->after('name');
            $table->json('description_translations')->nullable()->after('description');
        });

        DB::table('accounts')->orderBy('id')->each(function ($account) {
            DB::table('accounts')->where('id', $account->id)->update([
                'name_translations' => json_encode(['en' => $account->name]),
                'description_translations' => $account->description
                    ? json_encode(['en' => $account->description])
                    : null,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['name_translations', 'description_translations']);
        });
    }
};
