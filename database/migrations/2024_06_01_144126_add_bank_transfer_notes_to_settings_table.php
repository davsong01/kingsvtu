<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumns('settings', ['bank_transfer_note'])) {
            Schema::table('settings', function (Blueprint $table) {
                $table->text('bank_transfer_note')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns('settings', ['bank_transfer_note'])) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('bank_transfer_note');
            });
        }

    }
};
