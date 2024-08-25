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
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->constrained('option_values')->onDelete('cascade');
            $table->foreignId('size_id')->constrained('option_values')->onDelete('cascade');
            $table->primary(['product_id', 'size_id', 'color_id']);
            $table->unsignedBigInteger('quantity');
            // Tạo unique để tránh trùng lặp biến thể
            $table->unique(['product_id', 'color_id', 'size_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};
