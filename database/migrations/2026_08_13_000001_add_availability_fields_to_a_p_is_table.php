<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('a_p_is', function (Blueprint $table) {
            if (! Schema::hasColumn('a_p_is', 'availability_status')) {
                $table->string('availability_status')->nullable()->after('balance');
            }

            if (! Schema::hasColumn('a_p_is', 'availability_score')) {
                $table->unsignedTinyInteger('availability_score')->nullable()->after('availability_status');
            }

            if (! Schema::hasColumn('a_p_is', 'availability_check_transactions_count')) {
                $table->unsignedInteger('availability_check_transactions_count')->nullable()->after('availability_score');
            }

            if (! Schema::hasColumn('a_p_is', 'successful_transactions')) {
                $table->unsignedInteger('successful_transactions')->nullable()->after('availability_check_transactions_count');
            }

            if (! Schema::hasColumn('a_p_is', 'failed_transactions')) {
                $table->unsignedInteger('failed_transactions')->nullable()->after('successful_transactions');
            }

            if (! Schema::hasColumn('a_p_is', 'availability_checked_at')) {
                $table->timestamp('availability_checked_at')->nullable()->after('failed_transactions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('a_p_is', function (Blueprint $table) {
            foreach ([
                'availability_checked_at',
                'failed_transactions',
                'successful_transactions',
                'availability_check_transactions_count',
                'availability_score',
                'availability_status',
            ] as $column) {
                if (Schema::hasColumn('a_p_is', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
