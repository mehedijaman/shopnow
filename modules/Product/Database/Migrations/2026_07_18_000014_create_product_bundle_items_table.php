<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('child_product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('child_product_variation_id')->nullable()->constrained('product_variations')->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->boolean('is_optional')->default(false);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['bundle_product_id', 'child_product_id', 'child_product_variation_id'], 'bundle_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bundle_items');
    }
};
