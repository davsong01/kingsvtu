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
        if (!Schema::hasColumn('products', 'ussd_string')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('ussd_string')->nullable();
            });
        }

        if (!Schema::hasColumn('products', 'multistep')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('multistep')->default('no');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'ussd_string')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn("ussd_string")->nullable();
            }); 
        }

        if (Schema::hasColumn('products', 'multistep')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn("multistep")->nullable();
            });
        }
    }
};
