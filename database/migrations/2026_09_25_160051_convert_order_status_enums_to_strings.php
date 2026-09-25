<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill any empty/invalid legacy values before loosening the columns.
        DB::table('orders')
            ->whereNull('status')
            ->orWhere('status', '')
            ->orWhereNotIn('status', ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'])
            ->update(['status' => 'pending']);

        DB::table('orders')
            ->whereNull('payment_status')
            ->orWhere('payment_status', '')
            ->orWhereNotIn('payment_status', ['paid', 'unpaid'])
            ->update(['payment_status' => 'unpaid']);

        DB::table('order_payments')
            ->whereNull('payment_method')
            ->orWhere('payment_method', '')
            ->orWhereNotIn('payment_method', ['cod', 'card', 'mobile'])
            ->update(['payment_method' => 'cod']);

        DB::table('order_payments')
            ->whereNull('payment_status')
            ->orWhere('payment_status', '')
            ->orWhereNotIn('payment_status', ['pending', 'success', 'failed'])
            ->update(['payment_status' => 'pending']);

        DB::table('order_shipments')
            ->whereNull('shopment_status')
            ->orWhere('shopment_status', '')
            ->orWhereNotIn('shopment_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
            ->update(['shopment_status' => 'pending']);

        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
            $table->string('payment_status')->default('unpaid')->change();
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->string('payment_method')->default('cod')->change();
            $table->string('payment_status')->default('pending')->change();
        });

        Schema::table('order_shipments', function (Blueprint $table) {
            $table->string('shopment_status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'])->default('pending')->change();
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid')->change();
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cod', 'card', 'mobile'])->default('cod')->change();
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending')->change();
        });

        Schema::table('order_shipments', function (Blueprint $table) {
            $table->enum('shopment_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending')->change();
        });
    }
};
