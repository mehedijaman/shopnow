<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_bundle_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete()->unique();
            $table->string('pricing_type', 20)->default('calculated');
            $table->string('discount_type', 20)->default('none');
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('fixed_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bundle_configs');
    }
};
