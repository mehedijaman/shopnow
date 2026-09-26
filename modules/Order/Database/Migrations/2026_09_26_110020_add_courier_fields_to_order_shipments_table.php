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
        Schema::table('order_shipments', function (Blueprint $table) {
            $table->string('consignment_id')->nullable();
            $table->string('courier_status')->nullable();
            $table->timestamp('booked_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('booking_error')->nullable();
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_shipments', function (Blueprint $table) {
            $table->dropIndex(['tracking_number']);
            $table->dropColumn([
                'consignment_id',
                'courier_status',
                'booked_at',
                'last_synced_at',
                'booking_error',
            ]);
        });
    }
};
