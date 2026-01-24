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
        if (!Schema::hasColumn('customer_levels', 'extra_benefit')) {
            Schema::table('customer_levels', function (Blueprint $table) {
                $table->text('extra_benefit')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('a_p_is', 'balance')) {
            Schema::table('a_p_is', function (Blueprint $table) {
                $table->dropColumn("balance")->nullable();
            }); 
        }
    }
};
