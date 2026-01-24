<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('transaction_logs', 'external_reference_id')) {
            Schema::table('transaction_logs', function (Blueprint $table) {
                $table->string('external_reference_id')->nullable()->after('reference_id');
            });

            DB::statement("UPDATE transaction_logs SET external_reference_id = reference_id");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            //
        });
    }
};
