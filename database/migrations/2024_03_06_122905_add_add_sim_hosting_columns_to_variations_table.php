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
        if (!Schema::hasColumn('variations', 'ussd_string')) {
            Schema::table('variations', function (Blueprint $table) {
                $table->string('ussd_string')->nullable();
            });
        }

        if (!Schema::hasColumn('variations', 'multistep')) {
            Schema::table('variations', function (Blueprint $table) {
                $table->string('multistep')->default('no');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('variations', 'ussd_string')) {
            Schema::table('variations', function (Blueprint $table) {
                $table->dropColumn("ussd_string");
            });
        }

        if (Schema::hasColumn('variations', 'multistep')) {
            Schema::table('variations', function (Blueprint $table) {
                $table->dropColumn("multistep");
            });
        }
    }
};
