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
        Schema::create('courier_events', function (Blueprint $table) {
            $table->id();
            $table->string('courier');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('tracking_id')->nullable()->index();
            $table->string('status')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_events');
    }
};
