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
        if (!Schema::hasColumn('products', 'min', 'max')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('min')->nullable()->after('name');
                $table->integer('max')->nullable()->after('min');
            });
        }

        if (!Schema::hasColumn('products', 'system_price', 'fixed_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->double('system_price')->nullable()->after('max');
                $table->string('fixed_price')->nullable()->after('system_price');
            });
        }

        if (!Schema::hasColumn('products', 'allow_qantity', 'qantity_graduation')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('allow_quantity')->nullable()->after('system_price');
                $table->string('quantity_graduation')->nullable()->after('allow_quantity');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
