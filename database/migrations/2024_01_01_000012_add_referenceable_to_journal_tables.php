<?php

use App\Models\AmortizationPayment;
use App\Models\Commission;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->nullableMorphs('referenceable');
        });

        Schema::table('journal_lines', function (Blueprint $table) {
            $table->nullableMorphs('referenceable');
        });

        $this->backfillJournalEntryReferences();
    }

    public function down(): void
    {
        Schema::table('journal_lines', function (Blueprint $table) {
            $table->dropMorphs('referenceable');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropMorphs('referenceable');
        });
    }

    protected function backfillJournalEntryReferences(): void
    {
        $sources = [
            Invoice::class => 'journal_entry_id',
            Expense::class => 'journal_entry_id',
            Payment::class => 'journal_entry_id',
            Commission::class => 'journal_entry_id',
            AmortizationPayment::class => 'journal_entry_id',
        ];

        foreach ($sources as $modelClass => $foreignKey) {
            $modelClass::query()
                ->whereNotNull($foreignKey)
                ->each(function ($source) use ($foreignKey, $modelClass) {
                    JournalEntry::whereKey($source->{$foreignKey})
                        ->whereNull('referenceable_type')
                        ->update([
                            'referenceable_type' => $modelClass,
                            'referenceable_id' => $source->id,
                        ]);
                });
        }
    }
};
