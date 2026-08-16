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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'allow_google_ad')) {
                $table->string('allow_google_ad')->default('no');
            }

            if (!Schema::hasColumn('settings', 'allow_google_dashboard_ad')) {
                $table->string('allow_google_dashboard_ad')->default('no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'allow_google_ad')) {
                $table->dropColumn('allow_google_ad');
            }

            if (Schema::hasColumn('settings', 'allow_google_dashboard_ad')) {
                $table->dropColumn('allow_google_dashboard_ad');
            }
        });
    }
};
