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
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->integer('category_id');
                $table->string('status');
                $table->string('name');
                $table->string('slug');
                $table->string('seo_title');
                $table->text('seo_description');
                $table->text('seo_keywords');
                $table->string('display_name');
                $table->string('image');
                $table->text('description');
                $table->string('api_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) Schema::dropIfExists('products');
    }
};
