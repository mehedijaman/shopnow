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
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->foreignId('division_id')->nullable();
            $table->foreignId('district_id')->nullable();
            $table->foreignId('upazilla_id')->nullable();
            $table->foreignId('union_id')->nullable();
            $table->string('address');
            $table->string('country')->nullable();

            // Order Status
            $table->enum('status', [
                'pending',
                'processing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
            ])->default('pending');

            // Financial details
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('shipping', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->decimal('paid', 10, 2)->default(0.00);
            $table->decimal('due', 10, 2)->default(0.00);
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->string('payment_method');

            // Additional notes
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
