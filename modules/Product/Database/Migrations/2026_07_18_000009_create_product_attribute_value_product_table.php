<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pivot_product_attr_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_attribute_value_id')->constrained('product_attribute_values')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->unique(['product_id', 'product_attribute_value_id'], 'pavp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pivot_product_attr_value');
    }
};
