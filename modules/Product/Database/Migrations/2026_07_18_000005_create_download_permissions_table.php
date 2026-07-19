<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_product_id')->constrained('order_products')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_file_id')->constrained('product_files')->cascadeOnDelete();
            $table->string('download_token', 64)->unique();
            $table->unsignedInteger('download_limit')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('first_downloaded_at')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_permissions');
    }
};
