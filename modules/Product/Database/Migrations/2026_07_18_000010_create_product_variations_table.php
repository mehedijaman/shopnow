<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->nullable()->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->boolean('active')->default(true);
            $table->string('variation_key');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['product_id', 'variation_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
