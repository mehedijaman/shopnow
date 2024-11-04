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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->timestamp('order_date');

            $table->enum('status', [
                'pending',
                'processing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('shipping_fee', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);

            $table->foreignId('shipping_address')->nullable()->constrained('customer_addresses')->nullOnDelete();
            $table->foreignId('billing_address')->nullable()->constrained('customer_addresses')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
