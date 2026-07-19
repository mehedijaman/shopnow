<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_product_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')->constrained('order_products')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained('product_variations')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_product_bundle_items');
    }
};
