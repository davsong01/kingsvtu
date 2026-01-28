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
        Schema::create('provider_webhooks', function (Blueprint $table) {
            $table->id();
            $table->integer('api_id');
            $table->string('reference')->nullable();
            $table->string('status')->default('pending');
            $table->longText('request_payload')->nullable();
            $table->string('type')->default('transaction');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_webhooks');
    }
};
