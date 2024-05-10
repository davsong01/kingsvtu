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
        Schema::create('shop_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('admin_id');
            $table->string('status')->default('pending');
            $table->text('decline_reason');
            $table->json('request_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_requests');
    }
};
