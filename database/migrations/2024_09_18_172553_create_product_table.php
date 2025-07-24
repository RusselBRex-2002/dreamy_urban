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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->string('banner_image')->nullable();
            $table->string('price');
            $table->string('sku');
            $table->string('no_of_qty');
            $table->longText('description');
            $table->string('information')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE')->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
