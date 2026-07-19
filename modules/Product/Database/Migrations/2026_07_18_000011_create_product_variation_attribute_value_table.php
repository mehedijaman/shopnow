<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pivot_var_attr_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variation_id')->constrained('product_variations')->cascadeOnDelete();
            $table->foreignId('product_attribute_value_id')->constrained('product_attribute_values')->cascadeOnDelete();

            $table->unique(['product_variation_id', 'product_attribute_value_id'], 'pvav_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pivot_var_attr_value');
    }
};
