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
        if (!Schema::hasTable('variations')) {
            Schema::create('variations', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id');
                $table->integer('category_id');
                $table->integer('api_id');
                $table->string('api_name')->nullable();
                $table->string('status')->default('active');
                $table->string('slug')->unique();
                $table->string('system_name')->nullable();
                $table->string('fixed_price')->nullable();
                $table->integer('api_price')->nullable();
                $table->integer('system_price')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('variations')) {
            Schema::dropIfExists('variations');
        }
    }
};
