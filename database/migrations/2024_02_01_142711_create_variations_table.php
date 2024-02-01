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
                $table->string('api_name');
                $table->string('slug');
                $table->string('system_name');
                $table->integer('fixed_price');
                $table->integer('api_price');
                $table->integer('system_price');
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
