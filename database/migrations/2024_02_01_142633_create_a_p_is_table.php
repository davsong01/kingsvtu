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
        if (!Schema::hasTable('a_p_is')){
            Schema::create('a_p_is', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->nullable();
                $table->string('api_key')->nullable();
                $table->string('secret_key')->nullable();
                $table->string('public_key')->nullable();
                $table->string('slug')->nullable();
                $table->integer('min')->nullable();
                $table->integer('max')->nullable();
                $table->string('serviceID')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('a_p_is')) {
            Schema::dropIfExists('a_p_is');
        }

    }
};
