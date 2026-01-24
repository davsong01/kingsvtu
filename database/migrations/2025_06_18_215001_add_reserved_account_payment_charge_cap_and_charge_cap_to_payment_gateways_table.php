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
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->double('reserved_account_payment_charge_cap', 15,2)->nullable();
        });

        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->double('charge_cap', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn('reserved_account_payment_charge_cap');
            $table->dropColumn('charge_cap');
        });
    }
};
