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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('product_brands')->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('price');
            $table->string('sale_price')->nullable();
            $table->string('quantity');
            $table->string('unit')->nullable();
            $table->string('min_order')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('featured')->default(false);
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
            $table->string('meta_tag_title', 60)->nullable();
            $table->string('meta_tag_description', 160)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
